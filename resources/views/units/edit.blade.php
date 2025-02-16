@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">Edit Unit</div>
                    <div class="card-body">
                        <form action="{{ route('units.update', $unit) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <!-- Add this in both create.blade.php and edit.blade.php forms -->
                            @if(auth()->user()->role === 'admin')
                                <div class="mb-3">
                                    <label for="store_id" class="form-label">Store</label>
                                    <select class="form-control @error('store_id') is-invalid @enderror"
                                            id="store_id"
                                            name="store_id"
                                            required>
                                        <option value="">Select Store</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}"
                                                {{ old('store_id', $unit->store_id ?? '') == $store->id ? 'selected' : '' }}>
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
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $unit->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="code" class="form-label">Code</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                    id="code" name="code" value="{{ old('code', $unit->code) }}" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Example: PCS, BOX, KG, etc.</div>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('units.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Unit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
