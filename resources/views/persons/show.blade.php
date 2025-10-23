@extends('adminlte::page')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                <div class="card mt-3">
                    <div class="card card-teal">
                        <div class="card-header">
                            <i class="nav-icon fas fa-user nav-icon"></i> {{ __('View Person Details') }}
                            <a href="{{ route('persons.index') }}" class="btn btn-sm btn-dark float-right">Back to List</a>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%">Regiment Number</th>
                                        <td>{{ $person->regiment_no }}</td>
                                    </tr>
                                    <tr>
                                        <th>Rank</th>
                                        <td>{{ $person->rank ? $person->rank->abb_name : 'Not specified' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Name</th>
                                        <td>{{ $person->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Unit</th>
                                        <td>{{ $person->unit }}</td>
                                    </tr>
                                    <tr>
                                        <th>NIC</th>
                                        <td>{{ $person->nic }}</td>
                                    </tr>
                                    <tr>
                                        <th>Army ID</th>
                                        <td>{{ $person->army_id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Permanent Address</th>
                                        <td>{{ $person->permanent_address }}</td>
                                    </tr>
                                    <tr>
                                        <th>Telephone No</th>
                                        <td>{{ $person->telephone_no }}</td>
                                    </tr>
                                    <tr>
                                        <th>Grama Seva Division</th>
                                        <td>{{ $person->grama_seva_division }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nearest Police Station</th>
                                        <td>{{ $person->nearest_police_station }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            {{-- <div class="mt-3">
                                <a href="{{ route('persons.edit', $person->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('persons.destroy', $person->id) }}" method="POST"
                                    style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this person?')">
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
