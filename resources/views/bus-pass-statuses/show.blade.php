@extends('adminlte::page')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card">
                    <div class="card card-teal">
                        <div class="card-header">
                            <i class="nav-icon fas fa-tags nav-icon"></i> {{ __('Bus Pass Status Details') }}
                            <div class="float-right">
                                {!! $busPassStatus->badge_html !!}
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Status Code:</strong><br>
                                    <code>{{ $busPassStatus->code }}</code>
                                </div>
                                <div class="col-md-6">
                                    <strong>Display Label:</strong><br>
                                    {{ $busPassStatus->label }}
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <strong>Badge Color:</strong><br>
                                    <span class="badge badge-{{ $busPassStatus->badge_color }}">{{ ucfirst($busPassStatus->badge_color) }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Sort Order:</strong><br>
                                    {{ $busPassStatus->sort_order }}
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <strong>Status:</strong><br>
                                    @if($busPassStatus->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <strong>Created:</strong><br>
                                    {{ $busPassStatus->created_at->format('d M Y, h:i A') }}
                                </div>
                            </div>

                            @if($busPassStatus->description)
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <strong>Description:</strong><br>
                                        {{ $busPassStatus->description }}
                                    </div>
                                </div>
                            @endif

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <strong>Badge Preview:</strong><br>
                                    {!! $busPassStatus->badge_html !!}
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <a href="{{ route('bus-pass-statuses.edit', $busPassStatus) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Status
                            </a>
                            <a href="{{ route('bus-pass-statuses.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <form action="{{ route('bus-pass-statuses.destroy', $busPassStatus) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Are you sure you want to delete this status?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
