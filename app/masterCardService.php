<?php

use GuzzleHttp\Client;

class masterCardService
{
    public static function sendRequest($url, $method, $data)
    {
        $fullUrl = env('MASTER_CARD_URL') . $url;
        $auth = base64_encode(env('MASTER_CARD_USERNAME') . ':' . env('MASTER_CARD_PASSWORD'));

        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $fullUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    'Authorization: Basic ' . $auth,
                    "cache-control: no-cache"
                ),
            ));

            if ($method != "GET") {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            }

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                return json_decode($err, true);
            } else {
                return json_decode($response, true);
            }

        } catch (\Exception $ex) {
            return json_decode($ex->getMessage(), true);
        }
    }

    public static function getUniqeId($type)
    {
        date_default_timezone_set("Asia/Colombo");
        return "DP" . $type . date("dmYhis");
    }

    public static function sendSms($mobile, $message)
    {
        try{
            $client = new Client(['verify' => false ]);
            $result = $client->post(env('SHOUTOUT_URL'), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Apikey '.env('SHOUTOUT_APIKEY'),
                ],
                'json' => [
                    'source' => env('SHOUTOUT_SOURCE'),
                    'destinations' => [$mobile],
                    'transports' => ['sms'],
                    'content' => [
                        "sms" => $message
                    ]
                ]
            ]);
            $result = $result->getBody()->getContents();
            \Log::info($result);
            return $result;
        }catch (\Exception $ex){
            \Log::info($ex->getMessage());
        }

    }

}
