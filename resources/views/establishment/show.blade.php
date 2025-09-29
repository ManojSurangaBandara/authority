@extends('adminlte::page')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            <div class="card mt-3">
                <div class="card card-teal">
                    <div class="card-header">
                        <i class="nav-icon fas fa-building"></i>{{ __('View Establishment Details') }}
                        <a href="{{ route('establishment.index') }}" class="btn btn-sm btn-dark float-right">Back to List</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th style="width: 30%">Name</th>
                                    <td>{{ $establishment->name }}</td>
                                </tr>
                            </tbody>
                        </table>

                        {{-- <div class="mt-3">
                            <a href="{{ route('establishment.edit', $establishment->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('establishment.destroy', $establishment->id) }}" method="POST"
                                style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this establishment?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('footer')
@endsection
