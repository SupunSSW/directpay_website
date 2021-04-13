<?php

namespace App\DataTables;

use App\Models\Auth\User;
use App\Models\CardTokenModel;
use App\Models\Transaction;
use App\Models\TransactionModel;
use Yajra\DataTables\Services\DataTable;

class RecuredTransactionDataTable extends DataTable
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
            ->editColumn('type', function ($data) {
                if ($data->type == Transaction::TYPE_MAIN) {
                    return '<h6><span class="badge badge-pill badge-primary">MAIN TRANSACTION</span></h6>';
                } elseif ($data->type == Transaction::TYPE_ONE_TIME) {
                    return '<h6><span class="badge badge-pill badge-primary">ONE TIME TRANSACTION</span></h6>';
                } else {
                    return '<h6><span class="badge badge-pill badge-primary">RECURED TRANSACTION</span></h6>';
                }
            })
            ->editColumn('status', function ($data) {
                if ($data->status == 'SUCCESS') {
                    return '<h6><span class="badge badge-pill badge-success">' . $data->status . '</span></h6>';
                } else {
                    return '<h6><span class="badge badge-pill badge-danger">' . $data->status . '</span></h6>';
                }
            })
            ->editColumn('retried', function ($data) {
                if ($data->retried == 1) {
                    return '<h6><span class="badge badge-pill badge-success">&nbsp;</span></h6>';
                } else {
                    return '-';
                }
            })
            ->editColumn('retried_transaction_id', function ($data) {
                if ($data->retried_transaction_id == 0) {
                    return '-';
                } else {
                    return $data->retried_transaction_id;
                }
            })
            ->editColumn('amount', function ($data){
                return number_format($data->amount, 2, '.', ',');
            })
            ->rawColumns(['type', 'status', 'retried']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Transaction $model)
    {
        $trn = $model->newQuery()->select([
            'transactions.*', 'insurences.agent_id'
        ])->join('insurences', 'transactions.insurance_id','=','insurences.id');;

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
            //->addAction(['width' => '80px'])
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
            'transaction_date',
            'agent_id' => ['name' => 'insurences.agent_id', 'title' => 'Agent'],
            'policy_no',
            'transaction_id',
            'type',
            'card_no',
            'status',
            'aiaRes',
            'description',
            'retried',
            'retried_attempt',
            'retried_transaction_id',
            'amount'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'RecuredTransaction_' . date('YmdHis');
    }
}
