@props([
    'category' => null,
    'name' => '',
    'count' => 0,
    'iconSrc' => asset('images/logo-snack-circle.png'),
    'iconAlt' => 'Category Icon',
])

@php
    $categoryName = $category ? $category->name : $name;
    $productCount = $category ? $category->products_count : $count;
@endphp

<div class="x-category-card">
    <div class="x-category-icon">
        <img src="{{ $iconSrc }}" alt="{{ $iconAlt }}" width="40">
        <div class="x-category-glow"></div>
    </div>
    <h5 class="x-category-title">{{ $categoryName }}</h5>
    <p class="x-category-count">{{ $productCount }} Produk</p>
    <div class="x-category-hover-effect"></div>
</div>
