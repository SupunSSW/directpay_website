<?php

namespace App\DataTables;

use App\Models\Transaction;
use App\User;
use Yajra\DataTables\Services\DataTable;

class DeliveriesDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Transaction $model)
    {
        $query = $model->newQuery()->select(
            'transactions.id',
            'transactions.tracking_no',
            'transactions.name',
            'transactions.address',
            'transactions.mobile',
            'transactions.amount',
            'transactions.type',
            'transactions.agent_email',
            'users.agent_hub',
            'transactions.status',
            'transactions.created_at')
            ->join('users', 'users.id', '=', 'transactions.agent_id');

        if($this->request->has("trackingNo")){
            $query->where('transactions.tracking_no', $this->request->get('trackingNo'));
        }

        if ($this->request()->has('dateRange')) {
            $dateRange = $this->request()->get('dateRange');
            $dateRange = explode(":", $dateRange);
            if (count($dateRange) > 1) {
                $from = $dateRange[0] . ' 00:00:00';
                $to = $dateRange[1] . ' 23:59:59';
                $query->whereBetween('transactions.created_at', [$from, $to]);
            } else {
                $dateRange = $this->request()->get('dateRange');
                $from = $dateRange . ' 00:00:00';
                $to = $dateRange . ' 23:59:59';
                $query->whereBetween('transactions.created_at', [$from, $to]);
            }
        }

        if($this->request->has('type')){
            $query->where('transactions.type', $this->request->get('type'));
        }

        if($this->request->has('status')){
            $query->where('transactions.status', $this->request->get('status'));
        }

        return $query;
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
//            ->addAction(['width' => '80px'])
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
            'id',
            'tracking_no',
            'name',
            'address',
            'mobile',
            'amount',
            'type',
            'agent_email',
            'agent_hub' => ['searchable' => false],
            'status',
            'created_at',
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Deliveries_' . date('YmdHis');
    }
}
