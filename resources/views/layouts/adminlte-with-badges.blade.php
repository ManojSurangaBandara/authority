@extends('adminlte::page')

{{-- Override the adminlte_js section to always include our pending approvals script --}}
@section('adminlte_js')
    @parent
    @include('partials.pending-approvals-script')
    @stack('custom-js')
@endsection
