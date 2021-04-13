<?php

namespace App\Http\Controllers\Api;

use App\Models\CardTokenModel;
use App\Models\insurence;
use App\Models\InsurenceModel;
use App\Models\SchedulModel;
use App\Models\TransactionModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use masterCardService;

class TokenizationController extends Controller
{
    public function createToken(Request $request)
    {
        $session = $request->input('sessionid');
        $amount = $request->input('amount');
        $startingDate = $request->input('startingDate');
        $interval = $request->input('interval');
        $policeId = $request->input('policyId');
        $user = auth()->user();

        $data = [
            'session' => [
                'id' => $session
            ],
            'sourceOfFunds' => [
                'type'=> "CARD"
            ]
        ];

        $res = masterCardService::sendRequest(env('MASTER_CARD_TOKEN_URL'),"POST",$data);
        Log::info("createToken-".Carbon::now());
        Log::info($res);
        Log::info("createToken-".Carbon::now());

        if($res['result'] == 'SUCCESS')
        {
            //tokenize
            $cardToken = new CardTokenModel;
            $cardToken->userId = $user->email;
            $cardToken->token = $res['token'];
            $cardToken->card_mask = $res['sourceOfFunds']['provided']['card']['number'];
            $cardToken->card_name = $res['sourceOfFunds']['provided']['card']['brand'];
            $cardToken->expiry = $res['sourceOfFunds']['provided']['card']['expiry'];
            $cardToken->trn_amount = 0;
            $cardToken->is_default = 1;
            $cardToken->is_active = 1;
            $cardToken->create_date = Carbon::now();
            $cardToken->save();

            //schedule
            $schedule = new SchedulModel;
            $schedule->payerId = $user->email;
            $schedule->payeeId = '9b244906-3019-416e-ac27-e1666653a764';
            $schedule->payingAccountRef  = $cardToken->id;
            $schedule->payingInterval = $interval;
            $schedule->amount = $amount;
            $schedule->nextPaymentDate = $startingDate;
            $schedule->startingtDate = $startingDate;
            $schedule->payUsing = 'CARD';
            $schedule->state = 2;
            $schedule->latestPaymentDate = Carbon::now();
            $schedule->retryAttempts = 3;
            $schedule->isRetry = 1;
            $schedule->doFirstPayment = 1;
            $schedule->save();

            //payment
            $trnId = TransactionModel::insertGetId([
                'payerAccountNumber' => 'AIA:CARDPAYMENT',
                'payeeId' => '9b244906-3019-416e-ac27-e1666653a764',
                'payerId' => $user->email,
                'payeeUserName' => 'AIA Insurance',
                'payeeName' => 'AIA Insurance',
                'payerBank' => 'CARD',
                'dateTime' => Carbon::now(),
                'status' => 'PENDING',
                'payingAmount' => $amount,
                'receivingAmount' => $amount,
                'originalAmount' => $amount,
                'type' => 'CardTransaction',
                'paymentCategory' => 'IPG:checkout',
                'currency' => 'LKR',
                'payUsing' => 'CARD',
                'initializedBy' => 'MASTERCARD_API',
                'reference' => $policeId

            ]);
            $doPayment = $this->doPayment($res['token'], $amount);
            $paymentStatus = $doPayment['result'];
            if ($paymentStatus == 'SUCCESS'){
                $trnData = TransactionModel::find($trnId);
                $trnData->status = 'SUCCESS';
                $trnData->castomParam = $doPayment['order']['id'].':'.$doPayment['transaction']['id'];
                $trnData->bankerTransId = $doPayment['transaction']['id'];
                $trnData->bankerResponseDesc = $doPayment['response']['acquirerMessage'];
                $trnData->createdAt = Carbon::now();
                $trnData->save();
            }else{
                $trnData = TransactionModel::find($trnId);
                $trnData->status = 'FAILED';
                $trnData->castomParam = $doPayment['order']['id'].':'.$doPayment['transaction']['id'];
                $trnData->bankerTransId = $doPayment['transaction']['id'];
                $trnData->bankerResponseDesc = $doPayment['response']['acquirerMessage'];
                $trnData->createdAt = Carbon::now();
                $trnData->save();
            }

            $cardExpDetails = $doPayment['sourceOfFunds']['provided']['card']['expiry'];
            $schData = SchedulModel::find($schedule->id);
            $schData->lastPaymentDate =  '20'.$cardExpDetails['year'].'-'.$cardExpDetails['month'].'-01 00:00:00';
            $schData->lastPaymentStatus = $paymentStatus;
            $schData->lastPaymentStatusDescription = $paymentStatus;
            $schData->lastPaymentStatusDescription = $paymentStatus;
            $schData->param1 = $cardToken->id.":".$schedule->id.":".$trnData->id;
            $schData->save();

            $insurence = new insurence;
            $insurence->agent_id = $user->id;
            $insurence->police_no = $policeId;
            $insurence->amount = $amount;
            $insurence->card_no = $res['sourceOfFunds']['provided']['card']['number'];
            $insurence->card_exp = $cardExpDetails['month'].'/'.$cardExpDetails['year'];
            $insurence->interval = $interval;
            $insurence->next_payment_date = $startingDate;
            $insurence->next_payment_amount = $amount;
            $insurence->card_res = $doPayment['response']['acquirerMessage'];
            $insurence->aia_res = '';
            $insurence->trn_status = $paymentStatus;
            $insurence->schId = $schedule->id;
            $insurence->status = 'active';
            $insurence->created_at = Carbon::now();
            $insurence->updated_at = Carbon::now();
            $insurence->save();

            logActivity($user->email, 'insurences', $user->email.' Create new Insurance Card: '.$res['sourceOfFunds']['provided']['card']['number'].' RefId: '.$insurence->id.' TrnStatus: '.$paymentStatus.' TrnId: '.$doPayment['transaction']['id']
            );

            return response()->json([
                'status' => 200,
                'data' => [
                    'status' => $paymentStatus,
                    'trnId' => $trnId
                ]
            ]);

        }else{

            return response()->json([
                'status' => 400,
                'data' => [
                    'status' => 'FAILURE',
                    'message' => 'Tokenize Failed'
                ]
            ]);

        }
    }

    private function doPayment($token, $amount)
    {
        $orderId = masterCardService::getUniqeId('ORDER');
        $transactionId = masterCardService::getUniqeId('TRANS');

        $data = [
            'apiOperation' => 'PAY',
            'order' => [
                'currency' => 'LKR',
                'amount' => $amount
            ],
            'sourceOfFunds' => [
                'token'=> $token
            ]
        ];

        $res = masterCardService::sendRequest(
            env('MASTER_CARD_PAYMENT_URL').'/'.$orderId.'/transaction/'.$transactionId,
            "PUT", $data);

        Log::info("Payment-".Carbon::now());
        Log::info($res);
        Log::info("Payment-".Carbon::now());

        return $res;
    }

    public function getToken(Request $request)
    {
        $token = $request->input('token');

        return masterCardService::sendRequest(env('MASTER_CARD_TOKEN_URL').'/'.$token,"GET",'');
    }

    public function deleteToken(Request $request)
    {
        $token = $request->input('token');
        return masterCardService::sendRequest(env('MASTER_CARD_TOKEN_URL').'/'.$token,"DELETE",'');
    }
}
