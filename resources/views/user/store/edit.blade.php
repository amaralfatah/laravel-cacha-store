@extends('layouts.app')

@section('content')

    <x-section-header title="Edit Store"/>

    <div class="card">

        <!-- Store Logo -->
        <div class="card-body">
            <form action="{{ route('user.store.update') }}" method="POST" enctype="multipart/form-data" id="storeForm">
                @csrf
                @method('PUT')

                <div class="d-flex align-items-start align-items-sm-center gap-4">
                    @if($store->logo)
                        <img src="{{ asset($store->logo) }}" alt="store-logo" class="d-block rounded" height="100"
                             width="100" id="previewImage"/>
                    @else
                        <div class="d-block rounded bg-light d-flex align-items-center justify-content-center"
                             id="noImagePreview" style="height: 100px; width: 100px;">
                            <i class='bx bx-store text-secondary' style="font-size: 2rem;"></i>
                        </div>
                        <img src="" alt="store-logo" class="d-block rounded d-none" height="100" width="100"
                             id="previewImage"/>
                    @endif
                    <div class="button-wrapper">
                        <label for="logo" class="btn btn-primary me-2 mb-4" tabindex="0">
                            <span class="d-none d-sm-block">Upload new photo</span>
                            <i class="bx bx-upload d-block d-sm-none"></i>
                            <input type="file" id="logo" name="logo" class="account-file-input" hidden
                                   accept="image/png, image/jpeg"/>
                        </label>
                        <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                            <i class="bx bx-reset d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Reset</span>
                        </button>

                        <p class="text-muted mb-0">Allowed JPG or PNG. Max size of 2MB</p>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="name" class="form-label">Store Name</label>
                        <input class="form-control @error('name') is-invalid @enderror"
                               type="text"
                               id="name"
                               name="name"
                               value="{{ old('name', $store->name) }}"
                               autofocus/>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="code" class="form-label">Store Code</label>
                        <input class="form-control"
                               type="text"
                               id="code"
                               value="{{ $store->code }}"
                               disabled/>
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="email" class="form-label">E-mail</label>
                        <input class="form-control @error('email') is-invalid @enderror"
                               type="email"
                               id="email"
                               name="email"
                               value="{{ old('email', $store->email) }}"/>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="phone">Phone Number</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class='bx bx-phone'></i></span>
                            <input type="text"
                                   id="phone"
                                   name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $store->phone) }}"/>
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 col-md-12">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror"
                                  id="address"
                                  name="address"
                                  rows="3">{{ old('address', $store->address) }}</textarea>
                        @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mt-2">
                    <button type="submit" class="btn btn-primary me-2">Save changes</button>
                    <a href="{{ route('user.store.show') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('storeForm');
            const logoInput = document.getElementById('logo');
            const previewImage = document.getElementById('previewImage');
            const noImagePreview = document.getElementById('noImagePreview');
            const resetButton = document.querySelector('.account-image-reset');

            // Original logo URL untuk reset
            const originalLogoUrl = "{{ $store->logo ? asset($store->logo) : '' }}";

            if (logoInput) {
                logoInput.addEventListener('change', function (e) {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();

                        reader.onload = function (e) {
                            if (previewImage) {
                                previewImage.src = e.target.result;
                                previewImage.classList.remove('d-none');
                                if (noImagePreview) {
                                    noImagePreview.classList.add('d-none');
                                }
                            }
                        }

                        reader.readAsDataURL(this.files[0]);
                    }
                });
            }

            if (resetButton) {
                resetButton.addEventListener('click', function () {
                    if (logoInput) {
                        logoInput.value = '';
                    }

                    if (originalLogoUrl) {
                        if (previewImage) {
                            previewImage.src = originalLogoUrl;
                            previewImage.classList.remove('d-none');
                        }
                        if (noImagePreview) {
                            noImagePreview.classList.add('d-none');
                        }
                    } else {
                        if (previewImage) {
                            previewImage.src = '';
                            previewImage.classList.add('d-none');
                        }
                        if (noImagePreview) {
                            noImagePreview.classList.remove('d-none');
                        }
                    }
                });
            }
        });
    </script>
@endpush
