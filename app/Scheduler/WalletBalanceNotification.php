<?php


namespace App\Scheduler;


use App\Mail\scheduleReports;
use App\Mail\walletNotification;
use App\Models\emailNotifications;
use App\Models\insurence;
use App\Models\MerhantWallet;
use Carbon\Carbon;
use Excel;
use Mail;

class WalletBalanceNotification
{
    public function __invoke()
    {
        try {
            $check600kNotificationRun = emailNotifications::where('status', 1)
                ->where('type', emailNotifications::EMAIL600k)
                ->whereDate('created_at', Carbon::today())->first();

            $check300kNotificationRun = emailNotifications::where('status', 1)
                ->where('type', emailNotifications::EMAIL300K)
                ->whereDate('created_at', Carbon::today())->first();

            $check1000kNotificationRun = emailNotifications::where('status', 1)
                ->where('type', emailNotifications::EMAIL1000k)
                ->whereDate('created_at', Carbon::today())->first();

            $pointBalance = MerhantWallet::where('merchantId', env('PAYEE_ID'))->first();

            if ($pointBalance) {

                if (!$check1000kNotificationRun) {
                    if ($pointBalance->balance >= 600000 && $pointBalance->balance <= 1000000) {
                        $sendNotification = new emailNotifications();
                        $sendNotification->type = emailNotifications::EMAIL1000k;
                        $sendNotification->status = 1;
                        $sendNotification->created_at = Carbon::now();
                        $sendNotification->updated_at = Carbon::now();
                        $sendNotification->save();

                        $emails = explode(',', env('WALLET_NOTIF_EMAILS'));
                        Mail::to($emails)->send(new walletNotification(
                            "Wallet balance 1000k notification",
                            "Wallet balance: " . number_format($pointBalance->balance, 2),
                            "<p style='color: green'>NORMAL</p>"));

                    }
                }

                if (!$check600kNotificationRun) {
                    if ($pointBalance->balance >= 300000 && $pointBalance->balance <= 600000) {
                        $sendNotification = new emailNotifications();
                        $sendNotification->type = emailNotifications::EMAIL600k;
                        $sendNotification->status = 1;
                        $sendNotification->created_at = Carbon::now();
                        $sendNotification->updated_at = Carbon::now();
                        $sendNotification->save();

                        $emails = explode(',', env('WALLET_NOTIF_EMAILS'));
                        Mail::to($emails)->send(new walletNotification(
                            "Wallet balance 600k notification",
                            "Wallet balance: " . number_format($pointBalance->balance, 2),
                            "<p style='color: yellow'>WARNING</p>"));

                    }
                }

                if (!$check300kNotificationRun) {
                    if ($pointBalance->balance >= 0 && $pointBalance->balance <= 300000) {
                        $sendNotification = new emailNotifications();
                        $sendNotification->type = emailNotifications::EMAIL300K;
                        $sendNotification->status = 1;
                        $sendNotification->created_at = Carbon::now();
                        $sendNotification->updated_at = Carbon::now();
                        $sendNotification->save();

                        $emails = explode(',', env('WALLET_NOTIF_EMAILS'));
                        Mail::to($emails)->send(new walletNotification(
                            "Wallet balance 300k notification",
                            "Wallet balance: " . number_format($pointBalance->balance, 2),
                            "<p style='color: red'>CRITICAL</p>"));

                    }
                }

            }
        } catch (\Exception $exception) {
            \Log::info('WalletBalanceNotification');
            \Log::info('exception');
            \Log::info($exception->getMessage());
            \Log::info('WalletBalanceNotification/');
        }
    }
}
