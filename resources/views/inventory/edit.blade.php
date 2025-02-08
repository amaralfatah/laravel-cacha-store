<!-- resources/views/inventory/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3>Edit Stok</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('inventory.update', $inventory) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Produk</label>
                                <input type="text" class="form-control" value="{{ $inventory->product->name }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Unit</label>
                                <input type="text" class="form-control" value="{{ $inventory->unit->name }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="quantity" class="form-label">Jumlah Stok</label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                    id="quantity" name="quantity" value="{{ old('quantity', $inventory->quantity) }}"
                                    required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="min_stock" class="form-label">Minimum Stok</label>
                                <input type="number" class="form-control @error('min_stock') is-invalid @enderror"
                                    id="min_stock" name="min_stock" value="{{ old('min_stock', $inventory->min_stock) }}"
                                    required>
                                @error('min_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
