<?php


namespace App\Scheduler;


use App\Mail\scheduleReports;
use App\Models\insurence;
use Carbon\Carbon;
use Excel;
use Mail;

class CardExpiryReport
{
    public function __invoke()
    {
        try {

            $year = Carbon::now()->format('y');
            $month = Carbon::now()->addMonth()->month;

            if($month == 12){
                $year = Carbon::now()->addYear()->format('y');
            }

            $data = insurence::select(['police_no', 'card_exp', 'next_payment_amount as Amount', 'interval as Frequency'])
                ->where('interval', '!=', '')
                ->where('status', 'active')
                ->where(\DB::raw('substring_index(card_exp, \'-\', -1)'), '=', $year)
                ->where(\DB::raw('substring_index(card_exp, \'-\', 1)'), '=', $month)
                ->get();

            $sheetName = 'CARD_EXPIRY_' . Carbon::parse(Carbon::now())->format('Y-m-d');

            Excel::create($sheetName, function ($excel) use ($data) {
                $excel->sheet('CARD_EXPIRY_' . Carbon::parse(Carbon::now())->format('Y-m-d'), function ($sheet) use ($data) {
                    $sheet->fromArray($data);
                });
            })->store('xls', storage_path('Excel'));

            $emails = explode(',', env('DAILY_DOWN_REPORT'));
            Mail::to($emails)->send(new scheduleReports('Card Expiry Report', $sheetName, 'Next Month Card Expiry Report'));
            \Log::info('CardExpiryReport');
            \Log::info('File');
            \Log::info($sheetName);
            \Log::info('CardExpiryReport/');
        } catch (\Exception $exception) {
            \Log::info('CardExpiryReport');
            \Log::info('$exception');
            \Log::info($exception->getMessage());
            \Log::info('CardExpiryReport/');
        }
    }
}
