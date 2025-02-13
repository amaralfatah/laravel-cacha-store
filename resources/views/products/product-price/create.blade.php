<!-- resources/views/product-price/create-tier.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Create Tiered Price</h2>
                <p class="text-muted mb-0">
                    <i class="bi bi-box me-2"></i>{{ $product->name }}
                    <span class="ms-3">
                    <i class="bi bi-upc me-2"></i>{{ $product->barcode }}
                </span>
                </p>
            </div>
            <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Product
            </a>
        </div>

        <div class="row">
            <!-- Current Price Information -->
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Current Price Information</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $defaultUnit = $product->productUnits->where('is_default', true)->first();
                        @endphp
                        <dl>
                            <dt>Default Unit</dt>
                            <dd>{{ $defaultUnit?->unit->name ?? 'N/A' }}</dd>

                            <dt>Base Price</dt>
                            <dd>Rp {{ number_format($defaultUnit?->selling_price ?? 0, 2) }}</dd>

                            @if($product->tax)
                                <dt>Tax</dt>
                                <dd>{{ $product->tax->name }} ({{ $product->tax->rate }}%)</dd>
                            @endif

                            @if($product->discount)
                                <dt>Active Discount</dt>
                                <dd>{{ $product->discount->name }}
                                    ({{ $product->discount->value }}{{ $product->discount->type === 'percentage' ? '%' : ' Rp' }})
                                </dd>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Current Tiers -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Existing Price Tiers</h5>
                    </div>
                    <div class="card-body">
                        @forelse($product->productUnits as $productUnit)
                            @if($productUnit->prices->isNotEmpty())
                                <h6 class="mb-2">{{ $productUnit->unit->name }}</h6>
                                <div class="table-responsive mb-3">
                                    <table class="table table-sm">
                                        <thead class="table-light">
                                        <tr>
                                            <th>Min. Qty</th>
                                            <th class="text-end">Price</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($productUnit->prices->sortBy('min_quantity') as $tier)
                                            <tr>
                                                <td>{{ number_format($tier->min_quantity) }}</td>
                                                <td class="text-end">Rp {{ number_format($tier->price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        @empty
                            <p class="text-muted mb-0">No price tiers defined yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Create New Tier Form -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Create New Price Tier</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('products.prices.store', $product) }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label for="product_unit_id" class="form-label">Product Unit</label>
                                <select name="product_unit_id"
                                        id="product_unit_id"
                                        class="form-select @error('product_unit_id') is-invalid @enderror"
                                        required>
                                    <option value="">Select Unit...</option>
                                    @foreach ($product->productUnits as $unit)
                                        <option value="{{ $unit->id }}"
                                                data-base-price="{{ $unit->selling_price }}"
                                                data-code="{{ $unit->unit->code }}"
                                            {{ old('product_unit_id') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->unit->name }}
                                            ({{ $unit->unit->code }}) -
                                            Base Price: Rp {{ number_format($unit->selling_price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="min_quantity" class="form-label">Minimum Quantity</label>
                                    <div class="input-group">
                                        <input type="number"
                                               class="form-control @error('min_quantity') is-invalid @enderror"
                                               id="min_quantity"
                                               name="min_quantity"
                                               value="{{ old('min_quantity') }}"
                                               step="0.01"
                                               min="0.01"
                                               required>
                                        <span class="input-group-text unit-code"></span>
                                        @error('min_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">
                                        The quantity at which this price tier becomes active
                                    </small>
                                </div>

                                <div class="col-md-6">
                                    <label for="price" class="form-label">Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number"
                                               class="form-control @error('price') is-invalid @enderror"
                                               id="price"
                                               name="price"
                                               value="{{ old('price') }}"
                                               step="0.01"
                                               min="0"
                                               required>
                                        @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">
                                        Must be lower than the base price for larger quantities
                                    </small>
                                </div>
                            </div>

                            <div class="alert alert-info" role="alert">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Pricing Rules:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Price must be lower than the unit's base price</li>
                                    <li>Price must be lower than tiers with smaller quantities</li>
                                    <li>Each quantity threshold must be unique per unit</li>
                                </ul>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('products.show', $product) }}"
                                   class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Create Price Tier
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productUnitSelect = document.getElementById('product_unit_id');
            const unitCodeSpan = document.querySelector('.unit-code');
            const priceInput = document.getElementById('price');

            if (productUnitSelect) {
                productUnitSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];

                    if (selectedOption.value) {
                        const unitCode = selectedOption.dataset.code;
                        const basePrice = parseFloat(selectedOption.dataset.basePrice);

                        unitCodeSpan.textContent = unitCode;

                        // Set max price to base price
                        priceInput.setAttribute('max', basePrice);

                        // Update price input placeholder
                        priceInput.setAttribute('placeholder', `Max: ${basePrice}`);
                    } else {
                        unitCodeSpan.textContent = '';
                        priceInput.removeAttribute('max');
                        priceInput.removeAttribute('placeholder');
                    }
                });

                // Trigger initial update if there's a selected value
                if (productUnitSelect.value) {
                    productUnitSelect.dispatchEvent(new Event('change'));
                }
            }
        });
    </script>
@endpush
