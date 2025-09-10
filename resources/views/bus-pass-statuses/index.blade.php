@extends('adminlte::page')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card">
                    <div class="card card-teal">
                        <div class="card-header">
                            <i class="nav-icon fas fa-tags nav-icon"></i> {{ __('Bus Pass Statuses') }}
                            <div class="float-right">
                                <a href="{{ route('bus-pass-statuses.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add New Status
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            @if($statuses->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th width="10%">#</th>
                                                <th width="15%">Code</th>
                                                <th width="20%">Label</th>
                                                <th width="15%">Badge Preview</th>
                                                <th width="20%">Description</th>
                                                <th width="10%">Sort Order</th>
                                                <th width="10%">Status</th>
                                                <th width="15%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($statuses as $status)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td><code>{{ $status->code }}</code></td>
                                                    <td>{{ $status->label }}</td>
                                                    <td>{!! $status->badge_html !!}</td>
                                                    <td>{{ $status->description ?? 'N/A' }}</td>
                                                    <td>{{ $status->sort_order }}</td>
                                                    <td>
                                                        @if($status->is_active)
                                                            <span class="badge badge-success">Active</span>
                                                        @else
                                                            <span class="badge badge-secondary">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('bus-pass-statuses.show', $status) }}" class="btn btn-info btn-sm" title="View">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('bus-pass-statuses.edit', $status) }}" class="btn btn-warning btn-sm" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <form action="{{ route('bus-pass-statuses.destroy', $status) }}" method="POST" style="display:inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm" 
                                                                    onclick="return confirm('Are you sure you want to delete this status?')" title="Delete">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-muted">No statuses found.</p>
                                    <a href="{{ route('bus-pass-statuses.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add First Status
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
