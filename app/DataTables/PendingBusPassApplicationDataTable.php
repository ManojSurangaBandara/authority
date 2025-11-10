<?php

namespace App\DataTables;

use App\Models\BusPassApplication;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PendingBusPassApplicationDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<BusPassApplication> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('type_label', function ($row) {
                return $row->type_label;
            })
            ->addColumn('status_badge', function ($row) {
                return $row->status_badge;
            })
            ->addColumn('applied_date', function ($row) {
                return $row->created_at ? $row->created_at->format('d M Y') : '';
            })
            ->addColumn('person_rank', function ($row) {
                return $row->person ? $row->person->rank : '';
            })
            // ->addColumn('action', function ($row) {
            //     $viewBtn = '<a href="' . route('bus-pass-applications.show', $row->id) . '" class="btn btn-xs btn-info" title="View"><i class="fas fa-eye"></i></a>';
            //     $editBtn = '<a href="' . route('bus-pass-applications.edit', $row->id) . '" class="btn btn-xs btn-primary mx-1" title="Edit"><i class="fas fa-edit"></i></a>';
            //     $deleteBtn = '<form action="' . route('bus-pass-applications.destroy', $row->id) . '" method="POST" style="display:inline">
            //         ' . csrf_field() . '
            //         ' . method_field("DELETE") . '
            //         <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure you want to delete this application?\')" title="Delete">
            //             <i class="fas fa-trash"></i>
            //         </button>
            //     </form>';

            //     return $viewBtn . $editBtn . $deleteBtn;
            // })
            ->filter(function ($query) {
                if ($estId = request('establishment_id')) {
                    $query->where('establishment_id', $estId);
                }
            })
            ->rawColumns(['action', 'status_badge', 'type_label', 'applied_date', 'person_rank'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<BusPassApplication>
     */
    public function query(BusPassApplication $model): QueryBuilder
    {
        $query = $model->newQuery()->with(['person', 'establishment'])->whereIn('status', [
            'pending_subject_clerk',
            'pending_staff_officer_branch',
            'pending_staff_officer_2_mov',
            'pending_col_mov',
            'pending',
        ]);

        // Filter by establishment for branch users
        $user = Auth::user();
        $branchRoles = ['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)'];
        if ($user && $user->hasAnyRole($branchRoles) && $user->establishment_id) {
            $query->where('establishment_id', $user->establishment_id);
        }

        return $query->orderBy('bus_pass_applications.created_at', 'desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('bus-pass-application-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                // Button::make('reset'),
                // Button::make('reload')
            ])
            ->ajax([
                'data' => "function(d) {
                    d.establishment_id = $('#establishment_id').val();
                }"
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->searchable(false)->orderable(false),
            Column::make('person.regiment_no')->title('Regiment No')->name('person.regiment_no'),
            Column::make('person.name')->title('Name')->name('person.name'),
            Column::make('person_rank')->title('Rank')->searchable(false),
            Column::make('establishment.name')->title('Establishment')->name('establishment.name'),
            Column::make('type_label')->title('Pass Type')->searchable(false),
            Column::make('status_badge')->title('Status')->searchable(false)->orderable(false),
            Column::make('applied_date')->title('Applied Date')->searchable(false)->orderable(false),
            // Column::computed('action')
            //     ->exportable(false)
            //     ->printable(false)
            //     ->width(120)
            //     ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'BusPassApplication_' . date('YmdHis');
    }
}
