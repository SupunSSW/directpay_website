<?php


namespace App\Scheduler;


use App\Mail\scheduleReports;
use App\Models\insurence;
use App\Models\Transaction;
use Carbon\Carbon;
use Excel;
use Mail;

class RecurringFailedReport
{
    public function __invoke()
    {
        try {

            $day = Carbon::now()->day;
            $month = Carbon::now()->month;
            $year = Carbon::now()->year;

            $trn = Transaction::select('policy_no')
                ->where('status', 'FAILED')
                ->where('type', 1)
                ->where(\DB::raw('day(created_at)'), $day)
                ->where(\DB::raw('month(created_at)'), $month)
                ->where(\DB::raw('year(created_at)'), $year)
                ->distinct()
                ->get();

            $dataArr = [];

            foreach ($trn as $item) {
                $count = Transaction::where('status', 'FAILED')
                    ->where('type', 1)
                    ->where('policy_no', $item->policy_no)
                    ->where(\DB::raw('day(created_at)'), $day)
                    ->where(\DB::raw('month(created_at)'), $month)
                    ->where(\DB::raw('year(created_at)'), $year)
                    ->count();

                $reason = Transaction::select('description')
                    ->where('status', 'FAILED')
                    ->where('type', 1)
                    ->where('policy_no', $item->policy_no)
                    ->where(\DB::raw('day(created_at)'), $day)
                    ->where(\DB::raw('month(created_at)'), $month)
                    ->where(\DB::raw('year(created_at)'), $year)
                    ->first();

                array_push($dataArr, [
                    $item->policy_no,
                    Carbon::now()->format('d-m-Y'),
                    $count,
                    $reason->description
                ]);
            }

            $sheetName = 'RECURRING_FAILED_' . Carbon::parse(Carbon::now())->format('Y-m-d');
            Excel::create($sheetName, function ($excel) use ($dataArr) {

                $excel->sheet('RECURRING_FAILED_', function ($sheet) use ($dataArr) {
                    $sheet->cell('A1', function ($cell) {
                        $cell->setValue('Policy number');
                    });
                    $sheet->cell('B1', function ($cell) {
                        $cell->setValue('Due Date');
                    });
                    $sheet->cell('C1', function ($cell) {
                        $cell->setValue('Attempts');
                    });
                    $sheet->cell('D1', function ($cell) {
                        $cell->setValue('Reason');
                    });

                    if (!empty($dataArr)) {
                        for ($i = 0; $i < count($dataArr); $i++) {
                            $z = $i + 2;
                            $sheet->cell('A' . $z, $dataArr[$i][0]);
                            $sheet->cell('B' . $z, $dataArr[$i][1]);
                            $sheet->cell('C' . $z, $dataArr[$i][2]);
                            $sheet->cell('D' . $z, $dataArr[$i][3]);
                        }
                    }
                });

            })->store('xls', storage_path('Excel'));

            $emails = explode(',', env('RECUR_FAILED_REPORT'));
            Mail::to($emails)->send(new scheduleReports('Recurring Failed Report', $sheetName, 'Recurring Failed Report'));

            \Log::info('RecurringFailedReport');
            \Log::info('File');
            \Log::info($sheetName);
            \Log::info('RecurringFailedReport/');
        } catch (\Exception $exception) {
            \Log::info('RecurringFailedReport');
            \Log::info('$exception');
            \Log::info($exception->getMessage());
            \Log::info('RecurringFailedReport/');
        }

    }
}
