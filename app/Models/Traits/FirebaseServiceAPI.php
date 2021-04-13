<?php


namespace App\Models\Traits;

use App\Models\Notification;

trait FirebaseServiceAPI
{
    public function sendNotification($title, $body, $token,$data, $type = Notification::TYPE_NOTIFICATION)
    {
        $fcmKey = env('FIREBASE_KEY');

        $data = array(
            "data" => $data,
            "type" => $type
        );
        $notification = array(
            "title" => $title,
            "body" => $body
        );
        $dataArray = array(
            "to" => $token,
            "priority" => "high",//use National ID Number instead of username
            "data" => $data,
            "notification" => $notification,
            "click_action" => "FLUTTER_NOTIFICATION_CLICK"
        );
        $data_string = json_encode($dataArray);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => array(
                "Authorization: key=" . $fcmKey,
                "Content-Type: application/json"
            ),
        ));

        $curlResponse = curl_exec($curl);
        $err = curl_error($curl);

        if ($curlResponse) {
            $decoded_response = json_decode($curlResponse, true);
            \Log::info("fcm: " . "success: " . $curlResponse . " token: " . $token);
        }
        if ($err) {
            \Log::info("fcm: " . "failed with error: " . $err);
        }
    }
}
