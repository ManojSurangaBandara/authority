@extends('adminlte::page')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card">
                    <div class="card card-warning">
                        <div class="card-header">
                            <i class="nav-icon fas fa-key nav-icon"></i> {{ __('Change Password') }}
                            <a href="{{ route('profile.show') }}" class="btn btn-sm btn-secondary float-right">
                                <i class="fas fa-arrow-left"></i> Back to Profile
                            </a>
                        </div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('profile.update-password') }}">
                                @csrf

                                <div class="form-group">
                                    <label for="current_password">{{ __('Current Password') }} <span
                                            class="text-danger">*</span></label>
                                    <input id="current_password" type="password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        name="current_password" required autocomplete="current-password">
                                    @error('current_password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password">{{ __('New Password') }} <span
                                            class="text-danger">*</span></label>
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="new-password">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Password must be at least 8 characters long.
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation">{{ __('Confirm New Password') }} <span
                                            class="text-danger">*</span></label>
                                    <input id="password_confirmation" type="password"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        name="password_confirmation" required autocomplete="new-password">
                                    @error('password_confirmation')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group mb-0">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> {{ __('Update Password') }}
                                    </button>
                                    <a href="{{ route('profile.show') }}" class="btn btn-secondary ml-2">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="text-info"><i class="fas fa-shield-alt"></i> Password Security Guidelines:</h6>
                        <ul class="text-muted small">
                            <li>Use a strong password with at least 8 characters</li>
                            <li>Include a mix of uppercase and lowercase letters</li>
                            <li>Add numbers and special characters for better security</li>
                            <li>Avoid using personal information or common words</li>
                            <li>Don't share your password with anyone</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('footer')
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Add password visibility toggle functionality
            $('<button type="button" class="btn btn-outline-secondary btn-sm" id="toggleCurrentPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 10;"><i class="fas fa-eye"></i></button>')
                .insertAfter('#current_password');
            $('<button type="button" class="btn btn-outline-secondary btn-sm" id="toggleNewPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 10;"><i class="fas fa-eye"></i></button>')
                .insertAfter('#password');
            $('<button type="button" class="btn btn-outline-secondary btn-sm" id="toggleConfirmPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 10;"><i class="fas fa-eye"></i></button>')
                .insertAfter('#password_confirmation');

            // Make password fields container relative
            $('#current_password, #password, #password_confirmation').parent().css('position', 'relative');

            // Toggle password visibility
            $('#toggleCurrentPassword').click(function() {
                const passwordField = $('#current_password');
                const icon = $(this).find('i');
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            $('#toggleNewPassword').click(function() {
                const passwordField = $('#password');
                const icon = $(this).find('i');
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            $('#toggleConfirmPassword').click(function() {
                const passwordField = $('#password_confirmation');
                const icon = $(this).find('i');
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
        });
    </script>
@endsection
