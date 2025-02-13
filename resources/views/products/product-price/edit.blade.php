@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Edit Harga Bertingkat</h2>
                <p class="text-muted mb-0">
                    <i class="bi bi-box me-2"></i>{{ $product->name }}
                    <span class="ms-3">
                    <i class="bi bi-upc me-2"></i>{{ $product->barcode }}
                </span>
                </p>
            </div>
            <div class="d-flex gap-2">
                <form action="{{ route('products.prices.destroy', [$product, $price]) }}"
                      method="POST"
                      onsubmit="return confirm('Anda yakin ingin menghapus tingkat harga ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash me-1"></i>Hapus
                    </button>
                </form>
                <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Produk
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Informasi Harga Saat Ini -->
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Informasi Harga Saat Ini</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $defaultUnit = $product->productUnits->where('is_default', true)->first();
                        @endphp
                        <dl>
                            <dt>Unit Default</dt>
                            <dd>{{ $defaultUnit?->unit->name ?? 'N/A' }}</dd>

                            <dt>Harga Dasar</dt>
                            <dd>Rp {{ number_format($defaultUnit?->selling_price ?? 0, 2) }}</dd>

                            @if($product->tax)
                                <dt>Pajak</dt>
                                <dd>{{ $product->tax->name }} ({{ $product->tax->rate }}%)</dd>
                            @endif

                            @if($product->discount)
                                <dt>Diskon Aktif</dt>
                                <dd>{{ $product->discount->name }}
                                    ({{ $product->discount->value }}{{ $product->discount->type === 'percentage' ? '%' : ' Rp' }}
                                    )
                                </dd>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Tingkat Harga Saat Ini -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Ringkasan Tingkat Harga</h5>
                    </div>
                    <div class="card-body">
                        @foreach($product->productUnits as $productUnit)
                            @if($productUnit->prices->isNotEmpty())
                                <div class="mb-4">
                                    <h6 class="border-bottom pb-2">
                                        {{ $productUnit->unit->name }}
                                        <small class="text-muted">(Dasar:
                                            Rp {{ number_format($productUnit->selling_price, 2) }})</small>
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead class="table-light">
                                            <tr>
                                                <th>Min. Jumlah</th>
                                                <th class="text-end">Harga</th>
                                                <th class="text-end">Diskon</th>
                                                <th class="text-center">Status</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($productUnit->prices->sortBy('min_quantity') as $tier)
                                                <tr @if($tier->id === $price->id) class="table-warning" @endif>
                                                    <td>{{ number_format($tier->min_quantity, 2) }}</td>
                                                    <td class="text-end">Rp {{ number_format($tier->price, 2) }}</td>
                                                    <td class="text-end">
                                                        @php
                                                            $discount = (($productUnit->selling_price - $tier->price) / $productUnit->selling_price) * 100;
                                                        @endphp
                                                        {{ number_format($discount, 1) }}%
                                                    </td>
                                                    <td class="text-center">
                                                        @if($tier->id === $price->id)
                                                            <span class="badge bg-warning text-dark">Sedang Diedit</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Form Edit Tingkat Harga -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Edit Tingkat Harga</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('products.prices.update', [$product, $price]) }}"
                              method="POST"
                              id="editPriceTierForm">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label for="product_unit_id" class="form-label">Unit Produk</label>
                                <select name="product_unit_id"
                                        id="product_unit_id"
                                        class="form-select @error('product_unit_id') is-invalid @enderror"
                                        required>
                                    @foreach ($product->productUnits as $unit)
                                        <option value="{{ $unit->id }}"
                                                data-base-price="{{ $unit->selling_price }}"
                                                data-code="{{ $unit->unit->code }}"
                                            {{ old('product_unit_id', $price->product_unit_id) == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->unit->name }}
                                            ({{ $unit->unit->code }}) -
                                            Harga Dasar: Rp {{ number_format($unit->selling_price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="min_quantity" class="form-label">Jumlah Minimum</label>
                                    <div class="input-group">
                                        <input type="number"
                                               class="form-control @error('min_quantity') is-invalid @enderror"
                                               id="min_quantity"
                                               name="min_quantity"
                                               value="{{ old('min_quantity', $price->min_quantity) }}"
                                               step="0.01"
                                               min="0.01"
                                               required>
                                        <span class="input-group-text unit-code"></span>
                                    </div>
                                    @error('min_quantity')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    @if(isset($previousTier))
                                        <small class="text-muted d-block">
                                            Tingkat sebelumnya: {{ number_format($previousTier->min_quantity) }}
                                        </small>
                                    @endif
                                    @if(isset($nextTier))
                                        <small class="text-muted d-block">
                                            Tingkat selanjutnya: {{ number_format($nextTier->min_quantity) }}
                                        </small>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <label for="price" class="form-label">Harga</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number"
                                               class="form-control @error('price') is-invalid @enderror"
                                               id="price"
                                               name="price"
                                               value="{{ old('price', $price->price) }}"
                                               step="0.01"
                                               min="0"
                                               required>
                                    </div>
                                    @error('price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div id="priceDetails" class="mt-2">
                                        <small class="text-muted">
                                            Diskon: <span id="discountPercentage">0</span>%</small>
                                        @if(isset($previousTier))
                                            <small class="text-muted d-block">
                                                Harga tingkat sebelumnya: Rp {{ number_format($previousTier->price, 2) }}
                                            </small>
                                        @endif
                                        @if(isset($nextTier))
                                            <small class="text-muted d-block">
                                                Harga tingkat selanjutnya: Rp {{ number_format($nextTier->price, 2) }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info" role="alert">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Aturan Penetapan Harga:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Harga harus lebih rendah dari harga dasar unit</li>
                                    <li>Harga harus lebih rendah dari tingkat dengan jumlah yang lebih kecil</li>
                                    <li>Harga harus lebih tinggi dari tingkat dengan jumlah yang lebih besar</li>
                                    <li>Setiap batas jumlah harus unik per unit</li>
                                </ul>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('products.show', $product) }}"
                                   class="btn btn-outline-secondary">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Perbarui Tingkat Harga
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
        document.addEventListener('DOMContentLoaded', function () {
            // DOM Elements
            const elements = {
                form: document.getElementById('editPriceTierForm'),
                unitSelect: document.getElementById('product_unit_id'),
                unitCode: document.querySelector('.unit-code'),
                price: document.getElementById('price'),
                minQuantity: document.getElementById('min_quantity'),
                discountPercentage: document.getElementById('discountPercentage')
            };

            // State management
            const state = {
                currentUnit: {
                    id: null,
                    basePrice: 0,
                    code: '',
                    tiers: []
                },
                validation: {
                    price: true,
                    quantity: true
                }
            };

            // Initialize price tiers data
            const priceTiers = {!! json_encode($product->productUnits->map(function($unit) use ($price) {
        return [
            'id' => $unit->id,
            'basePrice' => floatval($unit->selling_price),
            'code' => $unit->unit->code,
            'tiers' => $unit->prices
                ->where('id', '!=', $price->id)
                ->map(function($tier) {
                    return [
                        'min_quantity' => floatval($tier->min_quantity),
                        'price' => floatval($tier->price)
                    ];
                })->values()
        ];
    })) !!};

            // Format currency to Rupiah
            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 2
                }).format(number);
            }

            // Validate minimum quantity
            function validateQuantity() {
                const quantity = parseFloat(elements.minQuantity.value);

                if (isNaN(quantity) || quantity <= 0) {
                    showError(elements.minQuantity, 'Jumlah harus lebih besar dari 0');
                    return false;
                }

                // Check if quantity already exists in other tiers
                const existingQuantity = state.currentUnit.tiers.find(t => t.min_quantity === quantity);
                if (existingQuantity) {
                    showError(elements.minQuantity, 'Jumlah ini sudah memiliki tingkat harga');
                    return false;
                }

                removeError(elements.minQuantity);
                return true;
            }

            // Validate price against rules
            function validatePrice() {
                const price = parseFloat(elements.price.value);
                const quantity = parseFloat(elements.minQuantity.value);

                if (isNaN(price) || price <= 0) {
                    showError(elements.price, 'Harga harus lebih besar dari 0');
                    return false;
                }

                // Must be lower than base price
                if (price >= state.currentUnit.basePrice) {
                    showError(elements.price, `Harga harus lebih rendah dari harga dasar ${formatRupiah(state.currentUnit.basePrice)}`);
                    return false;
                }

                // Check against other tiers
                const sortedTiers = [...state.currentUnit.tiers].sort((a, b) => a.min_quantity - b.min_quantity);

                // Find adjacent tiers
                const lowerTier = sortedTiers.filter(t => t.min_quantity < quantity).pop();
                const higherTier = sortedTiers.find(t => t.min_quantity > quantity);

                // Validate against lower tier
                if (lowerTier && price >= lowerTier.price) {
                    showError(elements.price,
                        `Harga harus lebih rendah dari ${formatRupiah(lowerTier.price)} (tingkat untuk jumlah ${lowerTier.min_quantity})`
                    );
                    return false;
                }

                // Validate against higher tier
                if (higherTier && price <= higherTier.price) {
                    showError(elements.price,
                        `Harga harus lebih tinggi dari ${formatRupiah(higherTier.price)} (tingkat untuk jumlah ${higherTier.min_quantity})`
                    );
                    return false;
                }

                removeError(elements.price);
                return true;
            }

            // Update discount percentage display
            function updateDiscountPercentage() {
                const price = parseFloat(elements.price.value);
                if (!isNaN(price) && state.currentUnit.basePrice > 0) {
                    const discount = ((state.currentUnit.basePrice - price) / state.currentUnit.basePrice) * 100;
                    elements.discountPercentage.textContent = discount.toFixed(1);
                } else {
                    elements.discountPercentage.textContent = '0.0';
                }
            }

            // Error display helpers
            function showError(element, message) {
                element.classList.add('is-invalid');

                let errorDiv = element.parentElement.nextElementSibling;
                if (!errorDiv?.classList.contains('invalid-feedback')) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    element.parentElement.after(errorDiv);
                }
                errorDiv.textContent = message;
            }

            function removeError(element) {
                element.classList.remove('is-invalid');
                const errorDiv = element.parentElement.nextElementSibling;
                if (errorDiv?.classList.contains('invalid-feedback')) {
                    errorDiv.remove();
                }
            }

            // Update current unit data when selection changes
            function updateCurrentUnit() {
                const selectedUnit = priceTiers.find(unit => unit.id === parseInt(elements.unitSelect.value));
                if (selectedUnit) {
                    state.currentUnit = {
                        id: selectedUnit.id,
                        basePrice: selectedUnit.basePrice,
                        code: selectedUnit.code,
                        tiers: selectedUnit.tiers
                    };
                    elements.unitCode.textContent = selectedUnit.code;
                }
                validatePrice();
                validateQuantity();
                updateDiscountPercentage();
            }

            // Event Listeners
            if (elements.form) {
                elements.form.addEventListener('submit', function (e) {
                    // Prevent submission if validation fails
                    if (!validatePrice() || !validateQuantity()) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                });
            }

            if (elements.unitSelect) {
                elements.unitSelect.addEventListener('change', updateCurrentUnit);
            }

            if (elements.price) {
                elements.price.addEventListener('input', () => {
                    validatePrice();
                    updateDiscountPercentage();
                });
            }

            if (elements.minQuantity) {
                elements.minQuantity.addEventListener('input', () => {
                    validateQuantity();
                    validatePrice(); // Revalidate price when quantity changes
                });
            }

            // Initialize on load
            updateCurrentUnit();
        });
    </script>
@endpush
