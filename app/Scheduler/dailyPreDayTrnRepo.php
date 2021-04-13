<?php


namespace App\Scheduler;


use App\Mail\scheduleReports;
use App\Models\insurence;
use App\Models\TransactionModel;
use Carbon\Carbon;
use Excel;
use Log;
use Mail;

class dailyPreDayTrnRepo
{
    public function __invoke()
    {
        try {

            $getDate = Carbon::now()->addDay(-1);
            $from = $getDate->format('Y-m-d') . ' 00:00:00';
            $to = $getDate->format('Y-m-d') . ' 23:59:59';

            //$from = '2020-05-22 00:00:00';
            //$to = '2020-06-22 23:59:59';

            $trnDates = [$from, $to];

            $data = TransactionModel::select([
                'id as TrnNo',
                'payerAccountNumber as Account',
                'originalAmount as Amount',
                'payerUserName as Payer',
                'payeeUserName as Payee',
                'castomParam as CustomParam',
                'reference',
                'payUsing',
                'type',
                'status',
                'dateTime'
            ])
                ->Where(function ($query) {
                    $query->where('payeeId', env('PAYEE_ID'))
                        ->orWhere('payerId', env('PAYEE_ID'));
                })
                ->whereBetween('dateTime', $trnDates)
                ->orderBy('id', 'DESC')
                ->get();

            $sheetName = ' ALL_TRAN_PRE_DAY_' . Carbon::parse(Carbon::now())->format('Y-m-d');

            Excel::create($sheetName, function ($excel) use ($data) {

                $excel->sheet('All transactions', function ($sheet) use ($data) {
                    $sheet->fromArray($data);
                });

                $excel->sheet('Bill payments', function ($sheet) use ($data) {
                    $i = 1;

                    $sheet->row(1, array(
                        'TrnNo',
                        'Account',
                        'Amount',
                        'Payer',
                        'Payee',
                        'CustomParam',
                        'reference',
                        'payUsing',
                        'type',
                        'status',
                        'dateTime',
                    ));

                    foreach ($data as $trn) {
                        if ($trn['type'] == 'BILL_PAYMENT') {
                            $i++;
                            $sheet->row($i, array(
                                $trn->TrnNo,
                                $trn->Account,
                                $trn->Amount,
                                $trn->Payer,
                                $trn->Payee,
                                $trn->CustomParam,
                                $trn->reference,
                                $trn->payUsing,
                                $trn->type,
                                $trn->status,
                                $trn->dateTime,
                            ));
                        }
                    }
                });

                $excel->sheet('Commission', function ($sheet) use ($data) {
                    $i = 1;

                    $sheet->row(1, array(
                        'TrnNo',
                        'Account',
                        'Amount',
                        'Payer',
                        'Payee',
                        'CustomParam',
                        'reference',
                        'payUsing',
                        'type',
                        'status',
                        'dateTime',
                    ));

                    foreach ($data as $trn) {
                        if ($trn['type'] == 'COMMISSION') {
                            $i++;
                            $sheet->row($i, array(
                                $trn->TrnNo,
                                $trn->Account,
                                $trn->Amount,
                                $trn->Payer,
                                $trn->Payee,
                                $trn->CustomParam,
                                $trn->reference,
                                $trn->payUsing,
                                $trn->type,
                                $trn->status,
                                $trn->dateTime,
                            ));
                        }
                    }
                });

                $excel->sheet('TopUps', function ($sheet) use ($data) {
                    $i = 1;

                    $sheet->row(1, array(
                        'TrnNo',
                        'Account',
                        'Amount',
                        'Payer',
                        'Payee',
                        'CustomParam',
                        'reference',
                        'payUsing',
                        'type',
                        'status',
                        'dateTime',
                    ));

                    foreach ($data as $key => $trn) {
                        if ($trn->type == 'MERCHANT_WALLET_TOPUP') {
                            $i++;
                            $sheet->row($i, array(
                                $trn->TrnNo,
                                $trn->Account,
                                $trn->Amount,
                                $trn->Payer,
                                $trn->Payee,
                                $trn->CustomParam,
                                $trn->reference,
                                $trn->payUsing,
                                $trn->type,
                                $trn->status,
                                $trn->dateTime,
                            ));
                        }
                    }
                });


            })->store('xls', storage_path('Excel'));

            $emails = explode(',', env('DAILIY_PRE_TRN_EMAILS'));
            Mail::to($emails)->send(new scheduleReports('Pre Daily Transaction Report', $sheetName, 'Pre Daily Transaction Report'));

        } catch (\Exception $exception) {
            \Log::info('ALL_TRANSACTIONS_MADE_THE_PREVIOUS_DAY_');
            \Log::info('exception');
            \Log::info($exception);
            \Log::info('ALL_TRANSACTIONS_MADE_THE_PREVIOUS_DAY_' . "\n");
        }
    }
}
