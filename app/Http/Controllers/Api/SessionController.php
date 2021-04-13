<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use masterCardService;

class SessionController extends Controller
{
    public function createSession()
    {
        $data = [
            'correlationId' => masterCardService::getUniqeId("SESSION"),
//            'session' => [
//                'authenticationLimit' => 5
//            ]
        ];

        $res = masterCardService::sendRequest(env('MASTER_CARD_SESSION_URL'), 'POST', $data);

        Log::info("CreateSession-".Carbon::now());
        Log::info($res);
        Log::info("CreateSession-".Carbon::now());

        if($res['result'] == 'SUCCESS')
        {
            return response()->json([
                'status' => 200,
                'data' => [
                    'sessionId' => $res['session']['id']
                ]
            ]);
        }else{
            return response()->json([
                'status' => 200,
                'data' => [
                    'sessionId' => 'ValidationException'
                ]
            ]);
        }
    }

    public function getSession(Request $request)
    {
        $sessionID = $request->input('sessionid');
        $url = env('MASTER_CARD_SESSION_URL') . '/' . $sessionID;

        return masterCardService::sendRequest($url, 'GET', '');
    }
}
