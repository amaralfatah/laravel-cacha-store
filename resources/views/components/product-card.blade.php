<!-- resources/views/components/product-card.blade.php -->
@props([
    'product',
    'showBadges' => true,
    'buttonSize' => 'normal',
    'showDiscount' => true,
])

<div class="card product-card h-100">
    <div class="product-image">
        <a href="{{ route('guest.show', $product->slug) }}">
            <img src="{{ $product->productImages->where('is_primary', true)->first()
                ? asset('storage/' . $product->productImages->where('is_primary', true)->first()->image_path)
                : asset('images/placeholder.png') }}"
                 alt="{{ $product->name }}"
                 class="card-img-top">
        </a>
        @if($showDiscount && $product->discount)
            <span class="badge bg-danger badge-product">
                -{{ $product->discount->value }}{{ $product->discount->type === 'percentage' ? '%' : 'Rp' }}
            </span>
        @endif

        @if($showBadges)
            @php
                $defaultUnit = $product->productUnits->where('is_default', true)->first();
                $stock = $defaultUnit ? $defaultUnit->stock : 0;
                $createdDate = $product->created_at;
                $isNew = $createdDate && $createdDate->diffInDays(now()) <= 30;
            @endphp

            @if($product->total_sold >= 100)
                <span class="badge bg-danger badge-product">
                    Terjual {{ $product->total_sold }}+
                </span>
            @elseif($stock <= 10 && $stock > 0)
                <span class="badge bg-warning text-dark badge-product">
                    Stok Tersisa {{ $stock }}
                </span>
            @elseif($isNew)
                <span class="badge bg-success badge-product">
                    Produk Baru
                </span>
            @endif
        @endif
    </div>

    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge bg-primary-light text-primary-cacha px-3 py-2 rounded-pill">
                {{ $product->category->name }}
            </span>
            <x-rating-stars :total="$product->total_sold ?? 0" />
        </div>

        <h5 class="card-title fw-bold mb-3">{{ $product->name }}</h5>
        <p class="card-text text-muted small mb-3">
            {{ Str::limit($product->short_description, 60) }}
        </p>

        <div class="d-flex justify-content-between align-items-center">
            <x-product-price :product="$product" />
            <a href="{{$product->url}}" @class([
                'btn btn-primary-cacha rounded-pill',
                'btn-sm px-2' => $buttonSize === 'small',
                'px-3' => $buttonSize === 'normal'
            ])>
                <i class="fas fa-cart-plus {{ $buttonSize === 'normal' ? 'me-1' : '' }}"></i>
                @if($buttonSize === 'normal')
                    Beli
                @endif
            </a>
        </div>
    </div>
</div>
