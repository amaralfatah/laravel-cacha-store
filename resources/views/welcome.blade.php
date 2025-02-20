@extends('guest.layouts.app')

@section('content')
    <!-- Hero Section -->
    @include('guest.landing.hero')

    <!-- Categories -->
    <section class="py-5">
        <div class="container py-4">
            <div class="row">
                @foreach($categories as $category)
                    <div class="col-md-3 col-6 mb-4">
                        <div class="category-card text-center">
                            <div class="category-icon">
                                <img src="{{asset('images/logo-snack-circle.png')}}" alt="{{ $category->name }}"
                                     width="40">
                            </div>
                            <h5 class="fw-bold">{{ $category->name }}</h5>
                            <p class="text-muted small">{{ $category->products_count }} Produk</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Popular Products -->
    <section class="py-5 bg-light" id="popular">
        <div class="container py-5">
            <div class="section-title text-center">
                <h2>Produk Terlaris Bulan Ini</h2>
                <p class="text-muted">Cemilan yang paling banyak diburu anak muda</p>
            </div>

            <div class="row g-4">
                @foreach($bestSellers as $product)
                    <div class="col-lg-3 col-md-6">
                        <x-product-card
                                :product="$product"
                                button-size="small"
                                :show-badges="true"
                        />
                    </div>
                @endforeach
            </div>


            <div class="text-center mt-5">
                <a href="#products" class="btn btn-outline-primary-cacha px-4 py-2">
                    Lihat Semua Produk <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- All Products -->
    <section class="py-5" id="products">
        <div class="container py-4">
            <div class="section-title text-center">
                <h2>Katalog Produk Kami</h2>
                <p class="text-muted">Temukan berbagai cemilan kekinian khas Pangandaran</p>
            </div>

            <div class="row g-4">
                @foreach($catalogProducts as $product)
                    <div class="col-lg-4 col-md-6">
                        <x-product-card
                                :product="$product"
                                :show-badges="true"
                        />
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-5">
                <a href="{{route('guest.shop')}}" class="btn btn-outline-primary-cacha px-4 py-2">
                    Tampilkan Lainnya ({{$totalProducts}}+)<i class="fas fa-arrow-down ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Statistics -->
    <section class="py-5 bg-primary-cacha text-white">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center mb-5">
                    <h2 class="fw-bold mb-3">Cacha Snack dalam Angka</h2>
                    <p class="lead opacity-75">Cemilan kekinian terpercaya yang telah melayani ribuan pelanggan di
                        seluruh
                        Indonesia</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-3 col-6">
                    <div class="counter-item">
                        <div class="counter-value">{{ $statistics['product_variants'] }}+</div>
                        <div class="counter-label">Varian Produk</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="counter-item">
                        <div class="counter-value">{{ $statistics['satisfied_customers'] }}K+</div>
                        <div class="counter-label">Pelanggan Puas</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="counter-item">
                        <div class="counter-value">{{ $statistics['total_cities'] }}+</div>
                        <div class="counter-label">Kota Terjangkau</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="counter-item">
                        <div class="counter-value">{{ $statistics['marketplace_rating'] }}</div>
                        <div class="counter-label">Rating Marketplace</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    @include('guest.landing.testimonials')

    <!-- Benefits -->
    @include('guest.landing.benefits')

    <!-- Gallery -->
    <section class="py-5 bg-light" id="gallery">
        <div class="container py-4">
            <div class="section-title text-center">
                <h2>Galeri Produk</h2>
                <p class="text-muted">Jelajahi berbagai varian produk menarik dari Cacha Snack</p>
            </div>

            <div class="row g-4">
                @foreach($gallery as $image)
                    <div class="col-md-4 col-6">
                        <div class="gallery-item">
                            @php
                                $path = is_string($image['image_path'] ?? null) ? $image['image_path'] : $image->image_path;
                                $prefix = str_starts_with($path, 'images/') ? '' : 'storage/';
                            @endphp
                            <img src="{{ asset($prefix . $path) }}"
                                 alt="{{ is_string($image['image_path'] ?? null) ? $image['product']['name'] : $image->product->name }}"
                                 class="gallery-img">
                            <div class="gallery-overlay">
                                <div class="text-white">
                                    <a class="text-decoration-none text-primary-cacha"
                                       href="{{ route('guest.show', $product->slug) }}">
                                        <h6 class="mb-1 fw-bold">
                                            {{ is_string($image['image_path'] ?? null) ? $image['product']['name'] : $image->product->name }}
                                        </h6></a>
                                    <p class="small mb-0">
                                        {{ is_string($image['image_path'] ?? null) ?
                                            $image['product']['short_description'] :
                                            Str::limit($image->product->short_description, 50) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA -->
    @include('guest.landing.cta')

    <!-- FAQ -->
    @include('guest.landing.faq')

    <!-- Contact -->
    @include('guest.landing.contact')
@endsection
