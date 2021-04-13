<?php

namespace App\DataTables;

use App\Models\Transaction;
use App\Models\TransactionModel;
use App\User;
use Carbon\Carbon;
use Log;
use Yajra\DataTables\Services\DataTable;

class TransactionReportDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
//            ->editColumn('type', function ($data) {
//                if ($data->type == Transaction::TYPE_MAIN) {
//                    return '<h6><span class="badge badge-pill badge-primary">MAIN TRANSACTION</span></h6>';
//                } elseif ($data->type == Transaction::TYPE_ONE_TIME) {
//                    return '<h6><span class="badge badge-pill badge-primary">ONE TIME TRANSACTION</span></h6>';
//                } else {
//                    return '<h6><span class="badge badge-pill badge-primary">RECURED TRANSACTION</span></h6>';
//                }
//            })
            ->editColumn('status', function ($data) {
                if ($data->status == 'SUCCESS') {
                    return '<h6><span class="badge badge-pill badge-success">' . $data->status . '</span></h6>';
                } else {
                    return '<h6><span class="badge badge-pill badge-danger">' . $data->status . '</span></h6>';
                }
            })
//            ->editColumn('amount', function ($data){
//                return number_format($data->amount, 2, '.', ',');
//            })
//            ->editColumn('aia_res', function ($data){
//                if ($data->status == 'SUCCESS') {
//                    return $data->aia_res;
//                } else{
//                    return '-';
//                }
//            })
            ->rawColumns(['type', 'status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(TransactionModel $model)
    {
        $trn = $model->newQuery()->Where(function ($query) {
            $query->where('payeeId', env('PAYEE_ID'))
                ->orWhere('payerId', env('PAYEE_ID'));
        })
        ->whereIn('payUsing', ['WALLET', 'MERCHANT_WALLET']);

        if ($this->request()->has('dateRange')) {

            Log::info($this->request()->get('dateRange'));

            $dateRange = $this->request()->get('dateRange');
            $dateRange = explode(":", $dateRange);
            if (count($dateRange) > 1) {
                $from = $dateRange[0] . ' 00:00:00';
                $to = $dateRange[1] . ' 23:59:59';
                $trn->whereBetween('dateTime', [$from, $to]);
            } else {
                $dateRange = $this->request()->get('dateRange');
                $from = $dateRange . ' 00:00:00';
                $to = $dateRange . ' 23:59:59';
                $trn->whereBetween('dateTime', [$from, $to]);
            }
        } else {
            $date = Carbon::now()->format('Y-m-d');
            $from = $date . ' 00:00:00';
            $to = $date . ' 23:59:59';
            $trn->whereBetween('dateTime', [$from, $to]);
        }

        if($this->request()->has('type')){
            $trn->where('type', $this->request()->get('type'));
        }

        if($this->request()->has('api')){
            $trn->where('initializedBy', $this->request()->get('api'));
        }
//
        if ($this->request()->has('status')) {
            $status = $this->request()->get('status');
            $trn->where('status', "'$status'");
        }
//
//        if($this->request()->has('policyNo')){
//            $poNo = $this->request()->get('policyNo');
//            $trn->where('policy_no', "$poNo");
//        }

        return $trn;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters($this->getBuilderParameters());
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'id' => ['title' => 'Trn No'],
            //'reference' => ['title' => 'Order No'],
            'payerAccountNumber' => ['title' => 'Account'],
            'originalAmount' => ['title' => 'Amount'],
            //'bankerResponseDesc' => ['title' => 'Card Response'],
            'payerUserName' => ['title' => 'Payer'],
            'payeeUserName' => ['title' => 'Payee'],
            'castomParam' => ['title' => 'Custom Param'],
            'reference',
            'payUsing',
            'type',
            'initializedBy',
            'status',
            'dateTime'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'TransactionReport_' . date('YmdHis');
    }
}
