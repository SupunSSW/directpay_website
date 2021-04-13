<?php

namespace App\DataTables;

use App\Models\FileManager;
use App\User;
use Carbon\Carbon;
use Yajra\DataTables\Services\DataTable;

class fileManagerDataTable extends DataTable
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
            ->editColumn('type', function($data){
                return $data->type == FileManager::UPLOAD ?
                    '<h6><span class="badge badge-success"><i class="fas fa-file-upload"></i> Upload</span></h6>'
                    :
                    '<h6><span class="badge badge-primary"><i class="fas fa-file-download"></i> Download</span></h6>';
            })
            ->editColumn('description', function($data){
                return $data->description ? $data->description : '-';
            })
//            ->editColumn('path', function ($data){
//                return '<a href="/storage/app/aiaUploads/'.$data->path.'" class="btn btn-secondary btn-sm" role="button" aria-pressed="true">File</a>';
//            })
            ->rawColumns(['type','path']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(FileManager $model)
    {
        $date = Carbon::now()->format('Y-m-d');
        $from = $date.' 00:00:00';
        $to = $date.' 23:59:59';
        return $model->newQuery()->select(['*'])
            ->whereBetween('updated_at',[$from, $to]);
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
            'updated_at',
            'user',
            'fileName',
            'type',
            'policy_no',
            'name',
            'previousValue',
            'newValue',
            'description',
//            'path'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'fileManager_' . date('YmdHis');
    }
}
