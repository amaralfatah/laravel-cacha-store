@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tambah Unit - {{ $product->name }}</h5>
                        @if($hasDefaultUnit)
                            <span class="badge bg-info">Unit default sudah ada</span>
                        @endif
                    </div>

                    <div class="card-body">
                        <form action="{{ route('products.units.store', $product) }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="unit_id" class="form-label">Unit</label>
                                <select class="form-select @error('unit_id') is-invalid @enderror"
                                        name="unit_id" id="unit_id" required>
                                    <option value="">Pilih unit...</option>
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

                            <div class="mb-3">
                                <label for="conversion_factor" class="form-label">Faktor Konversi</label>
                                <input type="number" class="form-control @error('conversion_factor') is-invalid @enderror"
                                       name="conversion_factor" id="conversion_factor"
                                       value="{{ old('conversion_factor', 1) }}" min="1" required>
                                @error('conversion_factor')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="purchase_price" class="form-label">Harga Beli</label>
                                    <input type="number" class="form-control @error('purchase_price') is-invalid @enderror"
                                           name="purchase_price" id="purchase_price"
                                           value="{{ old('purchase_price') }}" min="0" step="0.01" required>
                                    @error('purchase_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="selling_price" class="form-label">Harga Jual</label>
                                    <input type="number" class="form-control @error('selling_price') is-invalid @enderror"
                                           name="selling_price" id="selling_price"
                                           value="{{ old('selling_price') }}" min="0" step="0.01" required>
                                    @error('selling_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="stock" class="form-label">Stok</label>
                                    <input type="number" class="form-control @error('stock') is-invalid @enderror"
                                           name="stock" id="stock"
                                           value="{{ old('stock', 0) }}" min="0" step="0.01" required>
                                    @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="min_stock" class="form-label">Stok Minimum</label>
                                    <input type="number" class="form-control @error('min_stock') is-invalid @enderror"
                                           name="min_stock" id="min_stock"
                                           value="{{ old('min_stock', 0) }}" min="0" step="0.01" required>
                                    @error('min_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="is_default" id="is_default"
                                           value="1" {{ !$hasDefaultUnit ? 'checked disabled' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        Jadikan Unit Default
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
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
                const defaultCheckbox = document.getElementById('is_default');
                const conversionInput = document.getElementById('conversion_factor');

                defaultCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        conversionInput.value = '1';
                        conversionInput.readOnly = true;
                    } else {
                        conversionInput.readOnly = false;
                    }
                });

                // Set initial state
                if (defaultCheckbox.checked) {
                    conversionInput.value = '1';
                    conversionInput.readOnly = true;
                }
            });
        </script>
    @endpush
@endsection
