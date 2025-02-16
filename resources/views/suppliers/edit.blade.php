@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">Edit Supplier</div>
                    <div class="card-body">
                        <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @if(auth()->user()->role === 'admin')
                                <div class="mb-3">
                                    <label for="store_id" class="form-label">Toko</label>
                                    <select class="form-select @error('store_id') is-invalid @enderror"
                                            id="store_id" name="store_id" required>
                                        <option value="">Pilih Toko</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}"
                                                {{ old('store_id', $supplier->store_id ?? '') == $store->id ? 'selected' : '' }}>
                                                {{ $store->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('store_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                            <div class="mb-3">
                                <label for="code" class="form-label">Kode</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                       id="code" name="code" value="{{ old('code', $supplier->code) }}" required>
                                @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $supplier->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Update Supplier</button>
                            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
