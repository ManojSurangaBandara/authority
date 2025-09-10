@extends('adminlte::page')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card">
                    <div class="card card-teal">
                        <div class="card-header">
                            <i class="nav-icon fas fa-tags nav-icon"></i> {{ __('Edit Bus Pass Status') }}
                        </div>

                        <form action="{{ route('bus-pass-statuses.update', $busPassStatus) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="code">Status Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                                   id="code" name="code" value="{{ old('code', $busPassStatus->code) }}" required>
                                            <small class="form-text text-muted">Unique identifier (e.g., pending, approved)</small>
                                            @error('code')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="label">Display Label <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('label') is-invalid @enderror"
                                                   id="label" name="label" value="{{ old('label', $busPassStatus->label) }}" required>
                                            <small class="form-text text-muted">Label shown to users</small>
                                            @error('label')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="badge_color">Badge Color <span class="text-danger">*</span></label>
                                            <select class="form-control @error('badge_color') is-invalid @enderror"
                                                    id="badge_color" name="badge_color" required>
                                                <option value="">Select Color</option>
                                                <option value="primary" {{ old('badge_color', $busPassStatus->badge_color) == 'primary' ? 'selected' : '' }}>Primary (Blue)</option>
                                                <option value="secondary" {{ old('badge_color', $busPassStatus->badge_color) == 'secondary' ? 'selected' : '' }}>Secondary (Gray)</option>
                                                <option value="success" {{ old('badge_color', $busPassStatus->badge_color) == 'success' ? 'selected' : '' }}>Success (Green)</option>
                                                <option value="danger" {{ old('badge_color', $busPassStatus->badge_color) == 'danger' ? 'selected' : '' }}>Danger (Red)</option>
                                                <option value="warning" {{ old('badge_color', $busPassStatus->badge_color) == 'warning' ? 'selected' : '' }}>Warning (Yellow)</option>
                                                <option value="info" {{ old('badge_color', $busPassStatus->badge_color) == 'info' ? 'selected' : '' }}>Info (Cyan)</option>
                                                <option value="light" {{ old('badge_color', $busPassStatus->badge_color) == 'light' ? 'selected' : '' }}>Light</option>
                                                <option value="dark" {{ old('badge_color', $busPassStatus->badge_color) == 'dark' ? 'selected' : '' }}>Dark</option>
                                            </select>
                                            @error('badge_color')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sort_order">Sort Order <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                                   id="sort_order" name="sort_order" value="{{ old('sort_order', $busPassStatus->sort_order) }}" required min="0">
                                            <small class="form-text text-muted">Lower numbers appear first</small>
                                            @error('sort_order')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror"
                                                      id="description" name="description" rows="3">{{ old('description', $busPassStatus->description) }}</textarea>
                                            @error('description')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" 
                                                       id="is_active" name="is_active" value="1" 
                                                       {{ old('is_active', $busPassStatus->is_active) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active">
                                                    Active Status
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Status
                                </button>
                                <a href="{{ route('bus-pass-statuses.show', $busPassStatus) }}" class="btn btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('bus-pass-statuses.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
