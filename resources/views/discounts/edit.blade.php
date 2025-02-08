{{-- // resources/views/discounts/edit.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">Edit Discount</div>
                    <div class="card-body">
                        <form action="{{ route('discounts.update', $discount) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $discount->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type"
                                    name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="percentage"
                                        {{ old('type', $discount->type) === 'percentage' ? 'selected' : '' }}>Percentage
                                    </option>
                                    <option value="fixed" {{ old('type', $discount->type) === 'fixed' ? 'selected' : '' }}>
                                        Fixed Amount</option>
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
                                        name="value" value="{{ old('value', $discount->value) }}" required>
                                    <span class="input-group-text"
                                        id="value-addon">{{ $discount->type === 'percentage' ? '%' : 'Rp' }}</span>
                                </div>
                                @error('value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                        value="1" {{ old('is_active', $discount->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Discount</button>
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
