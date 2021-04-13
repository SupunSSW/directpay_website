<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Support\Facades\Http;

class TransactionController extends Controller
{
    const LOCALHOST = 'http://127.0.0.1';
    const ROUTE_CASH_PAYMENT = '/internal/cashTransactionRequest';
    const ROUTE_PAYMENT_LINK = '/v1/mpg/api/external/generateLink';
    const ROUTE_LANKA_QR = '/internal/qr/get';

    public function processQR(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'data' => [
                    'title' => 'Missing required field!',
                    'code' => 'ValidationException',
                    'message' => $validator->errors()->first()
                ]
            ]);
        }

        $user = auth()->user();
        $content = $request->input('content');

        logActivity($user->email, 'Transactions', 'content: ' . $content);

        $values = explode(", ", $content);

        $trackingNo = $values[0];

        if (!empty($trackingNo)) {
            if (isSuccessTransactionExists($trackingNo)) {
                logActivity($user->email, 'Transactions', 'attempt: tracking no:' . $trackingNo . ' , Agent: ' . $user->email);

                return response()->json([
                    'status' => 400,
                    'data' => [
                        'title' => 'Already Paid',
                        'code' => 'AlreadyPaidException',
                        'message' => "Payment already received for this Tracking No. $trackingNo"
                    ]
                ]);
            }
        }

        $name = sizeof($values) >= 4 ? $values[3] : "";
        $address = "";
        $mobile = "";
        $amount = sizeof($values) >= 3 ? $values[2] : "";

        logActivity($user->email, 'Transactions', ' processed content - name: ' . $name . ' address: ' . $address . ' mobile: ' . $mobile . ' amount: ' . $amount);

        try {
            return response()->json([
                'status' => 200,
                'data' => [
                    'tracking_no' => $trackingNo,
                    'name' => $name,
                    'address' => $address,
                    'mobile' => $mobile,
                    'amount' => $amount,
                ]
            ]);
        } catch (\Exception $exception) {
        }

        return response()->json([
            'status' => 400,
            'data' => [
                'code' => "UnableToProcess",
                'title' => $name,
                'message' => "Cannot process QR!"
            ]
        ]);
    }

    public function complete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tracking_no' => 'required',
            'mobile' => 'required|digits:10',
            'amount' => 'required|min:1|max:8',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'data' => [
                    'title' => 'Missing required field!',
                    'code' => 'ValidationException',
                    'message' => $validator->errors()->first()
                ]
            ]);
        }

        $user = auth()->user();
        $tracking_no = $request->get('tracking_no');
        $type = $request->get('type');
        $amount = $request->get('amount');
        $mobile = $request->get('mobile');

        if (!empty($amount)) {
            $amount = str_replace(',', '', $amount);
        }

        if (isSuccessTransactionExists($tracking_no)) {
            logActivity($user->email, 'Transactions', 'attempt: tracking no:' . $tracking_no . ' , Agent: ' . $user->email . ' , Type: ' . $type . ', Amount: ' . $amount);

            return response()->json([
                'status' => 400,
                'data' => [
                    'title' => 'Already Paid',
                    'code' => 'AlreadyPaidException',
                    'message' => "Payment already received for this Tracking No. $tracking_no"
                ]
            ]);
        }

        try {
            //saving pending transaction record
            $transaction = new Transaction();
            $transaction->tracking_no = $tracking_no;
            $transaction->name = $request->get('name');
            $transaction->address = $request->get('address');
            $transaction->mobile = $request->get('mobile');
            $transaction->amount = $amount;
            $transaction->type = $type;
            $transaction->agent_id = $user->id;
            $transaction->agent_email = $user->email;
            $transaction->status = Transaction::STATUS_PENDING;
            $transaction->save();

            logActivity($user->email, 'Transactions', 'complete: Pending transaction saved ' . $transaction->id . ' , Agent: ' . $transaction->agent_email . ' , Tracking No: ' . $transaction->tracking_no . ' , Type: ' . $type);

            $response_transaction_data = [
                'tracking_no' => $tracking_no,
                'type' => $type,
                'amount' => $amount,
                'status' => Transaction::STATUS_PENDING
            ];

            if ($type == Transaction::TYPE_CASH) {
                $response = $this->doCashTransaction($tracking_no, $transaction->id, $amount);
                if ($response != null) {
                    if ($response->status == 200) {
                        $response_transaction_data["status"] = Transaction::STATUS_SUCCESS;
                        $tran_id = $response->data->transactionId;

                        $this->updateTransaction($transaction, $tran_id, "CASH", Transaction::STATUS_SUCCESS);
                        return $this->processSuccessResponse($response_transaction_data);
                    }
                }
            } else if ($type == Transaction::TYPE_QR) {
                $response = $this->getLankaQR($transaction->id, $amount);
                if ($response != null) {
                    if ($response->status == 200) {
                        return $this->processSuccessResponse($response_transaction_data, "Please scan the QR to proceed the payment.", [
                            "qr" => $response->data->qrData
                        ]);
                    }
                }
            } else if ($type == Transaction::TYPE_LINK) {
                $response = $this->sendPaymentLink($tracking_no, $transaction->id, $amount, $mobile);
                if ($response->status == 200) {
                    if ($response->data->smsStatus[0] == "Success") {
                        return $this->processSuccessResponse($response_transaction_data, "Successfully sent link to $mobile");
                    }
                }
            }

            $transaction->status = Transaction::STATUS_FAILED;
            $transaction->save();

            return response()->json([
                'status' => 400,
                'data' => [
                    'title' => 'Failed to proceed',
                    'code' => 'UnknownException',
                    'message' => 'Error while processing. Please try again in a moment!',
                    'transaction' => $response_transaction_data
                ]
            ]);
        } catch (\Exception $exception) {
            \Log::error('USER[' . $user->email . ']  - ' . $exception);
            return response()->json([
                'status' => 400,
                'data' => [
                    'title' => 'Failed to proceed',
                    'code' => 'UnknownException',
                    'message' => 'Error while processing. Please try again in a moment!'
                ]
            ]);
        }
    }

    public function history(Request $request)
    {
        $query = Transaction::query();

        $query->whereIn('status', [
            Transaction::STATUS_SUCCESS,
            Transaction::STATUS_PENDING,
            Transaction::STATUS_FAILED,
        ]);

        $query->where('agent_id', auth()->user()->id);

        if ($request->has('date')) {
            $from = date($request->json('date.from') . " 00:00:00");
            $to = date($request->json('date.to') . " 23:59:59");

            \Log::info('DATE - ' . $from . $to);

            $query->whereBetween('created_at', [$from, $to]);
        }

        if ($request->has('tracking_no')) {
            $tracking_no = $request->get('tracking_no');
            $query->where('tracking_no', $tracking_no);
        }

        $query->orderBy('created_at', 'desc');

        if ($request->has('limit')) {
            $limit = $request->get('limit');
            $query->limit($limit);
        }

        $transactions = $query->get();
        return response()->json([
            'status' => 200,
            'data' => [
                "transactions" => $transactions
            ]
        ]);
    }

    private function updateTransaction(Transaction $transaction, $id, $reference, $status)
    {
        $transaction->transaction_id = $id;
        $transaction->transaction_reference = $reference;
        $transaction->status = $status;

        $transaction->save();
    }

    private function processSuccessResponse($response_transaction_data, $message = 'Successfully created a transaction', $params = [])
    {
        $data = [
            'title' => 'Success',
            'code' => 'Success',
            'message' => $message,
            'transaction' => $response_transaction_data
        ];

        $data = array_merge($data, $params);

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    private function doCashTransaction($reference, $orderId, $amount)
    {
        $merchantId = env('PAYEE_ID');
        $json_body = [
            "amount" => $amount,
            "orderId" => $orderId,
            "reference" => $reference,
            "merchantId" => $merchantId,
        ];

        try {
            $client = new Client(['verify' => false]);
            $result = $client->post(self::LOCALHOST . self::ROUTE_CASH_PAYMENT, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => $json_body
            ]);
            $result = $result->getBody()->getContents();
            \Log::info($result);
            return json_decode($result);
        } catch (\Exception $ex) {
            \Log::info($ex->getMessage());
        }

        return null;
    }

    private function sortJSON($array)
    {
        $dataString = "";
        $sortedKeys = array_sort(array_keys($array));

        foreach ($sortedKeys as $value) {
            $dataString .= is_array($array[$value]) ? $this->sortJSON($array[$value]) : $array[$value];
        }

        return $dataString;
    }

    private function generateSignature($dataString)
    {
        try {
//            $dataString = $this->sortJSON($json_body);

            \Log::info("data string: " . $dataString);

            $filepath = storage_path('keys/private_key_' . env('PAYEE_ID') . '.pem');
            \Log::info('file path: ' . $filepath);

            $pkeyid = openssl_pkey_get_private('file://' . $filepath);
            $signResult = openssl_sign($dataString, $signature, $pkeyid, OPENSSL_ALGO_SHA256);
            $signature = base64_encode($signature);

            openssl_free_key($pkeyid);
            return $signature;

        } catch (\Exception $exception) {
            \Log::error($exception);
        }
        return null;
    }

    private function getLankaQR($reference, $amount)
    {
        $merchantId = env('PAYEE_ID');

        $json_body = [
            "mid" => $merchantId,
            "amount" => $amount,
            "reference" => $reference
        ];

        try {
            $client = new Client(['verify' => false]);
            $result = $client->post(self::LOCALHOST . self::ROUTE_LANKA_QR, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => $json_body
            ]);
            $result = $result->getBody()->getContents();
            \Log::info($result);
            return json_decode($result);
        } catch (\Exception $ex) {
            \Log::info($ex->getMessage());
        }
    }

    private function sendPaymentLink($name, $orderId, $amount, $mobile)
    {
        $merchantId = env('DP_MERCHNAT_ID');
        $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $sms_content = "Make payment for $name";
        $notification_url = "$url/api/transaction/confirm/link";
        $currency = "LKR";
        $mobile_plus_cc = "+94" . substr($mobile, 1, strlen($mobile) - 1);

        $json_body = [
            "amount" => $amount,
            "currency" => $currency,
            "mId" => $merchantId,
            "name" => $name,
            "notification_url" => $notification_url,
            "reference" => $orderId,
            "send_sms" => [
                "contact_no" => $mobile_plus_cc,
                "sms_content" => $sms_content
            ],
            "type" => 1,
        ];

        $signature = $this->generateSignature($amount . $mobile_plus_cc . $currency . $merchantId . $name . $notification_url . $orderId . $sms_content . 1);

        \Log::info('signature: ' . $signature);
        try {
            $client = new Client(['verify' => false]);
            $result = $client->post(env('DIRECTPAY_URL', 'https://dev.directpay.lk') . self::ROUTE_PAYMENT_LINK, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Signature' => $signature
                ],
                'json' => $json_body
            ]);
            $result = $result->getBody()->getContents();
            \Log::info($result);
            return json_decode($result);
        } catch (\Exception $ex) {
            \Log::info($ex->getMessage());
        }

        return null;
    }
}
