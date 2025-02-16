{{-- // resources/views/discounts/create.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">Create Discount</div>
                    <div class="card-body">
                        <form action="{{ route('discounts.store') }}" method="POST">
                            @csrf
                            @if(auth()->user()->role === 'admin')
                                <div class="mb-3">
                                    <label for="store_id" class="form-label">Store</label>
                                    <select class="form-select @error('store_id') is-invalid @enderror"
                                            id="store_id"
                                            name="store_id"
                                            required>
                                        <option value="">Select Store</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}"
                                                {{ old('store_id') == $store->id ? 'selected' : '' }}>
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
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type"
                                    name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>
                                        Percentage</option>
                                    <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Fixed Amount
                                    </option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="value" class="form-label">Value</label>
                                <div class="input-group">
                                    <input type="number" step="0.01"
                                        class="form-control @error('value') is-invalid @enderror" id="value"
                                        name="value" value="{{ old('value') }}" required>
                                    <span class="input-group-text" id="value-addon">%</span>
                                </div>
                                @error('value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                        value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Create Discount</button>
                            <a href="{{ route('discounts.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('type').addEventListener('change', function() {
                const valueAddon = document.getElementById('value-addon');
                if (this.value === 'percentage') {
                    valueAddon.textContent = '%';
                } else {
                    valueAddon.textContent = 'Rp';
                }
            });
        </script>
    @endpush
@endsection
