<?php
namespace App\DataTables;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Query\Builder;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Engines\BaseEngine;
use Yajra\DataTables\Services\DataTable;

class DataTableBase extends DataTable {

    /*
     * The reason why this class exists is that Yajra/Datatables requires this service class for export methods to work
     * properly.
     */

    /** @var Builder The query that will be used to get the data from the db. */
    private $mQuery;

    /** @var array An array of columns */
    private $mColumns;

    /** @var BaseEngine The DataTable */
    private $mDataTable;


    public function __construct($query, $dataTable, $columns) {
        //parent::__construct(app(Datatables::class), app(Factory::class));

        $this->mQuery = $query;
        $this->mColumns = $columns;
        $this->mDataTable = $dataTable;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax() {
        return $this->mDataTable->make(true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Support\Collection
     */
    public function query() {
        return $this->mQuery;
    }

    public function html() {
        return $this->builder()
            ->columns($this->mColumns)->parameters([
            'dom' => 'Bflr<"toolbar">tip',
            'order' => [[0, 'desc']],
            'responsive' => true,
            'language'=> [
                'processing' => "<img src='https://cdn.directpay.lk/dev/assets/processing.gif'> Loading..."
            ],
            'buttons' => ['print', 'reload'],
            'destroy' => 'true',
            'lengthMenu' => [[10, 25, 50, -1], [10, 25, 50, "All"]],
            'orderable'      => true,
            'searchable'     => true,
            'exportable'     => true,
            'printable'      => true,
            'visible'        => true,
        ]);
    }

}
