<?php

namespace App\DataTables;

use App\Models\FileManager;
use App\User;
use Yajra\DataTables\Services\DataTable;

class uploadsReportDataTable extends DataTable
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
            ->editColumn('data', function ($data) {
                return '
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th scope="col">Column</th>
                      <th scope="col">Previous Value</th>
                      <th scope="col">New Value</th>
                      <th scope="col">Description</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <th scope="row">'.$data->name.'</th>
                      <td>'.$data->previousValue.'</td>
                      <td>'.$data->newValue.'</td>
                      <td>'.$data->description.'</td>
                    </tr>
                  </tbody>
                </table>';
            })
            ->rawColumns(['data']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(FileManager $model)
    {
        return $model->newQuery()->select(['*']);
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
            'created_at' => ['title' => 'Date'],
            'policy_no',
            'fileName',
            'user',
            'data'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'uploadsReport_' . date('YmdHis');
    }
}
