@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Unit - {{ $product->name }}</h5>
                        <span class="badge {{ $unit->is_default ? 'bg-primary' : 'bg-secondary' }}">
                        {{ $unit->is_default ? 'Unit Default' : 'Unit Tambahan' }}
                    </span>
                    </div>

                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <strong>{{ $unit->unit->name }} ({{ $unit->unit->code }})</strong>
                        </div>

                        <form action="{{ route('products.units.update', [$product, $unit]) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="conversion_factor" class="form-label">Faktor Konversi</label>
                                <input type="number"
                                       class="form-control @error('conversion_factor') is-invalid @enderror"
                                       name="conversion_factor" id="conversion_factor"
                                       value="{{ old('conversion_factor', $unit->conversion_factor) }}"
                                       min="1" required {{ $unit->is_default ? 'readonly' : '' }}>
                                @error('conversion_factor')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="purchase_price" class="form-label">Harga Beli</label>
                                    <input type="number"
                                           class="form-control @error('purchase_price') is-invalid @enderror"
                                           name="purchase_price" id="purchase_price"
                                           value="{{ old('purchase_price', $unit->purchase_price) }}"
                                           min="0" step="50" required>
                                    @error('purchase_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="selling_price" class="form-label">Harga Jual</label>
                                    <input type="number"
                                           class="form-control @error('selling_price') is-invalid @enderror"
                                           name="selling_price" id="selling_price"
                                           value="{{ old('selling_price', $unit->selling_price) }}"
                                           min="0" step="50" required>
                                    @error('selling_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <input type="hidden" name="stock" value="{{ $unit->stock }}">

                            <div class="col-md-12 mb-3">
                                <label for="min_stock" class="form-label">Stok Minimum</label>
                                <input type="number" class="form-control @error('min_stock') is-invalid @enderror"
                                       name="min_stock" id="min_stock"
                                       value="{{ old('min_stock', $unit->min_stock) }}"
                                       min="0" step="1" required>
                                @error('min_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input"
                                           name="is_default" id="is_default" value="1"
                                        {{ old('is_default', $unit->is_default) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        Jadikan Unit Default
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-danger"
                                        onclick="confirmDelete()">Hapus
                                </button>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('products.show', $product) }}"
                                       class="btn btn-secondary">Batal</a>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </div>
                        </form>

                        <form id="deleteForm"
                              action="{{ route('products.units.destroy', [$product, $unit]) }}"
                              method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const defaultCheckbox = document.getElementById('is_default');
                const conversionInput = document.getElementById('conversion_factor');

                defaultCheckbox.addEventListener('change', function () {
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

            function confirmDelete() {
                if (confirm('Apakah Anda yakin ingin menghapus unit ini?')) {
                    document.getElementById('deleteForm').submit();
                }
            }
        </script>
    @endpush
@endsection
