<!-- resources/views/product_units/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-plus-circle"></i> Add New Unit
                            <small class="text-muted d-block mt-1">{{ $product->name }}</small>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('products.units.store', $product) }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label for="unit_id" class="form-label">Select Unit</label>
                                <select class="form-select form-select-lg @error('unit_id') is-invalid @enderror"
                                        id="unit_id" name="unit_id">
                                    <option value="">Choose a unit...</option>
                                    @foreach($availableUnits as $unit)
                                        <option value="{{ $unit->id }}"
                                            {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }} ({{ $unit->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="conversion_factor" class="form-label">
                                        Conversion Factor
                                        <i class="bi bi-info-circle" data-bs-toggle="tooltip"
                                           title="How many base units equal one of this unit"></i>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.0001"
                                               class="form-control @error('conversion_factor') is-invalid @enderror"
                                               id="conversion_factor" name="conversion_factor"
                                               value="{{ old('conversion_factor', '1.0000') }}">
                                        <span class="input-group-text">x base unit</span>
                                    </div>
                                    @error('conversion_factor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="price" class="form-label">
                                        Price
                                        <i class="bi bi-info-circle" data-bs-toggle="tooltip"
                                           title="Price for this unit size"></i>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" step="100"
                                               class="form-control @error('price') is-invalid @enderror"
                                               id="price" name="price" value="{{ old('price') }}">
                                    </div>
                                    @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="is_default"
                                           name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        Set as Default Unit
                                        <small class="text-muted d-block">
                                            The default unit's price will be used as the base price for all conversions
                                        </small>
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('products.units.index', $product) }}"
                                   class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Save Unit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            })
        </script>
    @endpush
@endsection
