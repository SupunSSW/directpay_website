<?php


namespace App\Scheduler;


use App\Mail\scheduleReports;
use App\Models\insurence;
use Carbon\Carbon;
use Excel;
use Mail;

class DailyDownloadReport
{
    public function __invoke()
    {
        try {
            $data = insurence::select('police_no')
                ->where('interval', '!=', '')
                ->where('status', 'active')
                ->get();

            $sheetName = 'POLICY_LIST_' . Carbon::parse(Carbon::now())->format('Y-m-d');

            Excel::create($sheetName, function ($excel) use ($data) {
                $excel->sheet('POLICY_LIST_' . Carbon::parse(Carbon::now())->format('Y-m-d'), function ($sheet) use ($data) {
                    $sheet->fromArray($data);
                });
            })->store('xls', storage_path('Excel'));

            $emails = explode(',', env('DAILY_DOWN_REPORT'));
            Mail::to($emails)->send(new scheduleReports('Daily Download Report', $sheetName, 'Daily Download Report'));
            \Log::info('DailyDownloadReport');
            \Log::info('File');
            \Log::info($sheetName);
            \Log::info('DailyDownloadReport/');
        } catch (\Exception $exception) {
            \Log::info('DailyDownloadReport');
            \Log::info('$exception');
            \Log::info($exception->getMessage());
            \Log::info('DailyDownloadReport/');
        }
    }
}
