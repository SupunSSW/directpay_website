<?php

namespace App\DataTables;

use App\Models\Item;
use App\User;
use Carbon\Carbon;
use Yajra\DataTables\Services\DataTable;

class PolicyPaymentDataTable extends DataTable
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
            ->editColumn('isRecurring', function ($data) {
                if ($data->isRecurring == 1) {
                    return 'Recurring Payment';
                } else {
                    return 'One time payment';
                }
            })
            ->editColumn('status', function ($data) {
                $date = Carbon::parse($data->updated_at);
                $now = Carbon::now();
                $diff = $date->diffInHours($now);

                if ($diff >= 24) {
                    return '<h6><span class="badge badge-warning">Expire</span></h6>';
                } else if ($data->status == Item::SUCCESS) {
                    return '<h6><span class="badge badge-success">Success</span></h6>';
                } else if ($data->status == Item::FAILED) {
                    return '<h6><span class="badge badge-danger">Failed</span></h6>';
                } else if ($data->status == Item::OPEN) {
                    return '<h6><span class="badge badge-info">Open</span></h6>';
                } else if ($data->status != Item::EXPIRE && $data->status == Item::CREATE) {
                    return '<h6><span class="badge badge-light">New</span></h6>';
                } else if ($data->status == Item::SMS_FAILED) {
                    return '<h6><span class="badge badge-danger">SMS failed</span></h6>';
                } else if ($data->status == Item::POLICY_VALIDATE_CLICK) {
                    return '<h6><span class="badge badge-info">Policy no validate click</span></h6>';
                } else if ($data->status == Item::POLICY_VALIDATE_SUCCESS) {
                    return '<h6><span class="badge badge-info">Policy no validate success</span></h6>';
                } else if ($data->status == Item::POLICY_VALIDATE_FAILED) {
                    return '<h6><span class="badge badge-info">Policy no validate failed</span></h6>';
                } else if ($data->status == Item::CONCENT_CLICK) {
                    return '<h6><span class="badge badge-info">Consent click</span></h6>';
                }
            })->rawColumns(['status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Item $model)
    {
        $data = $model->newQuery();
        if (\auth()->user()->hasRole(['administrator'])) {
            $data->select(['*']);
        } else {
            $data->select(['*'])->where('user', \auth()->user()->email);
        }

        if ($this->request()->has('dateRange')) {
            $dateRange = $this->request()->get('dateRange');
            $dateRange = explode(":", $dateRange);
            if (count($dateRange) > 1) {
                $from = $dateRange[0] . ' 00:00:00';
                $to = $dateRange[1] . ' 23:59:59';
                $data->whereBetween('created_at', [$from, $to]);
            } else {
                $dateRange = $this->request()->get('dateRange');
                $from = $dateRange . ' 00:00:00';
                $to = $dateRange . ' 23:59:59';
                $data->whereBetween('created_at', [$from, $to]);
            }
        } else {
            $date = Carbon::now()->format('Y-m-d');
            $from = $date . ' 00:00:00';
            $to = $date . ' 23:59:59';
            $data->whereBetween('created_at', [$from, $to]);
        }


        return $data;
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
            'created_at',
            'user',
            'policeNo' => ['title' => 'Policy No'],
            //'customerMobile',
            'isRecurring' => ['title' => 'Type'],
            'amount' => ['title' => 'Due/One time Amount'],
            'preAmount' => ['title' => 'Premium Amount'],
            'linkId' => ['title' => 'Link'],
            'status'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'PolicyPayment_' . date('YmdHis');
    }
}
