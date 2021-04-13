<?php

use App\Helpers\General\Timezone;
use App\Helpers\General\HtmlHelper;
use App\Models\Activity;
use App\Models\Transaction;
use Carbon\Carbon;
use GuzzleHttp\Client;

/*
 * Global helpers file with misc functions.
 */
if (!function_exists('app_name')) {
    /**
     * Helper to grab the application name.
     *
     * @return mixed
     */
    function app_name()
    {
        return config('app.name');
    }
}

if (!function_exists('gravatar')) {
    /**
     * Access the gravatar helper.
     */
    function gravatar()
    {
        return app('gravatar');
    }
}

if (!function_exists('timezone')) {
    /**
     * Access the timezone helper.
     */
    function timezone()
    {
        return resolve(Timezone::class);
    }
}

if (!function_exists('include_route_files')) {

    /**
     * Loops through a folder and requires all PHP files
     * Searches sub-directories as well.
     *
     * @param $folder
     */
    function include_route_files($folder)
    {
        try {
            $rdi = new recursiveDirectoryIterator($folder);
            $it = new recursiveIteratorIterator($rdi);

            while ($it->valid()) {
                if (!$it->isDot() && $it->isFile() && $it->isReadable() && $it->current()->getExtension() === 'php') {
                    require $it->key();
                }

                $it->next();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

if (!function_exists('home_route')) {

    /**
     * Return the route to the "home" page depending on authentication/authorization status.
     *
     * @return string
     */
    function home_route()
    {
        if (auth()->check()) {
            if (auth()->user()->can('view backend')) {
                return 'admin.dashboard';
            } else {
                return 'frontend.user.dashboard';
            }
        }

        return 'frontend.index';
    }
}

if (!function_exists('style')) {

    /**
     * @param       $url
     * @param array $attributes
     * @param null $secure
     *
     * @return mixed
     */
    function style($url, $attributes = [], $secure = null)
    {
        return resolve(HtmlHelper::class)->style($url, $attributes, $secure);
    }
}

if (!function_exists('script')) {

    /**
     * @param       $url
     * @param array $attributes
     * @param null $secure
     *
     * @return mixed
     */
    function script($url, $attributes = [], $secure = null)
    {
        return resolve(HtmlHelper::class)->script($url, $attributes, $secure);
    }
}

if (!function_exists('form_cancel')) {

    /**
     * @param        $cancel_to
     * @param        $title
     * @param string $classes
     *
     * @return mixed
     */
    function form_cancel($cancel_to, $title, $classes = 'btn btn-danger btn-sm')
    {
        return resolve(HtmlHelper::class)->formCancel($cancel_to, $title, $classes);
    }
}

if (!function_exists('form_submit')) {

    /**
     * @param        $title
     * @param string $classes
     *
     * @return mixed
     */
    function form_submit($title, $classes = 'btn btn-success btn-sm pull-right')
    {
        return resolve(HtmlHelper::class)->formSubmit($title, $classes);
    }
}

if (!function_exists('camelcase_to_word')) {

    /**
     * @param $str
     *
     * @return string
     */
    function camelcase_to_word($str)
    {
        return implode(' ', preg_split('/
          (?<=[a-z])
          (?=[A-Z])
        | (?<=[A-Z])
          (?=[A-Z][a-z])
        /x', $str));
    }
}

if (!function_exists('logActivity')) {
    function logActivity($user, $type, $details)
    {
        $activity = new Activity;
        $activity->user = $user;
        $activity->type = $type;
        $activity->detail = $details;
        $activity->created_at = \Carbon\Carbon::now();
        $activity->updated_at = \Carbon\Carbon::now();
        $activity->save();

        \Log::info('User: ' . $user . ' Type: ' . $type . ' Details: ' . $details);
    }
}

if (!function_exists('checkTransaction')) {
    function isSuccessTransactionExists($trackingNo)
    {
        $transaction = Transaction::query()->where('tracking_no', $trackingNo)->where('status', Transaction::STATUS_SUCCESS)->get();
        if ($transaction) {
            if (sizeof($transaction) > 0) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('aiaResCode')) {
    function aiaResCode($code)
    {
        \Log::info('code: ' . $code);
        $resList = [
            '0' => 'Success',
            '1' => 'Invalid Client ID',
            '2' => 'Authentication Failure',
            '3' => 'Invalid Policy No',
            '4' => 'Policy Number Does Not Exist',
            '5' => 'Invalid Instrument Type',
            '6' => 'Invalid Instrument No',
            '7' => 'Invalid Payment Amount',
            '8' => 'Duplicate Instrument No',
            '9' => 'Payment Processing Error Due To Other Reasons'
        ];

        return array_key_exists($code, $resList) ? $resList[$code] : 'error_res';
    }
}

if (!function_exists('itemUrl')) {
    function itemUrl()
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $codeAlphabet .= rand(111111, 999999);
        $max = strlen($codeAlphabet);

        for ($i = 0; $i < 8; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $token;
    }
}

if (!function_exists('validatePoNo')) {
    function validatePoNo($poNo, $user)
    {
        if (env('STAGE') == 'local') {
            return response()->json([
                'status' => 200,
                'data' => [
                    'status' => 'Success'
                ]
            ]);
        } else {
            try {
                \Log::info($poNo);
                \Log::info($user);
                \Log::info(env('AIA_WEB_SERVICE_URL'));

                $url = env('AIA_WEB_SERVICE_URL');
                $curl = curl_init();

                $xml = '
         <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="http://ws.aviva.com/">
            <soapenv:Header/>
            <soapenv:Body>
                <ws:validatePolicyNo>
                    <policyNo>' . $poNo . '</policyNo>
                </ws:validatePolicyNo>
            </soapenv:Body>
        </soapenv:Envelope>
        ';

                curl_setopt_array($curl, array(
                    CURLOPT_PORT => env('AIA_WEB_SERVICE_POST'),
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $xml,
                    CURLOPT_HTTPHEADER => array(
                        "Content-Type: text/xml",
                        "cache-control: no-cache"
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);
                $responseCode = '';
                $dom = new DOMDocument;
                $dom->loadXML($response);
                foreach ($dom->getElementsByTagName('validatePolicyNoResponse') as $item0) {
                    $responseCode = $item0->nodeValue;
                }

                \Log::info(Carbon::now() . "======AIA validatePolicyNo REQUEST/RESPOSE");
                \Log::info($url);
                \Log::info('-Request');
                \Log::info($xml);
                \Log::info('-Response');
                \Log::info($responseCode);
                \Log::info(Carbon::now() . "======AIA validatePolicyNo REQUEST/RESPOSE");

                if ($err) {
                    \Log::info("cURL Error #:" . $err);
                    logActivity($user, 'PolicyValidate', "Enter Policy No:" . $poNo . "  Response:" . $err);
                    return response()->json([
                        'status' => 400,
                        'data' => [
                            'code' => 'InvalidException',
                            'message' => 'Invalid policy no.'
                        ]
                    ]);
                } else {
                    \Log::info($response);
                    if ($responseCode == 0) {
                        logActivity($user, 'PolicyValidate', "Enter Policy No:" . $poNo . "  Response: SUCCESS [" . aiaResCode($responseCode) . "]");
                        return response()->json([
                            'status' => 200,
                            'data' => [
                                'status' => 'Success'
                            ]
                        ]);
                    } else {
                        logActivity($user, 'PolicyValidate', "Enter Policy No:" . $poNo . "  Response: FAILED [" . aiaResCode($responseCode) . "]");
                        return response()->json([
                            'status' => 400,
                            'data' => [
                                'code' => 'InvalidException',
                                'message' => 'Invalid policy no.'
                            ]
                        ]);
                    }
                }
            } catch (Exception $exception) {
                \Log::info($exception->getMessage());
                return response()->json([
                    'status' => 400,
                    'data' => [
                        'code' => 'InvalidException',
                        'message' => 'AIA policy validate service currently unavailable.'
                    ]
                ]);
            }
        }
    }
}

if (!function_exists('sendEmail')) {
    function sendEmail($email, $password)
    {
        $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $client = new Client();


//        $data = [
//            'headers' => [
//                'Content-Type' => 'application/json',
//                'Authorization' => 'Apikey '.env('SHOUTOUT_APIKEY'),
//            ],
//            'json' => [
//                'source' => env('SHOUTOUT_EMAIL_SOURCE'),
//                'destinations' => [$email],
//                'transports' => ['EMAIL'],
//                'content' => [
//                    'email' => [
//                        'htmlType' => 'True',
//                        'subject' => 'Login Details',
//                        'body' => 'Email : ' . $email . '<br/> Password: ' . $password . '<br/> URL: ' . $url
//                    ]
//                ]
//            ]
//        ];
//
//        \Log::info(print_r($data, true));
//
//        $result = $client->post(env('SHOUTOUT_URL'), $data);
//        $result = $result->getBody()->getContents();
//        \Log::info($result);
    }
}

if (!function_exists('codeCheck')) {
    function codeCheck($code)
    {
        $resResult = [
            'CL' => [
                'status' => 'active',
                'desc' => 'Claimed'
            ],
            'ET' => [
                'status' => 'active',
                'desc' => 'Extended Term Insurance'
            ],
            'HA' => [
                'status' => 'active',
                'desc' => 'Premium Holiday'
            ],
            'HP' => [
                'status' => 'active',
                'desc' => 'PH Reinstatement Pending'
            ],
            'LA' => [
                'status' => 'active',
                'desc' => 'Lapsed'
            ],
            'PH' => [
                'status' => 'active',
                'desc' => 'Premium Deferment / Holiday'
            ],
            'PP' => [
                'status' => 'active',
                'desc' => 'Premium Paying'
            ],
            'PU' => [
                'status' => 'active',
                'desc' => 'Made Paid-up'
            ],
            'PX' => [
                'status' => 'active',
                'desc' => 'Premium Deferment / Holiday'
            ],
            'IF' => [
                'status' => 'active',
                'desc' => 'IF'
            ],


            'AP' => [
                'status' => 'inactive',
                'desc' => 'Annuity in Payment'
            ],
            'CF' => [
                'status' => 'inactive',
                'desc' => 'Cancelled from Inception'
            ],
            'DC' => [
                'status' => 'inactive',
                'desc' => 'Declined'
            ],
            'DH' => [
                'status' => 'inactive',
                'desc' => 'Approved Death Claim'
            ],
            'DM' => [
                'status' => 'inactive',
                'desc' => 'Deferred Maturity'
            ],
            'ES' => [
                'status' => 'inactive',
                'desc' => 'Early Settlement Surrender'
            ],
            'EX' => [
                'status' => 'inactive',
                'desc' => 'Expired'
            ],
            'FL' => [
                'status' => 'inactive',
                'desc' => 'Freelook'
            ],
            'FP' => [
                'status' => 'inactive',
                'desc' => 'PPT Completed'
            ],
            'LX' => [
                'status' => 'inactive',
                'desc' => 'Lapsed - No Reinstatement'
            ],
            'MA' => [
                'status' => 'inactive',
                'desc' => 'Matured'
            ],
            'NF' => [
                'status' => 'inactive',
                'desc' => 'Non-Forfeiture Surrendered'
            ],
            'NT' => [
                'status' => 'inactive',
                'desc' => 'Not taken Up'
            ],
            'PO' => [
                'status' => 'inactive',
                'desc' => 'Contract Postponed'
            ],
            'RD' => [
                'status' => 'inactive',
                'desc' => 'Registered Death Claim'
            ],
            'S' => [
                'status' => 'inactive',
                'desc' => 'Suspended Pending Transaction'
            ],
            'SC' => [
                'status' => 'inactive',
                'desc' => 'Special Cancellation'
            ],
            'SP' => [
                'status' => 'inactive',
                'desc' => 'Single Premium'
            ],
            'ST' => [
                'status' => 'inactive',
                'desc' => 'Termination due to Loan > CSV'
            ],
            'SU' => [
                'status' => 'inactive',
                'desc' => 'Fully Surrendered'
            ],
            'TR' => [
                'status' => 'inactive',
                'desc' => 'Terminated'
            ],
            'VR' => [
                'status' => 'inactive',
                'desc' => 'Vesting Registered'
            ],
            'WD' => [
                'status' => 'inactive',
                'desc' => 'Withdrawn'
            ],
            'WB' => [
                'status' => 'inactive',
                'desc' => 'Withdrawn Benefit'
            ]
        ];

        return array_key_exists($code, $resResult) ? $resResult[$code] : [
            'status' => 'n/a',
            'desc' => 'n/a'
        ];
    }
}

if (!function_exists('intervalList')) {
    function intervalList($code)
    {
        $resResult = [
            '12' => 'MONTHLY',
            '6' => 'BIANNUAL',
            '4' => 'QUARTERLY',
            '1' => 'YEARLY'
        ];
        return array_key_exists($code, $resResult) ? $resResult[$code] : 'error_res';
    }
}

if (!function_exists('aiaDateFormat')) {
    function aiaDateFormat($date)
    {
        //20190222
        $year = substr($date, 0, 4);
        $month = substr($date, 4, 2);
        $day = substr($date, 6, 2);
        return $year . '-' . $month . '-' . $day;
    }
}
