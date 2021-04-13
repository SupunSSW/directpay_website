<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use masterCardService;

class check3dsController extends Controller
{
    public function check3ds(Request $request)
    {
        $req3DsId = masterCardService::getUniqeId("3DS");
        $amount = $request->input('amount');

        $data = [
            'apiOperation' => "CHECK_3DS_ENROLLMENT",
            'session' => [
                'id' => $request->input('sessionid')

            ],
            '3DSecure' => [
                'authenticationRedirect' => [
                    //"responseUrl" => (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/aia/public/api/check3dRedirect?reqId=$req3DsId"
                    "responseUrl" => (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/api/check3dRedirect?reqId=$req3DsId"
                ]
            ],
            'order' => [
                'amount' => $amount,
                'currency' => 'LKR'
            ]
        ];

        $res = masterCardService::sendRequest(env('MASTER_CARD_3DS_URL').'/'.$req3DsId, 'PUT', $data);

        if (! isset($res['error']) ){
            $cardEnrollStatus = $res['response']['3DSecure']['gatewayCode'];

            Log::info("check3ds-" . Carbon::now());
            Log::info($res);
            Log::info("gatewayCode ".$cardEnrollStatus);
            Log::info("check3ds-" . Carbon::now());

            if($cardEnrollStatus == 'CARD_ENROLLED'){

                return response()->json([
                    'status' => 200,
                    'data' => [
                        'html' => $res['3DSecure']['authenticationRedirect']['simple']['htmlBodyContent'],
                        '3DSecureId' => $res['3DSecureId']
                    ]
                ]);

            }else{
                return response()->json([
                    'status' => 400,
                    'data' => [
                        'status' => $cardEnrollStatus
                    ]
                ]);
            }
        }else{

            Log::info("check3ds-" . Carbon::now());
            Log::info($res);
            Log::info("check3ds-" . Carbon::now());

            return response()->json([
                'status' => 400,
                'data' => [
                    'status' => ''
                ]
            ]);
        }
    }


    public function check3dRedirect(Request $request)
    {
        Log::info("check3dRedirect-" . Carbon::now());
        Log::info($request->all());
        Log::info("check3dRedirect-" . Carbon::now());

        $paRes = $request->input('PaRes');
        $reqId = $request->input('reqId');

        $data = [
            'apiOperation' => "PROCESS_ACS_RESULT",
            '3DSecure' => [
                'paRes' => $paRes
            ]
        ];

        $res = masterCardService::sendRequest(env('MASTER_CARD_3DS_URL').'/'.$reqId, 'POST', $data);

        Log::info($res);

        $summery = $res['3DSecure']['summaryStatus'];
        $id3Ds = $res['3DSecureId'];

        Log::info($summery);
        Log::info($id3Ds);
        Log::info('gatewaysdk://3dsecure?summaryStatus='.$summery.'&3DSecureId='.$id3Ds);

        return redirect()->away('gatewaysdk://3dsecure?acsResult='.urlencode(json_encode($res)));
    }
}
