@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .upload-button {
            width: 32px;
            height: 32px;
        }

        .store-logo {
            width: 120px;
            height: 120px;
            object-fit: cover;
        }
    </style>
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <!-- Main Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 d-flex align-items-center">
                            <i class="bi bi-building text-primary me-2"></i>
                            Create New Store
                        </h5>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('stores.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Logo Upload Section -->
                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <div class="border rounded-circle p-2 bg-light">
                                    <div
                                        class="rounded-circle bg-white d-flex align-items-center justify-content-center store-logo">
                                        <i class="bi bi-building display-6 text-secondary"></i>
                                    </div>
                                    <label for="logo"
                                           class="position-absolute bottom-0 end-0 mb-1 me-1 btn btn-primary rounded-circle p-0 d-flex align-items-center justify-content-center upload-button">
                                        <i class="bi bi-camera-fill small"></i>
                                        <input type="file" id="logo" name="logo" class="d-none" accept="image/*">
                                    </label>
                                </div>
                            </div>
                            @error('logo')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-4">
                            <!-- Store Information -->
                            <div class="col-md-6">
                                <h6 class="text-primary border-bottom pb-2 mb-4">
                                    <i class="bi bi-info-circle me-2"></i>Store Information
                                </h6>

                                <div class="mb-4">
                                    <label for="code" class="form-label small fw-semibold text-secondary">Store
                                        Code</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-hash"></i>
                                        </span>
                                        <input type="text"
                                               class="form-control @error('code') is-invalid @enderror"
                                               id="code"
                                               name="code"
                                               value="{{ old('code') }}"
                                               required>
                                    </div>
                                    @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="name" class="form-label small fw-semibold text-secondary">Store
                                        Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-shop"></i>
                                        </span>
                                        <input type="text"
                                               class="form-control @error('name') is-invalid @enderror"
                                               id="name"
                                               name="name"
                                               value="{{ old('name') }}"
                                               required>
                                    </div>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="address"
                                           class="form-label small fw-semibold text-secondary">Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-geo-alt"></i>
                                        </span>
                                        <textarea class="form-control @error('address') is-invalid @enderror"
                                                  id="address"
                                                  name="address"
                                                  rows="3"
                                                  required>{{ old('address') }}</textarea>
                                    </div>
                                    @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="col-md-6">
                                <h6 class="text-primary border-bottom pb-2 mb-4">
                                    <i class="bi bi-person-lines-fill me-2"></i>Contact Information
                                </h6>

                                <div class="mb-4">
                                    <label for="phone" class="form-label small fw-semibold text-secondary">Phone
                                        Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-telephone"></i>
                                        </span>
                                        <input type="text"
                                               class="form-control @error('phone') is-invalid @enderror"
                                               id="phone"
                                               name="phone"
                                               value="{{ old('phone') }}"
                                               required>
                                    </div>
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="email" class="form-label small fw-semibold text-secondary">Email
                                        Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-envelope"></i>
                                        </span>
                                        <input type="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               id="email"
                                               name="email"
                                               value="{{ old('email') }}"
                                               required>
                                    </div>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-semibold text-secondary">Store Status</label>
                                    <div class="form-check form-switch">
                                        <input type="checkbox"
                                               class="form-check-input"
                                               id="is_active"
                                               name="is_active"
                                               value="1"
                                            {{ old('is_active') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active Store
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-4 pt-4 border-top">
                            <a href="{{ route('stores.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Create Store
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('logo').addEventListener('change', function (e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = document.querySelector('.rounded-circle.bg-white');
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Store Logo';
                    img.className = 'rounded-circle store-logo';
                    preview.parentNode.replaceChild(img, preview);
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    </script>
@endpush
