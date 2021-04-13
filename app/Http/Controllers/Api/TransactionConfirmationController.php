<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Models\Auth\User;
use App\Models\Traits\FirebaseServiceAPI;

class TransactionConfirmationController extends Controller
{
    use FirebaseServiceAPI;

    public function confirm(Request $request, $method)
    {
        \Log::info("Confirmation received - method: " . $method . " , body: " . json_encode($request->all()));

        //send notification to agent
        if ($method == 'qr') {
            $id = $request->json('reference');
            $transaction_id = $request->json('transactionId');
            $reference = 'LANKA_QR';
            $datetime = date_parse_from_format('d-m-Y h:i a', $request->json('dateTime'));
            $status = $request->json('status');

            $transaction = Transaction::find($id);

            if (!$transaction) {
                return response()->json([
                    'status' => 400,
                    'data' => [
                        'title' => 'Error',
                        'code' => 'TransactionNotFound',
                        'message' => 'Transaction not found for given reference!'
                    ]
                ]);
            }

            logActivity($transaction->agent_email, 'Transactions', 'Transaction confirmation - method: ' . $method . ' status: ' . $status . ' body: ' . json_encode($request->all()));

            $transaction->transaction_id = $transaction_id;
            $transaction->transaction_reference = $reference;
            $transaction->transaction_datetime = $datetime["year"] . '-' . $datetime["month"] . '-' . $datetime["day"] . ' ' . $datetime["hour"] . ':' . $datetime["minute"] . ':' . $datetime['second'];
            $transaction->status = $status == 200 ? Transaction::STATUS_SUCCESS : Transaction::STATUS_FAILED;

            $transaction->save();
            if ($status == 200) {
                $this->sendUserNotification($transaction->agent_id, "Payment received", "Successfully payment received for " . $transaction->tracking_no);
            } else if ($status == 400) {
                $this->sendUserNotification($transaction->agent_id, "Payment failed", "Payment failed for " . $transaction->tracking_no);
            }
        } else if ($method == 'link') {
            $id = $request->json('data.order.reference');
            $transaction_id = $request->json('data.transaction.transactionId');
            $reference = $request->json('data.transaction.card.number');
            $datetime = $request->json('data.transaction.dateTime');
            $status = $request->json('data.transaction.status');

            $user = $request->json('data.order.customerEmail');
            logActivity($user, 'Transactions', 'Transaction confirmation - method: ' . $method . ' status: ' . $status . ' body: ' . json_encode($request->all()));

            $transaction = Transaction::find($id);

            if (!$transaction) {
                return response()->json([
                    'status' => 400,
                    'data' => [
                        'title' => 'Error',
                        'code' => 'TransactionNotFound',
                        'message' => 'Transaction not found for given reference!'
                    ]
                ]);
            }

            $transaction->transaction_id = $transaction_id;
            $transaction->transaction_reference = $reference;
            $transaction->transaction_datetime = $datetime;
            $transaction->status = $status;

            $transaction->save();
            if ($status == Transaction::STATUS_SUCCESS) {
                $this->sendUserNotification($transaction->agent_id, "Payment received", "Successfully payment received for " . $transaction->tracking_no . " from " . $reference);
            } else if ($status == Transaction::STATUS_FAILED) {
                $this->sendUserNotification($transaction->agent_id, "Payment failed", "Payment failed for " . $transaction->tracking_no);
            }
        } else {
            return response()->json([
                'status' => 400,
                'data' => [
                    'title' => 'Error',
                    'code' => 'UnsupportedMethod',
                    'message' => 'Unsupported method!'
                ]
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => [
                'title' => 'Success',
                'code' => 'TransactionUpdated',
                'message' => 'Successfully transaction updated!'
            ]
        ]);
    }

    private function sendUserNotification($user_id, $title, $message)
    {
        $agent = User::find($user_id);
        if ($agent->firebase_token) {
            $notification = new Notification();

            $notification->title = $title;
            $notification->message = $message;
            $notification->user_id = $user_id;
            $notification->scope = 'USER';

            $notification->save();
            $this->sendNotification($title, $message, $agent->firebase_token, ['type' => Notification::TYPE_NOTIFICATION], Notification::TYPE_NOTIFICATION);
        } else {
            logActivity($agent->email, 'warning', $agent->email . ' Cannot send notification - Firebase Token not found!. Title: ' . $title . 'Message: ' . $message);
        }
    }
}
