{{-- resources/views/stores/form.blade.php --}}
<div class="row g-3">
    <div class="col-md-6">
        <label for="code" class="form-label">Store Code</label>
        <input type="text" name="code" id="code"
               value="{{ old('code', $store->code ?? '') }}"
               class="form-control @error('code') is-invalid @enderror"
               required>
        @error('code')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="name" class="form-label">Store Name</label>
        <input type="text" name="name" id="name"
               value="{{ old('name', $store->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror"
               required>
        @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="address" class="form-label">Address</label>
        <textarea name="address" id="address" rows="3"
                  class="form-control @error('address') is-invalid @enderror"
                  required>{{ old('address', $store->address ?? '') }}</textarea>
        @error('address')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="phone" class="form-label">Phone</label>
        <input type="text" name="phone" id="phone"
               value="{{ old('phone', $store->phone ?? '') }}"
               class="form-control @error('phone') is-invalid @enderror"
               required>
        @error('phone')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email"
               value="{{ old('email', $store->email ?? '') }}"
               class="form-control @error('email') is-invalid @enderror"
               required>
        @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="logo" class="form-label">Logo</label>
        <div class="d-flex align-items-center gap-3">
            @if(isset($store) && $store->logo)
                <img src="{{ $store->logo }}" alt="Current logo"
                     class="rounded" style="height: 80px; width: 80px; object-fit: cover;">
            @endif
            <input type="file" name="logo" id="logo"
                   class="form-control @error('logo') is-invalid @enderror"
                   accept="image/*">
        </div>
        @error('logo')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <div class="form-check">
            <input type="checkbox" name="is_active" id="is_active"
                   value="1"
                   {{ old('is_active', $store->is_active ?? true) ? 'checked' : '' }}
                   class="form-check-input @error('is_active') is-invalid @enderror">
            <label class="form-check-label" for="is_active">
                Active Store
            </label>
        </div>
        @error('is_active')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
