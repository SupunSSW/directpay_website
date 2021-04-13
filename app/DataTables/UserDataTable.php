<?php

namespace App\DataTables;

use App\Models\Auth\User;
use App\Repositories\Backend\Auth\UserRepository;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
{
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
            ->editColumn('confirmed', function ($users) {
                if ($users->confirmed) {
                    return '<span class="badge badge-success">Yes</span>';
                } else {
                    return '<span class="badge badge-danger">No</span>';
                }
            })
            ->addColumn('action', function ($users) {
                return User::find($users->id)->getActionButtonsAttribute();
            })
            ->addColumn('role', function ($users){
                return User::find($users->id)->getRolesLabelAttribute();
            })
            ->rawColumns(['confirmed', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        return $this->userRepository->getAllUsers();
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
            'first_name',
            'last_name',
            'email',
            'confirmed',
            'role' => ['title' => 'Role'],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'User_' . date('YmdHis');
    }
}
