@extends('adminlte::page')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="card mt-3">
                <div class="card card-teal">
                    <div class="card-header">
                        <i class="fas fa-fw fa-landmark"></i> View Gs Devision
                        <a href="{{ route('gs-devision.index') }}" class="btn btn-sm btn-dark float-right">Back</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th style="width: 30%">Gs Devision Name</th>
                                    <td>{{ $gs_devision->name }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@include('footer')
@endsection
