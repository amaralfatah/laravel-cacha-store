<!-- resources/views/components/product-price.blade.php -->
@props(['product'])

<div class="product-price">
    @php
        $defaultUnit = $product->productUnits->where('is_default', true)->first();
        if ($defaultUnit) {
            $price = $defaultUnit->selling_price;
            if ($product->discount) {
                $discountAmount = $product->discount->type === 'percentage'
                    ? ($price * $product->discount->value / 100)
                    : $product->discount->value;
                $discountedPrice = $price - $discountAmount;
            }
        }
    @endphp

    @if(isset($discountedPrice))
        <span>Rp{{ number_format($discountedPrice, 0, ',', '.') }}</span>
        <span class="product-price-old">
            Rp{{ number_format($price, 0, ',', '.') }}
        </span>
    @else
        Rp{{ number_format($price ?? 0, 0, ',', '.') }}
    @endif
</div>
