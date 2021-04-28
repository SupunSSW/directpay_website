<?php

namespace App\Http\Controllers;

use App\accVerifications;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class accVerification extends Controller
{

    function verifyAcc(Request $request)
    {
//            $accDetails= new accDetails;
//            $accDetails->merchantId=$req->mId;
//            $accDetails->billerCode=$req->billerCode;
//            $accDetails->accountNumber=$req->accountnumber;


//            echo '$accDetails';


        \Log::info(json_encode($request->all()));

        $accountNumber = $request->input('_description');
        $amount = $request->input('_amount');
        $remarks = $request->input('_orderId');


        \Log::info($accountNumber);
        \Log::info($amount);
        \Log::info($remarks);


        $requestData = '{
    "accountNumber": "' . $accountNumber . '",
    "billerCode": "MOB_PO",
    "merchantId": "fffb36a1-d41a-4786-be52-866edcc35f03"
}';

        $hash = hash_hmac('sha256', $requestData, '27e2daa07c0630b8fbe22357ef84ad61d3f060548170274fff1a4e814fb63f9e');


        //return $hash;
        //echo $hash;

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
            CURLOPT_POSTFIELDS => $requestData,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer' . $hash,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);


        curl_close($curl);


        return;


    }


}
