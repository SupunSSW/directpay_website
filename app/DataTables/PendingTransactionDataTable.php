<?php

namespace App\DataTables;

use App\Models\insurence;
use App\tempEdit;
use App\User;
use Carbon\Carbon;
use Yajra\DataTables\Services\DataTable;

class PendingTransactionDataTable extends DataTable
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
            ->editColumn('withPrAmount', function ($data) {
                return number_format($data->withPrAmount, 2, '.', ',');
            })
            ->editColumn('amount', function ($data) {
                return number_format($data->amount, 2, '.', ',');
            })
            ->editColumn('next_payment_amount', function ($data) {
                return number_format($data->next_payment_amount, 2, '.', ',');
            })
            ->editColumn('user', function ($data){
                if($data->user){
                    return $data->user;
                } else{
                    return '-';
                }
            })
            ->addColumn('action', function ($data) {
                $end = Carbon::parse($data['created_at']);
                $now = Carbon::now();
                $length = $end->diffInDays($now);
                $tempData = null;
                if ($data->isEdit) {
                    $tempData = tempEdit::find($data->isEdit);
                }
                return view('backend.transaction.pendingAction')->with([
                    'data' => $data, 'dateLength' => $length, 'tempData' => $tempData
                ]);
            })
            ->addColumn('status', function ($data) {
                if ($data->status == 'active') {
                    if ($data->interval) {
                        if (\auth()->user()->hasRole(['administrator']) || \auth()->user()->can('Edit') || \auth()->user()->can('approve')) {
                            if ($data->isEdit) {
                                $tmpData = tempEdit::find($data->isEdit);
                                if ($tmpData->status && $tmpData->status != 'deleted') {
                                    if ($tmpData->status == tempEdit::TYPE_PENDING && \auth()->user()->hasRole(['administrator']) || (\auth()->user()->can('Edit') && \auth()->user()->can('approve'))) {
                                        return '
                                            <h6><span class="badge badge-success">Active</span></h6>
                                            <button type="button" class="btn btn-sm btn-warning btnPendingActive" value="' . $tmpData->id . '">Pending Inactive</button>
                                        ';
                                    }
                                    if ($tmpData->status == tempEdit::TYPE_REJECT && \auth()->user()->hasRole(['administrator']) || \auth()->user()->can('Edit')) {
                                        return '
                                            <h6><span class="badge badge-success">Active</span></h6>
                                            <button type="button" class="btn btn-sm btn-danger btnInactive" value="' . $data->id . '">Inactive</button>
                                        ';
                                    } else if ($tmpData->status == tempEdit::TYPE_PENDING && \auth()->user()->can('Edit')) {
                                        return '
                                            <h6><span class="badge badge-success">Active</span></h6>
                                            <button type="button" class="btn btn-sm btn-warning" disabled >Pending Inactive</button>
                                        ';
                                    } else if ($tmpData->status == tempEdit::TYPE_PENDING && \auth()->user()->hasRole(['administrator']) || \auth()->user()->can('approve')) {
                                        return '
                                            <h6><span class="badge badge-success">Active</span></h6>
                                            <button type="button" class="btn btn-sm btn-warning btnPendingActive" value="' . $tmpData->id . '">Pending Inactive</button>
                                        ';
                                    } else if ($tmpData->status == tempEdit::TYPE_REJECT && \auth()->user()->can('approve')) {
                                        return '
                                            <h6><span class="badge badge-success">Active</span></h6>
                                        ';
                                    }
                                } else {
                                    if (\auth()->user()->hasRole(['administrator']) || \auth()->user()->can('Edit')) {
                                        return '
                                         <h6><span class="badge badge-success">Active</span></h6>
                                        <button type="button" class="btn btn-sm btn-danger btnInactive" value="' . $data->id . '">Inactive</button>
                                    ';
                                    } else {
                                        return '
                                            <h6><span class="badge badge-success">Active</span></h6>
                                          
                                        ';
                                        //<button type="button" class="btn btn-sm btn-success" disabled>Active</button>
                                    }
                                }
                            } else {
                                if (\auth()->user()->hasRole(['administrator']) || \auth()->user()->can('Edit')) {
                                    return '
                                        <h6><span class="badge badge-success">Active</span></h6>
                                        <button type="button" class="btn btn-sm btn-danger btnInactive" value="' . $data->id . '">Inactive</button>
                                    ';
                                } else {
                                    return '
                                           <h6><span class="badge badge-success">Active</span></h6>
                                        ';
                                    // <button type="button" class="btn btn-sm btn-success" disabled>Active</button>
                                }
                            }
                        } else {
                            return '
                                <h6><span class="badge badge-success">Active</span></h6>
                            ';
                        }
                    } else {
                        return '-';
                    }
                } else {
                    if ($data->interval) {
                        if (\auth()->user()->hasRole(['administrator']) || \auth()->user()->can('Edit') || \auth()->user()->can('approve')) {
                            if ($data->isEdit) {
                                $tmpData = tempEdit::find($data->isEdit);
                                if ($tmpData->status && $tmpData->status != 'deleted') {
                                    if ($tmpData->status == tempEdit::TYPE_PENDING && \auth()->user()->hasRole(['administrator']) || (\auth()->user()->can('Edit') && \auth()->user()->can('approve'))) {
                                        return '
                                            <h6><span class="badge badge-danger">Inactive</span></h6>
                                            <button type="button" class="btn btn-sm btn-warning btnPendingActive" value="' . $tmpData->id . '">Pending Active</button>
                                        ';
                                    } else if ($tmpData->status == tempEdit::TYPE_REJECT && \auth()->user()->hasRole(['administrator']) || \auth()->user()->can('Edit')) {
                                        return '
                                            <h6><span class="badge badge-danger">Inactive</span></h6>
                                            <button type="button" class="btn btn-sm btn-success btnActive" value="' . $data->id . '">Inactive</button>
                                        ';
                                    } else if ($tmpData->status == tempEdit::TYPE_PENDING && \auth()->user()->can('Edit')) {
                                        return '
                                            <h6><span class="badge badge-danger">Inactive</span></h6>
                                            <button type="button" class="btn btn-sm btn-warning" disabled >Pending Active</button>
                                        ';
                                    } else if ($tmpData->status == tempEdit::TYPE_PENDING && \auth()->user()->hasRole(['administrator']) || \auth()->user()->can('approve')) {
                                        return '
                                            <h6><span class="badge badge-danger">Inactive</span></h6>
                                            <button type="button" class="btn btn-sm btn-warning btnPendingActive" value="' . $tmpData->id . '">Pending Active</button>
                                        ';
                                    } else if ($tmpData->status == tempEdit::TYPE_REJECT && \auth()->user()->can('approve')) {
                                        return '
                                            <h6><span class="badge badge-danger">Inactive</span></h6>
                                        ';
                                    }
                                } else {
                                    if (\auth()->user()->hasRole(['administrator']) || \auth()->user()->can('Edit')) {
                                        return '
                                        <h6><span class="badge badge-danger">Inactive</span></h6>
                                        <button type="button" class="btn btn-sm btn-success btnActive" value="' . $data->id . '">Active</button>
                                    ';
                                    } else {
                                        return '
                                            <h6><span class="badge badge-danger">Inactive</span></h6>
                                        ';
                                    }
                                }
                            } else {
                                if (\auth()->user()->hasRole(['administrator']) || \auth()->user()->can('Edit')) {
                                    return '
                                        <h6><span class="badge badge-danger">Inactive</span></h6>
                                        <button type="button" class="btn btn-sm btn-success btnActive" value="' . $data->id . '">Active</button>
                                    ';
                                } else {
                                    return '
                                            <h6><span class="badge badge-danger">Inactive</span></h6>
                                        ';
                                }
                            }
                        } else {
                            return '
                                <h6><span class="badge badge-danger">Inactive</span></h6>
                            ';
                        }
                    } else {
                        return '-';
                    }
                }
            })->rawColumns(['status', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(insurence $model)
    {
        return $model->newQuery()->select(
            ['insurences.id', 'insurences.created_at', 'insurences.agent_id', 'insurences.police_no', 'insurences.interval',
                'insurences.premium', 'insurences.amount', 'insurences.card_res', 'insurences.trn_status', 'insurences.aia_res',
                'insurences.next_payment_date', 'insurences.next_payment_amount', 'insurences.card_no', 'insurences.card_exp', 'insurences.sec_card_no',
                'insurences.sec_card_exp', 'insurences.status', 'temp_edits.user', 'insurences.isEdit'
            ]
        )
            ->where('trn_status', 'SUCCESS')
            ->whereNotNull('isEdit')
            ->join('temp_edits', 'insurences.id', '=', 'temp_edits.insurence_id');
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
            ->addAction()
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
            'id' => ['title' => 'Ref ID', 'exportable' => true, 'printable' => true],
            'created_at' => ['title' => 'Date & Time', 'exportable' => true],
            'agent_id' => ['title' => 'Agent', 'exportable' => true],
            'police_no' => ['title' => 'Policy Number', 'exportable' => true],
            'interval' => ['title' => 'Payment Frequency', 'exportable' => true],
            'premium' => ['title' => 'premium', 'exportable' => true],
            'amount' => ['title' => 'Collected Amount', 'exportable' => true],
            //'withPrAmount' => ['title' => 'Recurring Amount', 'exportable' => true],
            'card_res' => ['title' => 'Card Response', 'exportable' => true],
            'trn_status' => ['title' => 'Payment Status', 'exportable' => true],
            'aia_res' => ['title' => 'AIA Response', 'exportable' => true],
            'next_payment_date' => ['title' => 'Next Payment Date', 'exportable' => true],
            'next_payment_amount' => ['title' => 'Next Payment Amount', 'exportable' => true],
            'card_no' => ['title' => 'Card Reference Number', 'exportable' => true],
            'card_exp' => ['title' => 'Card Exp.Date', 'exportable' => true],
            'sec_card_no' => ['title' => 'Optional Card #', 'exportable' => true],
            'sec_card_exp' => ['title' => 'Optional Card Exp', 'exportable' => true],
            'status',
            'user' => ['title' => 'Edited User','name' => 'temp_edits.user']
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'PendingTransaction_' . date('YmdHis');
    }
}
