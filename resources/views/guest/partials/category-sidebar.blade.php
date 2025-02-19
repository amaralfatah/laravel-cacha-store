{{-- This component can be included in the shop.blade.php if you need a category sidebar --}}
<div class="shop-sidebar">
    <div class="shop-widget mb--40">
        <h3 class="widget-title mb--25">Kategori Produk</h3>
        <ul class="widget-list category-list">
            <li>
                <a href="{{ route('guest.shop') }}" class="category-filter {{ !request()->has('category') ? 'active' : '' }}">
                    Semua Produk
                    <span class="count">({{ \App\Models\Product::where('is_active', true)->count() }})</span>
                </a>
            </li>
            @foreach($categories as $category)
                <li>
                    <a href="{{ route('guest.shop', ['category' => $category->id]) }}"
                       class="category-filter {{ request('category') == $category->id ? 'active' : '' }}"
                       data-category-id="{{ $category->id }}">
                        {{ $category->name }}
                        <span class="count">({{ \App\Models\Product::where('category_id', $category->id)->where('is_active', true)->count() }})</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
