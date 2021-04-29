<?php

namespace App\Http\Controllers;

use App\accVerifications;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class accVerification extends Controller
{

    function verifyAcc(Request $request)
    {
        \Log::info(json_encode($request->all()));

        $validator = \Validator::make($request->all(), [
            'accountNo' => 'required',
            'amount' => 'required'
        ]);

        if ($validator->fails()) {

            $errorData = new \stdClass();
            $errorData->title = 'Validation Exception';
            $errorData->message = $validator->errors()->first();

            return response()->json([
                'status' => 401,
                'data' => $errorData
            ]);
        }


        // TODO: Get biller category for merhcant using API call - currently har coded as '1'
        $bllerCategoryId = 1; // Mobile

        // TODO: Get billers for merchant, according to biller category
        $billerCode = 'ETIS014814'; // Etisalat

        // TODO: validate mobile number
        $merchantId = env('MERCHANT_USER_ID');
        $accountNumber = $request->input('accountNo');
        $amount = $request->input('amount');
        $remarks = $request->input('remarks');


        $requestData = [
            "accountNumber" => $accountNumber,
            "billerCode" => $billerCode,
            "merchantId" => $merchantId
        ];

        \Log::info('requestData ' . json_encode($requestData));

        $dataString = $accountNumber.$billerCode.$merchantId;
        $hash = hash_hmac('sha256', $dataString, env('MERCHANT_SECRET'));

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://dev.directpay.lk/v2/backend/external/api/validateAccountAction',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($requestData),
            CURLOPT_HTTPHEADER => array(
                'Authorization: hmac ' . $hash,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        \Log::info('RESPONSE: ' . $response);

        $responseObject = json_decode($response);

        return response()->json([
            'status' => $responseObject->status,
            'data' => $responseObject->data
        ]);

    }


}
