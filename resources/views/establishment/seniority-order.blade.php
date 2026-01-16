@extends('adminlte::page')

@section('title', 'Establishment Seniority Order')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-sort-numeric-up"></i> Establishment Seniority Order</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('establishment.index') }}">Establishments</a></li>
                <li class="breadcrumb-item active">Seniority Order</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-sort-numeric-up"></i> Manage Establishment Seniority Order
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Drag and drop establishments to change their seniority order. Higher positions
                            (top) have higher seniority.</p>

                        <div class="row">
                            <div class="col-md-8">
                                <div id="establishments-list">
                                    @foreach ($establishments as $establishment)
                                        <div class="list-group-item d-flex justify-content-between align-items-center establishment-item"
                                            data-id="{{ $establishment->id }}" draggable="true">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-grip-vertical handle mr-3 text-muted"
                                                    style="cursor: grab;"></i>
                                                <strong>{{ $establishment->name }}</strong>
                                                @if ($establishment->seniority_order)
                                                    <span class="badge badge-secondary ml-2">Order:
                                                        {{ $establishment->seniority_order }}</span>
                                                @else
                                                    <span class="badge badge-warning ml-2">Not Set</span>
                                                @endif
                                            </div>
                                            <small
                                                class="text-muted">{{ $establishment->is_active ? 'Active' : 'Inactive' }}</small>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Instructions</h5>
                                    </div>
                                    <div class="card-body">
                                        <ul>
                                            <li>Drag establishments up or down to change their seniority order</li>
                                            <li>The top establishment has the highest seniority (1)</li>
                                            <li>The bottom establishment has the lowest seniority</li>
                                            <li>Click "Save Order" to apply changes</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary" id="saveOrderBtn">
                            <i class="fas fa-save"></i> Save Order
                        </button>
                        <a href="{{ route('establishment.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Establishments
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .establishment-item {
            cursor: move;
            transition: all 0.2s ease;
        }

        .establishment-item:hover {
            background-color: #f8f9fa;
        }

        .establishment-item.dragging {
            opacity: 0.5;
            transform: rotate(5deg);
        }

        .establishment-item.drag-over {
            border: 2px dashed #007bff;
            background-color: #e7f3ff;
        }

        .handle {
            cursor: grab;
        }

        .handle:active {
            cursor: grabbing;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            let draggedElement = null;

            // Drag start event
            document.addEventListener('dragstart', function(e) {
                if (e.target.classList.contains('establishment-item')) {
                    draggedElement = e.target;
                    e.target.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/html', e.target.outerHTML);
                }
            });

            // Drag end event
            document.addEventListener('dragend', function(e) {
                if (e.target.classList.contains('establishment-item')) {
                    e.target.classList.remove('dragging');
                    // Remove drag-over class from all items
                    document.querySelectorAll('.establishment-item').forEach(item => {
                        item.classList.remove('drag-over');
                    });
                }
            });

            // Drag over event
            document.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';

                if (e.target.classList.contains('establishment-item')) {
                    // Remove drag-over from all items
                    document.querySelectorAll('.establishment-item').forEach(item => {
                        item.classList.remove('drag-over');
                    });
                    // Add drag-over to current item
                    e.target.classList.add('drag-over');
                }
            });

            // Drop event
            document.addEventListener('drop', function(e) {
                e.preventDefault();

                if (e.target.classList.contains('establishment-item') && draggedElement &&
                    draggedElement !== e.target) {
                    const allItems = Array.from(document.querySelectorAll('.establishment-item'));
                    const draggedIndex = allItems.indexOf(draggedElement);
                    const targetIndex = allItems.indexOf(e.target);

                    if (draggedIndex < targetIndex) {
                        // Moving down - insert after target
                        e.target.parentNode.insertBefore(draggedElement, e.target.nextSibling);
                    } else {
                        // Moving up - insert before target
                        e.target.parentNode.insertBefore(draggedElement, e.target);
                    }
                }

                // Clean up
                document.querySelectorAll('.establishment-item').forEach(item => {
                    item.classList.remove('drag-over');
                });
                draggedElement = null;
            });

            // Save order button
            $('#saveOrderBtn').on('click', function() {
                var establishmentIds = [];
                $('.establishment-item').each(function() {
                    establishmentIds.push($(this).data('id'));
                });

                $.ajax({
                    url: '{{ route('establishment.update-seniority-order') }}',
                    method: 'POST',
                    data: {
                        establishments: establishmentIds,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            // Refresh the page to show updated order numbers
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            toastr.error('Failed to update order');
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error('Error updating order: ' + error);
                    }
                });
            });
        });
    </script>
@stop
