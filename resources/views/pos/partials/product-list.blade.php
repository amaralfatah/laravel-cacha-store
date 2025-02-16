@if($products->isEmpty())
    <div class="product-item">Tidak ada produk ditemukan</div>
@else
    @foreach($products as $product)
        <div class="product-item">
            <form action="{{ route('pos.get-product') }}" method="GET">
                <input type="hidden" name="barcode" value="{{ $product->barcode }}">
                <button type="submit" class="btn btn-link text-decoration-none p-0">
                    {{ $product->name }} - {{ $product->barcode }}
                    <small class="text-muted d-block">
                        Stok: {{ optional($product->defaultUnit)->stock ?? 0 }}
                        {{ optional(optional($product->defaultUnit)->unit)->name ?? '-' }}
                    </small>
                </button>
            </form>
        </div>
    @endforeach
@endif
