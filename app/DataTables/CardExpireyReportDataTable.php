<?php

namespace App\DataTables;

use App\Models\insurence;
use App\User;
use Carbon\Carbon;
use DB;
use Yajra\DataTables\Services\DataTable;

class CardExpireyReportDataTable extends DataTable
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
            ->addColumn('action', function ($data) {
                $year = Carbon::now()->format('y');
                $month = Carbon::now()->month;
                return view('backend.Reports.cardActionBtn')->with([
                    'data' => $data,
                    'month' => $month,
                    'year' => $year
                ]);
            })->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(insurence $model)
    {
        $month = Carbon::now()->format('m');
        $year = Carbon::now()->format('y');
        $monthWithYear = $month . '-' . $year;

        $insData = $model->newQuery()->select('id', 'police_no', 'card_no', 'card_exp', 'sec_card_no', 'sec_card_exp',
            'created_at', DB::raw("card_exp <= \"$monthWithYear\" AS expired_pri"), DB::raw("sec_card_exp <= \"$monthWithYear\" AS expired_sec"))->where('card_exp', '!=', '-');

        if ($this->request()->has('policyNo')) {
            $poNo = $this->request()->get('policyNo');
            $insData->where('policy_no', "$poNo");
        }

        if ($this->request()->has('cardNo')) {
            $cardNo = 'xxxx-xxxx-xxxx-' . $this->request()->get('cardNo');
            $insData->where('card_no', "$cardNo")
                ->orWhere('sec_card_no', "$cardNo");
        }

        if ($this->request()->has('month')) {
            $month = $this->request()->get('month');
            $year = Carbon::now()->format('y');
            $insData->where('card_exp', 'like', "$month" . '-' . "$year")
                ->orWhere('sec_card_exp', 'like', "$month" . '-' . "$year");
        }

        if ($this->request()->has('year')) {
            $year = $this->request()->get('year');
            $insData->where('card_exp', 'like', '%' . "$year" . '%')
                ->orWhere('sec_card_exp', 'like', '%' . "$year" . '%');
        }

        if ($this->request()->has('expired')) {
            $year = Carbon::now()->format('y');
            $month = Carbon::now()->month;
            $insData
                ->where(\DB::raw('substring_index(card_exp, \'-\', -1)'), '<=', $year)
                ->where(\DB::raw('substring_index(card_exp, \'-\', 1)'), '<=', $month);
                //->orWhere(\DB::raw('substring_index(sec_card_exp, \'-\', -1)'), '<=', $year)
                //->orWhere(\DB::raw('substring_index(sec_card_exp, \'-\', 1)'), '<=', $month);
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
            'police_no',
            'card_no',
            'card_exp',
            'sec_card_no' => ['title' => 'Secondary Card #'],
            'sec_card_exp' => ['title' => 'Secondary Card Exp'],
            'action'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'CardExpireyReport_' . date('YmdHis');
    }
}
