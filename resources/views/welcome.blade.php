@extends('guest.layouts.app')

@section('header-class', '')

@section('content')
    <!-- Slider area Start -->
    <section class="homepage-slider mb--11pt5">
        <div class="element-carousel slick-right-bottom" data-slick-options='{
                    "slidesToShow": 1,
                    "dots": true
                }'>
            <div class="item">
                <div class="single-slide height-2 d-flex align-items-center bg-image"
                     data-bg-image="{{asset('payne/assets/img/slider/slider-bg-02.jpg')}}">
                    <div class="container">
                        <div class="row align-items-center g-0 w-100">
                            <div class="col-lg-6 col-md-8">
                                <div class="slider-content py-0">
                                    <div class="slider-content__text mb--95 md-lg--80 mb-md--40 mb-sm--15">
                                        <h3 class="text-uppercase font-weight-light" data-animation="fadeInUp"
                                            data-duration=".3s" data-delay=".3s">SNACK TRADISIONAL PREMIUM</h3>
                                        <h1 class="heading__primary mb--40 mb-md--20" data-animation="fadeInUp"
                                            data-duration=".3s" data-delay=".3s">CEMILAN LOKAL</h1>
                                        <p class="font-weight-light" data-animation="fadeInUp"
                                           data-duration=".3s" data-delay=".3s">Nikmati beragam pilihan cemilan tradisional khas Indonesia dengan kualitas premium. Dibuat dengan bahan-bahan pilihan dan resep otentik yang diwariskan turun-temurun.</p>
                                    </div>
                                    <div class="slider-content__btn">
                                        <a href="{{ route('guest.shop') }}" class="btn-link" data-animation="fadeInUp"
                                           data-duration=".3s" data-delay=".6s">Belanja Sekarang</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5 offset-lg-1 col-md-4">
                                <figure class="slider-image d-none d-md-block">
                                    <img src="{{asset('images/products/slider-image-01.png')}}" alt="Slider Image">
                                </figure>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="single-slide height-2 d-flex align-items-center bg-image"
                     data-bg-image="{{asset('payne/assets/img/slider/slider-bg-02.jpg')}}">
                    <div class="container">
                        <div class="row align-items-center g-0 w-100">
                            <div class="col-lg-6 col-md-8">
                                <div class="slider-content py-0">
                                    <div class="slider-content__text mb--95 md-lg--80 mb-md--40 mb-sm--15">
                                        <h3 class="text-uppercase font-weight-light" data-animation="fadeInUp"
                                            data-duration=".3s" data-delay=".3s">CAMILAN SEHAT & BERKUALITAS</h3>
                                        <h1 class="heading__primary mb--40 mb-md--20" data-animation="fadeInUp"
                                            data-duration=".3s" data-delay=".3s">SNACK TRADISIONAL</h1>
                                        <p class="font-weight-light" data-animation="fadeInUp"
                                           data-duration=".3s" data-delay=".3s">Tanpa pengawet, tanpa MSG, dan dibuat dengan standar kebersihan tinggi. Nikmati sensasi rasa autentik dari berbagai daerah di Indonesia dalam kemasan yang modern dan higienis.</p>
                                    </div>
                                    <div class="slider-content__btn">
                                        <a href="{{ route('guest.shop') }}" class="btn-link" data-animation="fadeInUp"
                                           data-duration=".3s" data-delay=".6s">Belanja Sekarang</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 offset-lg-2 col-md-4">
                                <figure class="slider-image d-none d-md-block">
                                    <img src="{{asset('images/products/slider-image-02.png')}}" alt="Slider Image">
                                </figure>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Slider area End -->

    <!-- Featured Product Area Start -->
    <section class="featured-product-area mb--10pt8">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="sr-only">Produk Unggulan</h2>
                </div>
            </div>
            <div class="row">
                @foreach($featuredProducts as $index => $product)
                    <div class="col-md-4 mb-sm--50">
                        <div class="featured-product text-md-start text-center p-0">
                            <div class="featured-product__inner info-left-center">
                                <figure class="featured-product__image">
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}">
                                </figure>
                                <div class="featured-product__info wow pbounceInDown" data-wow-delay="{{ $index * 0.3 + 0.3 }}s"
                                     data-wow-duration=".8s">
                                    <div class="featured-product__info-inner rotated-info">
                                        <h4 class="featured-product__text font-size-14 text-light" >Produk Unggulan</h4>
                                        <h2 class="featured-product__name font-size-34 text-light" >
                                            <a href="{{ route('guest.product-details', $product->slug) }}">{{ $product->name }}</a></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- Featured Product Area End -->

    <!-- Product Area Start -->
    <section class="product-area mb--50 mb-xl--40 mb-lg--25 mb-md--30 mb-sm--20">
        <div class="container">
            <div class="row mb--42">
                <div class="col-xl-5 col-lg-6 col-sm-10">
                    <h2 class="heading__secondary">PRODUK TERBARU</h2>
                    <p>Temukan produk-produk terbaru kami yang baru saja ditambahkan ke koleksi snack tradisional premium.</p>
                </div>
            </div>
            <div class="row">
                @foreach($newArrivals as $product)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb--65 mb-md--50">
                        <div class="payne-product">
                            <div class="product__inner">
                                <div class="product__image">
                                    <figure class="product__image--holder">
                                        <img src="{{ $product->image }}" alt="{{ $product->name }}">
                                    </figure>
                                    <a href="#" class="product-overlay"></a>
                                    <div class="product__action">
                                        <a data-bs-toggle="modal" data-bs-target="#productModal" class="action-btn" data-product-id="{{ $product->id }}">
                                            <i class="fa fa-eye"></i>
                                            <span class="sr-only">Lihat Cepat</span>
                                        </a>
                                        <a href="#" class="action-btn add-to-wishlist" data-product-id="{{ $product->id }}">
                                            <i class="fa fa-heart-o"></i>
                                            <span class="sr-only">Tambahkan ke wishlist</span>
                                        </a>
                                        <a href="#" class="action-btn add-to-compare" data-product-id="{{ $product->id }}">
                                            <i class="fa fa-repeat"></i>
                                            <span class="sr-only">Bandingkan</span>
                                        </a>
                                        <a href="#" class="action-btn add-to-cart" data-product-id="{{ $product->id }}">
                                            <i class="fa fa-shopping-cart"></i>
                                            <span class="sr-only">Tambahkan ke Keranjang</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="product__info">
                                    <div class="product__info--left">
                                        <h3 class="product__title">
                                            <a href="{{ route('guest.product-details', $product->slug) }}">{{ $product->name }}</a>
                                        </h3>
                                        <div class="product__price">
                                            <span class="money">{{ number_format($product->price, 0, ',', '.') }}</span>
                                            <span class="sign">Rp</span>
                                        </div>
                                    </div>
                                    <div class="product__info--right">
                                    <span class="product__rating">
                                        @for ($i = 0; $i < 5; $i++)
                                            <i class="fa fa-star"></i>
                                        @endfor
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- Product Area End -->

    <!-- Banner Area Start -->
    <section class="banner-area mb--11pt5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="sr-only">Banner Section</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-lg--50">
                    <div class="banner">
                        <div class="banner__inner">
                            <div class="banner__info bg-image"
                                 data-bg-image="{{('payne/assets/img/banner/banner-bg-01.jpg')}}">
                                <div class="banner__info-inner">
                                    <h2 class="banner__title">CEMILAN TRADISIONAL</h2>
                                    <a href="{{ route('guest.shop') }}" class="banner__btn">Belanja Sekarang</a>
                                    <p class="banner__text">DISKON HINGGA 26% UNTUK PEMBELIAN PERTAMA</p>
                                </div>
                            </div>
                            <figure class="banner__image">
                                <img src="{{asset('payne/assets/img/banner/banner-01.jpg')}}" alt="Banner" class="w-100">
                            </figure>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="banner">
                        <div class="banner__inner">
                            <div class="banner__info bg-image"
                                 data-bg-image="{{('payne/assets/img/banner/banner-bg-01.jpg')}}">
                                <div class="banner__info-inner">
                                    <h2 class="banner__title">SNACK PREMIUM</h2>
                                    <a href="{{ route('guest.shop') }}" class="banner__btn">Belanja Sekarang</a>
                                    <p class="banner__text">GRATIS ONGKIR UNTUK PEMBELIAN MINIMAL RP100.000</p>
                                </div>
                            </div>
                            <figure class="banner__image">
                                <img src="{{asset('payne/assets/img/banner/banner-02.jpg')}}" alt="Banner" class="w-100">
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Banner Area End -->

    <!-- Product Area Start -->
    <section class="product-area mb--50 mb-xl--40 mb-lg--25 mb-md--30 mb-sm--20">
        <div class="container">
            <div class="row mb--42">
                <div class="col-xl-5 col-lg-6 col-sm-10">
                    <h2 class="heading__secondary">Produk Populer</h2>
                    <p>Temukan produk-produk snack tradisional yang paling banyak diminati oleh pelanggan kami.</p>
                </div>
            </div>
            <div class="row">
                @foreach($popularProducts as $product)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb--65 mb-md--50">
                        <div class="payne-product">
                            <div class="product__inner">
                                <div class="product__image">
                                    <figure class="product__image--holder">
                                        <img src="{{ $product->image }}" alt="{{ $product->name }}">
                                    </figure>
                                    <a href="#" class="product-overlay"></a>
                                    <div class="product__action">
                                        <a data-bs-toggle="modal" data-bs-target="#productModal" class="action-btn" data-product-id="{{ $product->id }}">
                                            <i class="fa fa-eye"></i>
                                            <span class="sr-only">Lihat Cepat</span>
                                        </a>
                                        <a href="#" class="action-btn add-to-wishlist" data-product-id="{{ $product->id }}">
                                            <i class="fa fa-heart-o"></i>
                                            <span class="sr-only">Tambahkan ke wishlist</span>
                                        </a>
                                        <a href="#" class="action-btn add-to-compare" data-product-id="{{ $product->id }}">
                                            <i class="fa fa-repeat"></i>
                                            <span class="sr-only">Bandingkan</span>
                                        </a>
                                        <a href="#" class="action-btn add-to-cart" data-product-id="{{ $product->id }}">
                                            <i class="fa fa-shopping-cart"></i>
                                            <span class="sr-only">Tambahkan ke Keranjang</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="product__info">
                                    <div class="product__info--left">
                                        <h3 class="product__title">
                                            <a href="{{ route('guest.product-details', $product->slug) }}">{{ $product->name }}</a>
                                        </h3>
                                        <div class="product__price">
                                            <span class="money">{{ number_format($product->price, 0, ',', '.') }}</span>
                                            <span class="sign">Rp</span>
                                        </div>
                                    </div>
                                    <div class="product__info--right">
                                    <span class="product__rating">
                                        @for ($i = 0; $i < 5; $i++)
                                            <i class="fa fa-star"></i>
                                        @endfor
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- Product Area End -->

    <!-- Countdown Product Area Start -->
    <div class="limited-product-area mb--11pt5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-md--40 mb-sm--45">
                    <div class="limited-product__image">
                        <div class="limited-product__title">
                            <h2>{{ $countdownProduct->name }}</h2>
                        </div>
                        <div class="limited-product__large-image">
                            <div class="element-carousel main-slider" data-slick-options='{
                                        "slidesToShow": 1,
                                        "asNavFor": ".nav-slider"
                                    }'>
                                @foreach($countdownProduct->mainImages as $image)
                                    <div class="item">
                                        <figure>
                                            <img src="{{ $image }}" alt="{{ $countdownProduct->name }}">
                                        </figure>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="limited-product__nav-image">
                            <div class="element-carousel nav-slider" data-slick-options='{
                                        "spaceBetween": 25,
                                        "slidesToShow": 3,
                                        "vertical": true,
                                        "focusOnSelect": true,
                                        "asNavFor": ".main-slider"
                                    }' data-slick-responsive='[
                                        {"breakpoint": 576, "settings": { "vertical": false }}
                                    ]'>
                                @foreach($countdownProduct->thumbImages as $image)
                                    <div class="item">
                                        <figure>
                                            <img src="{{ $image }}" alt="{{ $countdownProduct->name }} thumbnail">
                                        </figure>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 offset-xl-1 col-lg-6">
                    <div class="limited-product__info">
                        <h2 class="limited-product__name">
                            <a href="#">{{ $countdownProduct->name }}</a>
                        </h2>
                        <p class="limited-product__desc">{{ $countdownProduct->short_description }}</p>
                        <div class="d-flex align-items-center">
                            <div class="limited-product__price">
                                <span class="money">{{ $countdownProduct->price }}</span>
                                <span class="sign">Rp</span>
                            </div>
                            <span class="limited-product__rating">
                                @for ($i = 0; $i < 5; $i++)
                                    <i class="fa fa-star"></i>
                                @endfor
                            </span>
                        </div>
                        <h3 class="limited-product__subtitle">PENAWARAN TERBAIK, WAKTU TERBATAS. DAPATKAN SEKARANG!</h3>
                        <div class="limited-product__countdown">
                            <div class="countdown-wrap">
                                <div class="countdown" data-countdown="2025/10/01" data-format="short">
                                    <div class="countdown__item">
                                        <span class="countdown__time daysLeft"></span>
                                        <span class="countdown__text daysText"></span>
                                    </div>
                                    <div class="countdown__item">
                                        <span class="countdown__time hoursLeft"></span>
                                        <span class="countdown__text hoursText"></span>
                                    </div>
                                    <div class="countdown__item">
                                        <span class="countdown__time minsLeft"></span>
                                        <span class="countdown__text minsText"></span>
                                    </div>
                                    <div class="countdown__item">
                                        <span class="countdown__time secsLeft"></span>
                                        <span class="countdown__text secsText"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('guest.shop') }}" class="btn-link">Belanja Sekarang</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Countdown Product Area End -->

    <!-- Featured Product Area Start -->
    <section class="featured-product-area mb--11pt5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="sr-only">Produk Promo</h2>
                </div>
            </div>
            <div class="row align-items-center">
                @foreach($promotionProducts as $index => $product)
                    <div class="col-md-6 {{ $index > 0 ? '' : 'mb-sm--50' }}">
                        <div class="featured-product">
                            <div class="featured-product__inner {{ $index > 0 ? 'info-center' : 'info-right-bottom' }}">
                                <figure class="featured-product__image">
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}">
                                </figure>
                                <div class="featured-product__info wow {{ $index > 0 ? 'pbounceInLeft' : 'pbounceInDown' }}" data-wow-delay="{{ $index * 0.3 + 0.3 }}s"
                                     data-wow-duration=".8s">
                                    <div class="featured-product__info-inner {{ $index > 0 ? '' : 'rotated-info' }}">
                                        <h4 class="featured-product__text text-light">Penawaran Spesial{{ $index > 0 ? ' Minggu Ini' : '' }}</h4>
                                        <h2 class="featured-product__name text-light">{{ $product->name }}</h2>
                                    </div>
                                </div>
                                <span class="featured-product__badge {{ $index > 0 ? '' : 'badge-top-left' }}">Diskon</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- Featured Product Area End -->

    <section class="method-area mb--11pt5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="sr-only">Keuntungan Berbelanja</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-sm--50">
                    <div class="method-box shipment-method">
                        <i class="flaticon-truck"></i>
                        <h3>Pengiriman Gratis</h3>
                    </div>
                </div>
                <div class="col-md-4 mb-sm--50">
                    <div class="method-box money-back-method">
                        <i class="flaticon-money"></i>
                        <h3>JAMINAN UANG KEMBALI</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="method-box support-method">
                        <i class="flaticon-support"></i>
                        <h3>LAYANAN 24/7</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Quick view product
            $('.action-btn').on('click', function() {
                const productId = $(this).data('product-id');
                // Implementasi AJAX untuk quick view
            });

            // Add to cart functionality
            $('.add-to-cart').on('click', function(e) {
                e.preventDefault();
                const productId = $(this).data('product-id');
                // Implementasi AJAX untuk add to cart
            });
        });
    </script>
@endsection
