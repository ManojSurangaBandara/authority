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
                        <div class="card-header"><i class="nav-icon fas fa-building"></i>{{ __('Edit Establishment') }}</div>
                        <div class="card-body">
                            <form action="{{ route('establishment.update', $establishment->id) }}" method="POST"
                                id="establishmentForm">
                                @csrf
                                @method('PUT')
                                <!-- Establishment Selection -->
                                <div class="mb-3">
                                    <label for="establishment_search">Establishment <span
                                            class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <input type="text" id="establishment_search" class="form-control"
                                            placeholder="Search for establishment..." autocomplete="off" required>
                                        <input type="hidden" name="name" id="name"
                                            value="{{ $establishment->name }}" required>
                                        <div id="establishment_dropdown" class="dropdown-menu w-100"
                                            style="max-height: 200px; overflow-y: auto; display: none;">
                                        </div>
                                    </div>
                                    <small class="text-muted">Selected: <span
                                            id="establishment_selected">{{ $establishment->name }}</span></small>
                                    <small class="form-text text-info">The establishment abbreviation will be stored as the
                                        name.</small>
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                    <a href="{{ route('establishment.index') }}" class="btn btn-sm btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('footer')
@endsection

@section('css')
    <style>
        .dropdown-menu {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            z-index: 1000;
        }

        .dropdown-item {
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-bottom: 1px solid #eee;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .dropdown-item:last-child {
            border-bottom: none;
        }

        .dropdown-item.active {
            background-color: #007bff;
            color: white;
        }

        .loading {
            padding: 0.5rem 1rem;
            text-align: center;
            color: #6c757d;
        }
    </style>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            let establishmentData = [];
            let currentEstablishmentName = '{{ $establishment->name }}';

            // Fetch establishment data from API
            function fetchEstablishments() {
                $.ajax({
                    url: 'https://str.army.lk/api/get_establishments/?str-token=1189d8dde195a36a9c4a721a390a74e6',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        establishmentData = data;
                        console.log('Establishments loaded:', data.length);

                        // Find current establishment and set the search field
                        const currentEst = data.find(item =>
                            item.abb.replace(/&#039;/g, "'").replace(/&amp;/g, "&") ===
                            currentEstablishmentName
                        );

                        if (currentEst) {
                            const displayName = currentEst.name.replace(/&#039;/g, "'").replace(
                                /&amp;/g, "&");
                            $('#establishment_search').val(displayName);
                        }
                    },
                    error: function() {
                        console.error('Failed to fetch establishments');
                        showAlert('Failed to load establishments data', 'error');
                    }
                });
            }

            // Initialize
            fetchEstablishments();

            // Show loading indicator
            function showLoading(dropdownId) {
                $(dropdownId).html('<div class="loading">Loading...</div>').show();
            }

            // Establishment search
            $('#establishment_search').on('input', function() {
                const searchTerm = $(this).val().toLowerCase().trim();

                if (searchTerm.length < 2) {
                    $('#establishment_dropdown').hide();
                    return;
                }

                showLoading('#establishment_dropdown');

                // Add small delay for better UX
                setTimeout(() => {
                    const filteredEstablishments = establishmentData.filter(item =>
                        item.name.toLowerCase().includes(searchTerm) ||
                        item.abb.toLowerCase().includes(searchTerm)
                    );

                    displayEstablishmentDropdown(filteredEstablishments);
                }, 300);
            });

            // Show dropdown on focus if there's text
            $('#establishment_search').on('focus', function() {
                const searchTerm = $(this).val().toLowerCase().trim();
                if (searchTerm.length >= 2) {
                    $(this).trigger('input');
                }
            });

            function displayEstablishmentDropdown(establishments) {
                const dropdown = $('#establishment_dropdown');
                dropdown.empty();

                if (establishments.length === 0) {
                    dropdown.append('<div class="dropdown-item">No establishments found</div>');
                } else {
                    establishments.forEach(item => {
                        const displayName = item.name.replace(/&#039;/g, "'").replace(/&amp;/g, "&");
                        const displayAbb = item.abb.replace(/&#039;/g, "'").replace(/&amp;/g, "&");

                        dropdown.append(`
                    <div class="dropdown-item" data-id="${item.id}" data-name="${displayName}" data-abb="${displayAbb}">
                        <strong>${displayAbb}</strong><br>
                        <small class="text-muted">${displayName}</small>
                    </div>
                `);
                    });
                }

                dropdown.show();
            }

            // Establishment selection
            $(document).on('click', '#establishment_dropdown .dropdown-item', function() {
                if (!$(this).data('name')) return;

                const name = $(this).data('name');
                const abb = $(this).data('abb');

                $('#establishment_search').val(name);
                $('#name').val(abb); // Store abbreviation instead of full name
                $('#establishment_selected').text(`${abb} (${name})`);
                $('#establishment_dropdown').hide();
            });

            // Hide dropdowns when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.position-relative').length) {
                    $('.dropdown-menu').hide();
                }
            });

            // Form submission validation
            $('#establishmentForm').on('submit', function(e) {
                const establishmentName = $('#name').val();

                if (!establishmentName.trim()) {
                    e.preventDefault();
                    showAlert('Please select an establishment from the dropdown', 'error');
                    return false;
                }
            });

            function showAlert(message, type) {
                const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
                const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

                // Insert alert at the top of the form
                $('.card-body').prepend(alertHtml);

                // Auto dismiss after 5 seconds
                setTimeout(() => {
                    $('.alert').fadeOut();
                }, 5000);
            }
        });
    </script>
@endsection
