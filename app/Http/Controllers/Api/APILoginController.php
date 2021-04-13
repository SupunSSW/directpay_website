<?php

namespace App\Http\Controllers\Api;

use App\CardToken;
use App\Devices;
use App\Models\Auth\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JsonSchema\Uri\Retrievers\Curl;
use Matrix\Exception;
use Validator;
use JWTFactory;
use JWTAuth;

class APILoginController extends Controller
{
    public function login(Request $request)
    {
//        $credentials = [
//            'email' => 'admin@admin.com',
//            'password' => 'secret'
//        ];
//        try {
//            if (!$token = JWTAuth::attempt($credentials)) {
//                //logActivity($request->input('email'), 'users', $request->input('email') . ' Login Failed -Invalid Credentials. App:'. $platform.'-'.$version );
//                return response()->json([
//                    'status' => 400,
//                    'data' => [
//                        'code' => 'ValidationException',
//                        'message' => 'Invalid Credentials'
//                    ]
//                ]);
//            }
//        } catch (JWTException $e) {
//            //logActivity($request->input('email'), 'users', $request->input('email') . ' Login Failed. App:'. $platform.'-'.$version);
//            return response()->json([
//                'status' => 400,
//                'data' => [
//                    'code' => 'ValidationException',
//                    'message' => 'Could Not Create Token'
//                ]
//            ]);
//        }
//
//        //logActivity($request->input('email'), 'users', $request->input('email') . ' Login Success. App:'. $platform.'-'.$version);
//        return response()->json([
//            'status' => 200,
//            'data' => [
//                'access_token' => $token,
//                'token_type' => 'bearer',
//                'expires_in' => 3600
//            ]
//        ]);
//
        //\Log::info($request->all());
        $validator = Validator::make($request->all(), [
            'email' => 'required|max:20',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'data' => [
                    'title' => 'Missing required parameters.',
                    'code' => 'ValidationException',
                    'message' => $validator->errors()->first()
                ]
            ]);
        }

        $platform = $request->input('platform');
        $version = $request->input('version');
        $user = $request->input('email');

//        $aiaLoginRes = $this->aiaLogin($request->input('email'), $request->input('password'));
//        $deviceStatus = $this->devicesValidation($user, $deviceId, $platform, $version);

        if (true) {
            if (true) {
//                $this->loginCountUpdate($user, $deviceId, $aiaLoginRes['status']);
                //$credentials = $request->only('email', 'password');
                $credentials = [
                    'email' => $request->input('email'),
                    'password' => $request->input('password')
                ];
                try {
                    if (!$token = JWTAuth::attempt($credentials)) {
                        logActivity($request->input('email'), 'users', $user . ' Login Failed -Invalid Credentials. App:' . $platform . '-' . $version);
                        return response()->json([
                            'status' => 400,
                            'data' => [
                                'title' => 'Invalid Credentials!',
                                'code' => 'ValidationException',
                                'message' => 'Username or password is incorrect.'
                            ]
                        ]);
                    }
                } catch (JWTException $e) {
                    logActivity($request->input('email'), 'users', $user . ' Login Failed. App:' . $platform . '-' . $version);
                    return response()->json([
                        'status' => 400,
                        'data' => [
                            'title' => 'Invalid Credentials!',
                            'code' => 'ValidationException',
                            'message' => 'Could Not Create Token'
                        ]
                    ]);
                }

                if ($request->has('firebase_token')) {
                    if (!empty($request->get('firebase_token'))) {
                        $userObject = User::query()->where('email', $user)->first();
                        if ($userObject) {
                            $userObject->firebase_token = $request->get('firebase_token');
                            $userObject->save();
                        }
                    } else {
                        logActivity($request->input('email'), 'warning', $user . ' Firebase Token not found!. App:' . $platform . '-' . $version);
                    }
                } else {
                    logActivity($request->input('email'), 'warning', $user . ' Firebase Token not found!. App:' . $platform . '-' . $version);
                }

                logActivity($request->input('email'), 'users', $user . ' Login Success. App:' . $platform . '-' . $version);

                return response()->json([
                    'status' => 200,
                    'data' => [
                        'access_token' => $token,
                        'token_type' => 'bearer',
                        'expires_in' => 3600
                    ]
                ]);
            } else {
//                $this->loginCountUpdate($user, $deviceId, $aiaLoginRes['status']);
                logActivity($request->input('email'), 'users', $user . ' App:' . $platform . '-' . $version);
                return response()->json([
                    'status' => 400,
                    'data' => [
                        'title' => 'Invalid Credentials!',
                        'code' => 'ValidationException',
//                        'message' => $aiaLoginRes['message']
                    ]
                ]);
            }
        } else {
            logActivity($request->input('email'), 'users', 'login failed - Invalid User device: Platform: ' . $platform . ' App:' . $platform . '-' . $version);
            return response()->json([
                'status' => 400,
                'data' => [
                    'code' => 'ValidationException',
                    'message' => 'User already registered with another device please contact the administrator!!'
                ]
            ]);
        }
    }

    public function refresh(Request $request)
    {
        try {
            $new_token = JWTAuth::refresh($request->input('token'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json([
            "status" => 200,
            "data" => [
                "access_token" => $new_token,
                "token_type" => "bearer",
                "expires_in" => 3600
            ]
        ]);
    }

    public function aiaLogin($username, $password)
    {
        \Log::info(env('AIA_AUTH_URL'));
        try {
            $client = new Client();
            $response = $client->post(env('AIA_AUTH_URL'), [
                'form_params' => [
                    'loginid' => $username,
                    'password' => $password
                ],
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => '*/*'
                ],
                'auth' => [env('AIA_AUTH_USER'), env('AIA_AUTH_PASSWORD')]
            ]);

            return $this->aiaDecrypt([
                'status' => $response->getStatusCode(),
                'data' => $response->getBody()->getContents(),
                'username' => $username
            ]);
        } catch (\Exception $exception) {
            \Log::info('exception: ' . $exception->getMessage());
            return $this->aiaDecrypt([
                'status' => 500,
            ]);
        }

    }

    public function aiaDecrypt($response)
    {
        \Log::info($response);
        date_default_timezone_set("Asia/Colombo");
        $date = date("Y-m-d");
        \Log::info($date);
        if ($response['status'] == 200) {
            try {
                $username = $response['username'];
                $data = $response['data'];
                $method = "AES-128-CBC";

                $iv = substr($data, 0, 16);
                $data = substr($data, 16);
                $key = $username . $date . 'AIA';
                $keyHash = hash('sha256', $key);
                $key16 = substr($keyHash, 0, 16);

                \Log::info("iv " . $iv);
                \Log::info("data " . $data);
                \Log::info("key " . $key);
                \Log::info("keyHash " . $keyHash);
                \Log::info("key16 " . $key16);

                $desc = openssl_decrypt(base64_decode($data), $method, $key16, 7, $iv);
                \Log::info([json_decode(preg_replace('/[\x00-\x1F\x7F]/u', '', stripslashes($desc)))]);
                $resData = json_decode(preg_replace('/[\x00-\x1F\x7F]/u', '', stripslashes($desc)));
                if ($resData->statusCode == 200) {
                    return [
                        'status' => true
                    ];
                } else {
                    return [
                        'status' => false,
                        'message' => $resData->statusDescription
                    ];
                }
            } catch (\Exception $exception) {
                \Log::info('exception: ' . $exception->getMessage());
                return [
                    'status' => false,
                    'message' => $exception->getMessage()
                ];
            }
        } else {
            return [
                'status' => false,
                'message' => 'Server error.'
            ];
        }
    }

    function loginCountUpdate($userId, $deviceId, $loginStatus)
    {
        $deviceDetails = Devices::where('user_id', $userId)
            ->where('device_id', $deviceId)
            ->first();
        if ($loginStatus) {
            $deviceDetails->successLoginCount = $deviceDetails->successLoginCount + 1;
        } else {
            $deviceDetails->failedLoginCount = $deviceDetails->failedLoginCount + 1;
        }
        $deviceDetails->updated_at = Carbon::now();
        $deviceDetails->save();
    }

    function devicesValidation($userId, $deviceId, $platform, $version)
    {
        $deviceDetails = Devices::where('user_id', $userId)->first();
        if ($deviceDetails) {
            if ($deviceDetails->device_id == $deviceId) {
                return '200';
            } else {
                return '400';
            }
        } else {
            $deviceDetails = new Devices();
            $deviceDetails->device_id = $deviceId;
            $deviceDetails->platform = $platform;
            $deviceDetails->user_id = $userId;
            $deviceDetails->version = $version;
            $deviceDetails->save();
            return '200';
        }
    }
}
