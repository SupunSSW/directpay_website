<?php

namespace App\Http\Controllers\Backend;

use App\Devices;
use App\Http\Controllers\Controller;
use App\Mail\walletNotification;
use App\Models\MerhantWallet;
use App\Models\TransactionModel;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use App\Models\insurence;
use App\Models\Transaction;
use App\Settings;
use Mail;

/**
 * Class DashboardController.
 */
class DashboardController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        //$isGenerateLink = Settings::where('name', 'isGenerateLink')->first()->value;

        $day = [];
        $amounts = [];
        $years = [];
        $thisYear = Carbon::now()->year;

        $yearsData = DB::connection('dp_mysql')->select("SELECT DISTINCT year(dateTime) years
                FROM transaction;"
        );

        foreach ($yearsData as $item) {
            array_push($years, $item->years);
        }

        $reData = [
            'link' => 0,
            'day' => 12,
            'years' => $years,
            'thisYear' => $thisYear,
            'amounts' => 0
        ];

        return view('backend.dashboard')->with($reData);
    }

    public function getChartData(Request $request)
    {
        $year = $request->input('year');
        $type = $request->input('type');
        $mainAmount = [];
        $recurAmount = [];
        $totAmount = [];
        $main = [];
        $labels = [];

        if ($year && $type) {
            if ($type == 'month') {
//                $main = DB::select(
//                    "SELECT sum(amount) amount, monthname(transaction_date) label
//                from transactions where year(transaction_date) = " . $year . " and type != 1 and
//                status = 'SUCCESS' group by year(transaction_date),month(transaction_date) order by month(transaction_date)"
//                );
//
//                $recur = DB::select(
//                    "SELECT sum(amount) amount, monthname(transaction_date) label
//                from transactions where year(transaction_date) = " . $year . " and type = 1 and
//                status = 'SUCCESS' group by year(transaction_date),month(transaction_date) order by month(transaction_date)"
//                );

                $tot = DB::connection('dp_mysql')->select(
                    "SELECT sum(originalAmount) amount, monthname(dateTime) label
                from transaction where year(dateTime) = " . $year . " and payeeId = '" . env('PAYEE_ID') . "' and
                status = 'SUCCESS' group by year(dateTime),month(dateTime) order by month(dateTime)"
                );
            } else {
//                $main = DB::select(
//                    "SELECT sum(amount) amount, weekofyear(transaction_date) label
//                from transactions
//                where year(transaction_date) = " . $year . "
//                and status = 'SUCCESS' and type != 1
//                group by weekofyear(transaction_date)
//                order by weekofyear(transaction_date);"
//                );
//
//                $recur = DB::select(
//                    "SELECT sum(amount) amount, weekofyear(transaction_date) label
//                from transactions
//                where year(transaction_date) = " . $year . "
//                and status = 'SUCCESS' and type = 1
//                group by weekofyear(transaction_date)
//                order by weekofyear(transaction_date);"
//                );

                $tot = DB::connection('dp_mysql')->select(
                    "SELECT sum(originalAmount) amount, weekofyear(dateTime) label
                from transaction
                where year(dateTime) = " . $year . " and
                payeeId = '" . env('PAYEE_ID') . "'
                and status = 'SUCCESS'
                group by weekofyear(dateTime)
                order by weekofyear(dateTime);"
                );
            }

//            foreach ($main as $item) {
//                array_push($mainAmount, $item->amount);
//                array_push($labels, $item->label);
//            }
//            foreach ($recur as $item) {
//                array_push($recurAmount, $item->amount);
            //}
            foreach ($tot as $item) {
                array_push($totAmount, $item->amount);
                array_push($labels, $item->label);
            }

            return [
                'label' => $labels,
                //'main' => $mainAmount,
                //'recurring' => $recurAmount,
                'tot' => $totAmount
            ];
        } else {
            return [
                'label' => [],
                'main' => [],
                'recurring' => [],
                'tot' => []
            ];
        }
    }

    public function getDashboardData(Request $request)
    {
        $type = $request->input('_type');

        if ($type != '') {
            if ($type == 5) {
                \Log::info($type);
                if (auth()->user()->can('Select dashboard All')) {
                    $type = 0;
                } else if (auth()->user()->can('Select dashboard Today')) {
                    $type = 1;
                } else if (auth()->user()->can('Select dashboard Week')) {
                    $type = 2;
                } else if (auth()->user()->can('Select dashboard Month')) {
                    $type = 3;
                } else if (auth()->user()->can('Select dashboard Year')) {
                    $type = 4;
                } else {
                    $type = 1;
                }
            }

            $pointBalance = MerhantWallet::where('merchantId', env('PAYEE_ID'))->first();

            if ($type == 0) {
                $trnCount = TransactionModel::select(['*'])->where('status', 'SUCCESS')
                    ->where('type', 'BILL_PAYMENT')
                    ->where('payerId', env('PAYEE_ID'))->get()->count();
                $totalTrnAmount = TransactionModel::where('status', 'SUCCESS')
                    ->where('type', 'BILL_PAYMENT')
                    ->where('payerId', env('PAYEE_ID'))->sum('originalAmount');
                $qr = Transaction::query()
                    ->where('type', Transaction::TYPE_QR)
                    ->where('status', Transaction::STATUS_SUCCESS)
                    ->sum('amount');

                $paymentLink = Transaction::query()
                    ->where('type', Transaction::TYPE_LINK)
                    ->where('status', Transaction::STATUS_SUCCESS)
                    ->sum('amount');

                $cash = Transaction::query()
                    ->where('type', Transaction::TYPE_CASH)
                    ->where('status', Transaction::STATUS_SUCCESS)
                    ->sum('amount');
            } else {
                $transaction = TransactionModel::query();
                $day = Carbon::now()->day;
                $week = Carbon::now()->weekOfYear;
                $month = Carbon::now()->month;
                $year = Carbon::now()->year;

                switch ($type) {
                    case 1:
                        //today
                        $trnCount = $transaction->where('status', 'SUCCESS')
                            ->where(\DB::raw('day(dateTime)'), $day)
                            ->where(\DB::raw('month(dateTime)'), $month)
                            ->where(\DB::raw('year(dateTime)'), $year)
                            ->where('type', 'BILL_PAYMENT')
                            ->where('payerId', env('PAYEE_ID'))
                            ->get()->count();

                        $totalTrnAmount = $transaction->where('status', 'SUCCESS')
                            ->where(\DB::raw('day(dateTime)'), $day)
                            ->where(\DB::raw('month(dateTime)'), $month)
                            ->where(\DB::raw('year(dateTime)'), $year)
                            ->where('type', 'BILL_PAYMENT')
                            ->where('payerId', env('PAYEE_ID'))
                            ->sum('originalAmount');

                        $qr = Transaction::query()
                            ->where(\DB::raw('day(created_at)'), $day)
                            ->where(\DB::raw('month(created_at)'), $month)
                            ->where(\DB::raw('year(created_at)'), $year)
                            ->where('type', Transaction::TYPE_QR)
                            ->where('status', Transaction::STATUS_SUCCESS)
                            ->sum('amount');

                        $paymentLink = Transaction::query()
                            ->where(\DB::raw('day(created_at)'), $day)
                            ->where(\DB::raw('month(created_at)'), $month)
                            ->where(\DB::raw('year(created_at)'), $year)
                            ->where('type', Transaction::TYPE_LINK)
                            ->where('status', Transaction::STATUS_SUCCESS)
                            ->sum('amount');

                        $cash = Transaction::query()
                            ->where(\DB::raw('day(created_at)'), $day)
                            ->where(\DB::raw('month(created_at)'), $month)
                            ->where(\DB::raw('year(created_at)'), $year)
                            ->where('type', Transaction::TYPE_CASH)
                            ->where('status', Transaction::STATUS_SUCCESS)
                            ->sum('amount');

                        break;
                    case 2:
                        //week
                        $trnCount = $transaction->select(['*'])->where('status', 'SUCCESS')
                            ->where(\DB::raw('weekofyear(dateTime)'), $week)
                            ->where(\DB::raw('year(dateTime)'), $year)
                            ->where('type', 'BILL_PAYMENT')
                            ->where('payerId', env('PAYEE_ID'))
                            ->get()->count();

                        $totalTrnAmount = $transaction->where('status', 'SUCCESS')
                            ->where(\DB::raw('weekofyear(dateTime)'), $week)
                            ->where(\DB::raw('year(dateTime)'), $year)
                            ->where('type', 'BILL_PAYMENT')
                            ->where('payerId', env('PAYEE_ID'))
                            ->sum('originalAmount');

                        $qr = Transaction::query()
                            ->where(\DB::raw('weekofyear(created_at)'), $week)
                            ->where(\DB::raw('year(created_at)'), $year)
                            ->where('type', Transaction::TYPE_QR)
                            ->where('status', Transaction::STATUS_SUCCESS)
                            ->sum('amount');

                        $paymentLink = Transaction::query()
                            ->where(\DB::raw('weekofyear(created_at)'), $week)
                            ->where(\DB::raw('year(created_at)'), $year)
                            ->where('type', Transaction::TYPE_LINK)
                            ->where('status', Transaction::STATUS_SUCCESS)
                            ->sum('amount');

                        $cash = Transaction::query()
                            ->where(\DB::raw('weekofyear(created_at)'), $week)
                            ->where(\DB::raw('year(created_at)'), $year)
                            ->where('type', Transaction::TYPE_CASH)
                            ->where('status', Transaction::STATUS_SUCCESS)
                            ->sum('amount');
                        break;
                    case 3:
                        //month
                        $trnCount = $transaction->select(['*'])->where('status', 'SUCCESS')
                            ->where(\DB::raw('month(dateTime)'), $month)
                            ->where(\DB::raw('year(dateTime)'), $year)
                            ->where('payerId', env('PAYEE_ID'))
                            ->get()->count();

                        $totalTrnAmount = $transaction->where('status', 'SUCCESS')
                            ->where(\DB::raw('month(dateTime)'), $month)
                            ->where(\DB::raw('year(dateTime)'), $year)
                            ->where('type', 'BILL_PAYMENT')
                            ->where('payerId', env('PAYEE_ID'))
                            ->sum('originalAmount');

                        $qr = Transaction::query()
                            ->where(\DB::raw('month(created_at)'), $month)
                            ->where(\DB::raw('year(created_at)'), $year)
                            ->where('type', Transaction::TYPE_QR)
                            ->where('status', Transaction::STATUS_SUCCESS)
                            ->sum('amount');

                        $paymentLink = Transaction::query()
                            ->where(\DB::raw('month(created_at)'), $month)
                            ->where(\DB::raw('year(created_at)'), $year)
                            ->where('type', Transaction::TYPE_LINK)
                            ->where('status', Transaction::STATUS_SUCCESS)
                            ->sum('amount');

                        $cash = Transaction::query()
                            ->where(\DB::raw('month(created_at)'), $month)
                            ->where(\DB::raw('year(created_at)'), $year)
                            ->where('type', Transaction::TYPE_CASH)
                            ->where('status', Transaction::STATUS_SUCCESS)
                            ->sum('amount');
                        break;
                    case 4:
                        //year
                        $trnCount = $transaction->select(['*'])->where('status', 'SUCCESS')
                            ->where(\DB::raw('year(dateTime)'), $year)
                            ->where('type', 'BILL_PAYMENT')
                            ->where('payerId', env('PAYEE_ID'))
                            ->get()->count();

                        $totalTrnAmount = $transaction->where('status', 'SUCCESS')
                            ->where(\DB::raw('year(dateTime)'), $year)
                            ->where('type', 'BILL_PAYMENT')
                            ->where('payerId', env('PAYEE_ID'))
                            ->sum('originalAmount');

                        $qr = Transaction::query()
                            ->where(\DB::raw('year(created_at)'), $year)
                            ->where('type', Transaction::TYPE_QR)
                            ->where('status', Transaction::STATUS_SUCCESS)
                            ->sum('amount');

                        $paymentLink = Transaction::query()
                            ->where(\DB::raw('year(created_at)'), $year)
                            ->where('type', Transaction::TYPE_LINK)
                            ->where('status', Transaction::STATUS_SUCCESS)
                            ->sum('amount');

                        $cash = Transaction::query()
                            ->where(\DB::raw('year(created_at)'), $year)
                            ->where('type', Transaction::TYPE_CASH)
                            ->where('status', Transaction::STATUS_SUCCESS)
                            ->sum('amount');
                        break;
                }
            }

            return [
                'trn' => $trnCount,
                'trnTot' => 'Rs. ' . $this->amountFormat($totalTrnAmount),
                'pointBalance' => $pointBalance ? number_format($pointBalance->balance, 2) : "0.00",
                'qr' => number_format($qr, 2, '.', ','),
                'payment_link' => number_format($paymentLink, 2, '.', ','),
                'cash' => number_format($cash, 2, '.', ','),
            ];
        } else {
            return [
                'insu' => 0,
                'trn' => 0,
                'devi' => 0,
                'trnTot' => 'Rs. ' . 0,
                'pend' => 0
            ];
        }
    }

    private function getMonthName($month)
    {
        $monthNames = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec'
        ];

        return $monthNames[$month];
    }

    public function isGenerateLink(Request $request)
    {
        $type = $request->input('isEnable');
        $user = auth()->user();
        $value = $type == 'true' ? 'Enable' : 'Disable';
        logActivity($user->email, 'Settings', 'Link Generate: ' . $value);
        $settingData = Settings::where('name', 'isGenerateLink')->first();
        $settingData->value = $type;
        $settingData->save();
        return 'true';
    }

    function amountFormat($n, $precision = 2)
    {
        if ($n < 1000000) {
            // Anything less than a million
            $n_format = number_format($n, 2, '.', ',');
        } else if ($n < 1000000000) {
            // Anything less than a billion
            $n_format = number_format($n / 1000000, $precision, '.', ',') . 'M';
        } else {
            // At least a billion
            $n_format = number_format($n / 1000000000, $precision, '.', ',') . 'B';
        }

        return $n_format;
    }
}
