@extends('adminlte::page')

@section('title', 'Bus Assignments')

@section('content_header')
    <h1>Switch From Temporary Card to Branch Card</h1>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('css')
    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
@endsection

@section('content')

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <i class="nav-icon fas fa-exchange-alt"></i> Switch From Temporary Card to Branch Card
                    </div>

                    <div class="card-body">
                        <form id="branchCardSwitchForm">
                            @csrf

                            <div class="form-group">
                                <label for="regiment_no">Regiment Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="regiment_no" name="regiment_no"
                                    placeholder="Enter regiment number" required>
                                <small class="form-text text-muted">Enter the regiment number of the application</small>
                            </div>

                            <div class="form-group">
                                <label for="branch_card_id">Branch Card ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="branch_card_id" name="branch_card_id"
                                    placeholder="Enter branch card ID" required>
                                <small class="form-text text-muted">Enter the branch card ID to switch to</small>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" id="switchBtn">
                                    <i class="fas fa-exchange-alt"></i> Switch
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('footer')
@endsection

@section('js')
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script>
        // Toastr configuration
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        $(document).ready(function() {
            $('#branchCardSwitchForm').on('submit', function(e) {
                e.preventDefault();

                const $btn = $('#switchBtn');
                const originalText = $btn.html();

                // Disable button and show loading
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

                $.ajax({
                    url: '{{ route('branch-card-switch.switch') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#branchCardSwitchForm')[0].reset();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        let message = 'An error occurred while processing your request.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        toastr.error(message);
                    },
                    complete: function() {
                        // Re-enable button
                        $btn.prop('disabled', false).html(originalText);
                    }
                });
            });
        });
    </script>
@endsection
