<?php

namespace App\DataTables\PaymentLink;

use App\Models\Transaction;
use App\User;
use Carbon\Carbon;
use Yajra\DataTables\Services\DataTable;

class PaymentLinkDataTable extends DataTable
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
            ->editColumn('amount', function ($data) {
                return number_format($data->amount, 2, '.', ',');
            })
            ->editColumn('status', function ($data) {
                if ($data->status == Transaction::STATUS_SUCCESS) {
                    return '<h6><span class="badge badge-pill badge-success">' . $data->status . '</span></h6>';
                }
                return '<h6><span class="badge badge-pill badge-danger">' . $data->status . '</span></h6>';
            })
            ->rawColumns(['status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Transaction $model)
    {
        if ($this->request->has('dateRange')) {
            $from = Carbon::parse(explode(':', $this->request->get('dateRange'))[0])->format('Y-m-d H:i:s');
            $to = Carbon::parse(explode(':', $this->request->get('dateRange'))[1])->endOfDay()->format('Y-m-d H:i:s');
        } else {
            $from = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
            $to = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');
        }

        return $model->newQuery()->select(['*'])
            ->where('type', Transaction::TYPE_LINK)
            ->whereBetween('created_at', [$from, $to]);
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
            'id',
            'tracking_no',
            'mobile',
            'amount',
            'agent_email' => [ 'title' => 'Agent'],
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
        return 'LinkPayments_' . date('YmdHis');
    }
}
