<?php

namespace App\DataTables;

use App\Helpers\Auth\Auth;
use App\Models\Auth\User;
use App\Models\insurence;
use App\tempEdit;
use Carbon\Carbon;
use function foo\func;
use Yajra\DataTables\Services\DataTable;

class FirstTransactionDataTable extends DataTable
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
            ->editColumn('next_payment_date', function ($data){
                return Carbon::parse($data->next_payment_date)->format('Y-m-d');
            })
            ->addColumn('action', function ($data) {
                $end = Carbon::parse($data['created_at']);
                $now = Carbon::now();
                $length = $end->diffInDays($now);
                $tempData = null;
                if ($data->isEdit) {
                    $tempData = tempEdit::find($data->isEdit);
                }
                return view('backend.transaction.firstTransactionAction')->with([
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
        $insData = $model->newQuery()->select(['*'])
            ->where('trn_status', 'SUCCESS')
            ->where('status', '!=', 'deleted');

        if ($this->request()->has('dateRange')) {
            \Log::info('daterange');
            $dateRange = $this->request()->get('dateRange');
            $dateRange = explode(":", $dateRange);
            if (count($dateRange) > 1) {
                $from = $dateRange[0] . ' 00:00:00';
                $to = $dateRange[1] . ' 23:59:59';
                $insData->whereBetween('created_at', [$from, $to]);
            } else {
                $dateRange = $this->request()->get('dateRange');
                $from = $dateRange . ' 00:00:00';
                $to = $dateRange . ' 23:59:59';
                $insData->whereBetween('created_at', [$from, $to]);
            }
        }

        if ($this->request()->has('status')) {
            \Log::info('status');
            $insData->where('status', $this->request()->get('status'));
        }

        if ($this->request()->has('policyNo')) {
            \Log::info('policyno');
            $insData->where('police_no', $this->request()->get('policyNo'));
        }

        if ($this->request()->has('agent')) {
            \Log::info('agent');
            $insData->where('agent_id', $this->request()->get('agent'));
        }

        return $insData;
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
        return 'FirstTransaction_' . date('YmdHis');
    }
}
