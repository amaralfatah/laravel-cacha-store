<!-- resources/views/product_units/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3>Edit Unit Conversion for {{ $product->name }}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('products.units.update', [$product, $unit]) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Unit</label>
                                <input type="text" class="form-control"
                                    value="{{ $unit->unit->name }} ({{ $unit->unit->code }})" readonly>
                                <small class="text-muted">Unit cannot be changed. Delete this conversion and create a new
                                    one if needed.</small>
                            </div>

                            <div class="mb-3">
                                <label for="conversion_factor" class="form-label">Conversion Factor</label>
                                <input type="number" step="0.0001"
                                    class="form-control @error('conversion_factor') is-invalid @enderror"
                                    id="conversion_factor" name="conversion_factor"
                                    value="{{ old('conversion_factor', $unit->conversion_factor) }}">
                                @error('conversion_factor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" step="0.01"
                                    class="form-control @error('price') is-invalid @enderror" id="price" name="price"
                                    value="{{ old('price', $unit->price) }}">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_default" name="is_default"
                                        value="1" {{ old('is_default', $unit->is_default) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">Set as Default Unit</label>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Update Unit Conversion</button>
                                <a href="{{ route('products.units.index', $product) }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
