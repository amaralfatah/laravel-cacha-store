@extends('guest.layouts.app')

@section('content')
    <!-- Slider area Start -->
    <section class="homepage-slider mb--11pt5">
        <div class="element-carousel slick-right-bottom" data-slick-options='{
                    "slidesToShow": 1,
                    "dots": true
                }'>
            <div class="item">
                <div class="single-slide d-flex align-items-center bg-image"
                     data-bg-image="{{asset('payne/assets/img/slider/slider-bg-01.jpg')}}">
                    <div class="container">
                        <div class="row align-items-center g-0 w-100">
                            <div class="col-lg-6 col-md-8">
                                <div class="slider-content">
                                    <div class="slider-content__text mb--95 md-lg--80 mb-md--40 mb-sm--15">
                                        <h3 class="text-uppercase font-weight-light" data-animation="fadeInUp"
                                            data-duration=".3s" data-delay=".3s">AMAZING PRODUCT!</h3>
                                        <h1 class="heading__primary mb--40 mb-md--20" data-animation="fadeInUp"
                                            data-duration=".3s" data-delay=".3s">BACKPACK</h1>
                                        <p class="font-weight-light" data-animation="fadeInUp"
                                           data-duration=".3s" data-delay=".3s">Neque porro quisquam est, qui
                                            dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed
                                            quia non numquam eius modi
                                            tempora Neque porro quisquam est, qui dolorem ipsum</p>
                                    </div>
                                    <div class="slider-content__btn">
                                        <a href="{{route('guest.shop')}}" class="btn-link" data-animation="fadeInUp"
                                           data-duration=".3s" data-delay=".6s">Shop Now</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 offset-lg-2 col-md-4">
                                <figure class="slider-image d-none d-md-block">
                                    <img src="{{asset('payne/assets/img/slider/slider-image-01.png')}}" alt="Slider Image">
                                </figure>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="single-slide d-flex align-items-center bg-image"
                     data-bg-image="{{asset('payne/assets/img/slider/slider-bg-01.jpg')}}">
                    <div class="container">
                        <div class="row align-items-center g-0 w-100">
                            <div class="col-lg-6 col-md-8">
                                <div class="slider-content py-0">
                                    <div class="slider-content__text mb--95 md-lg--80 mb-md--40 mb-sm--15">
                                        <h3 class="text-uppercase font-weight-light" data-animation="fadeInUp"
                                            data-duration=".3s" data-delay=".3s">AMAZING PRODUCT!</h3>
                                        <h1 class="heading__primary mb--40 mb-md--20" data-animation="fadeInUp"
                                            data-duration=".3s" data-delay=".3s">BACKPACK</h1>
                                        <p class="font-weight-light" data-animation="fadeInUp"
                                           data-duration=".3s" data-delay=".3s">Neque porro quisquam est, qui
                                            dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed
                                            quia non numquam eius modi
                                            tempora Neque porro quisquam est, qui dolorem ipsum</p>
                                    </div>
                                    <div class="slider-content__btn">
                                        <a href="{{route('guest.shop')}}" class="btn-link" data-animation="fadeInUp"
                                           data-duration=".3s" data-delay=".6s">Shop Now</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 offset-lg-2 col-md-4">
                                <figure class="slider-image d-none d-md-block">
                                    <img src="{{asset('payne/assets/img/slider/slider-image-02.png')}}" alt="Slider Image">
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
                    <h2 class="sr-only">Featured Product</h2>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-md-6 mb-sm--50">
                    <div class="featured-product">
                        <div class="featured-product__inner info-center">
                            <figure class="featured-product__image">
                                <img src="{{asset('payne/assets/img/products/product-01-500x466.jpg')}}" alt="Featured Product">
                            </figure>
                            <div class="featured-product__info wow pbounceInLeft" data-wow-delay=".3s" data-wow-duration="1s">
                                <div class="featured-product__info-inner">
                                    <h4 class="featured-product__text">Amazing Product!</h4>
                                    <h2 class="featured-product__name">Bisco Bag</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="featured-product">
                        <div class="featured-product__inner info-left-bottom">
                            <figure class="featured-product__image">
                                <img src="{{asset('payne/assets/img/products/product-02-500x575.jpg')}}" alt="Featured Product">
                            </figure>
                            <div class="featured-product__info wow pbounceInDown" data-wow-duration="1s">
                                <div class="featured-product__info-inner rotated-info">
                                    <h4 class="featured-product__text">Special Offer <strong>39%</strong> Off</h4>
                                    <h2 class="featured-product__name">Feedo Bag</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Featured Product Area End -->

    <!-- Product Area Start -->
    <section class="product-area mb--50 mb-xl--40 mb-lg--25 mb-md--30 mb-sm--20 mb-xs--15">
        <div class="container">
            <div class="row mb--42">
                <div class="col-lg-5 col-sm-10">
                    <h2 class="heading__secondary">NEW ARRIVALS</h2>
                    <p>Neque porro quisquam est, qui dolorem ipsum quia dolor ipisci velit, sed quia non numquam
                        eius modi </p>
                </div>
            </div>
            <div class="row">
{{--                start - max 4 product--}}
                <div class="col-lg-3 col-md-4 col-sm-6 mb--65 mb-md--50">
                    <div class="payne-product">
                        <div class="product__inner">
                            <div class="product__image">
                                <figure class="product__image--holder">
                                    <img src="{{asset('payne/assets/img/products/product-03-270x300.jpg')}}" alt="Product">
                                </figure>
                                <a href="product-details.html" class="product-overlay"></a>
                                <div class="product__action">
                                    <a data-bs-toggle="modal" data-bs-target="#productModal" class="action-btn">
                                        <i class="fa fa-eye"></i>
                                        <span class="sr-only">Quick View</span>
                                    </a>
                                    <a href="wishlist.html" class="action-btn">
                                        <i class="fa fa-heart-o"></i>
                                        <span class="sr-only">Add to wishlist</span>
                                    </a>
                                    <a href="wishlist.html" class="action-btn">
                                        <i class="fa fa-repeat"></i>
                                        <span class="sr-only">Add To Compare</span>
                                    </a>
                                    <a href="cart.html" class="action-btn">
                                        <i class="fa fa-shopping-cart"></i>
                                        <span class="sr-only">Add To Cart</span>
                                    </a>
                                </div>
                            </div>
                            <div class="product__info">
                                <div class="product__info--left">
                                    <h3 class="product__title">
                                        <a href="product-details.html">Lexbaro Begadi</a>
                                    </h3>
                                    <div class="product__price">
                                        <span class="money">132.00</span>
                                        <span class="sign">$</span>
                                    </div>
                                </div>
                                <div class="product__info--right">
                                            <span class="product__rating">
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                            </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
{{--                end - max 4 product--}}
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
                            <h2>Backpack</h2>
                        </div>
                        <div class="limited-product__large-image">
                            <div class="element-carousel main-slider" data-slick-options='{
                                        "slidesToShow": 1,
                                        "asNavFor": ".nav-slider"
                                    }'>
                                <div class="item">
                                    <figure>
                                        <img src="{{asset('payne/assets/img/products/product-11-321x450.png')}}"
                                             alt="Countdown Product">
                                    </figure>
                                </div>
                                <div class="item">
                                    <figure>
                                        <img src="{{asset('payne/assets/img/products/product-12-321x450.png')}}"
                                             alt="Countdown Product">
                                    </figure>
                                </div>
                                <div class="item">
                                    <figure>
                                        <img src="{{asset('payne/assets/img/products/product-13-321x450.png')}}"
                                             alt="Countdown Product">
                                    </figure>
                                </div>
                            </div>
                        </div>
                        <div class="limited-product__nav-image">
                            <div class="element-carousel nav-slider" data-slick-options='{
                                        "spaceBetween": 25,
                                        "slidesToShow": 3,
                                        "vertical": true,
                                        "focusOnSelect": true,
                                        "asNavFor": ".main-slider"
                                    }'
                                 data-slick-responsive='[
                                        {"breakpoint": 576, "settings": { "vertical": false }}
                                    ]'
                            >
                                <div class="item">
                                    <figure>
                                        <img src="{{asset('payne/assets/img/products/product-11-123x127.jpg')}}"
                                             alt="Product Nav Image">
                                    </figure>
                                </div>
                                <div class="item">
                                    <figure>
                                        <img src="{{asset('payne/assets/img/products/product-12-123x127.jpg')}}"
                                             alt="Product Nav Image">
                                    </figure>
                                </div>
                                <div class="item">
                                    <figure>
                                        <img src="{{asset('payne/assets/img/products/product-13-123x127.jpg')}}"
                                             alt="Product Nav Image">
                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 offset-xl-1 col-lg-6">
                    <div class="limited-product__info">
                        <h2 class="limited-product__name">
                            <a href="product-details.html">BLINGO BACKPACK</a>
                        </h2>
                        <p class="limited-product__desc">Neque porro quisquam est, qui dolorem ipsum quia dolor
                            ipisci velit, sed quia non numquam eius modi </p>
                        <div class="d-flex align-items-center">
                            <div class="limited-product__price">
                                <span class="money">162</span>
                                <span class="sign">$</span>
                            </div>
                            <span class="limited-product__rating">
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                    </span>
                        </div>
                        <h3 class="limited-product__subtitle">BEST DEAL, LIMITED TIME OFFER GET YOUR’S NOW!</h3>
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
                        <a href="{{route('guest.shop')}}" class="btn-link">Shop Now</a>
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
                    <h2 class="sr-only">Featured Product</h2>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-md-6 mb-sm--50">
                    <div class="featured-product">
                        <div class="featured-product__inner info-right-bottom">
                            <figure class="featured-product__image">
                                <img src="{{asset('payne/assets/img/products/product-14-500x575.jpg')}}" alt="Featured Product">
                            </figure>
                            <div class="featured-product__info wow pbounceInDown" data-wow-delay=".6s" data-wow-duration=".8s">
                                <div class="featured-product__info-inner rotated-info">
                                    <h4 class="featured-product__text">Special Offer <strong>39%</strong> Off</h4>
                                    <h2 class="featured-product__name">Feedo Bag</h2>
                                </div>
                            </div>
                            <span class="featured-product__badge badge-top-left">53% off</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="featured-product">
                        <div class="featured-product__inner info-center">
                            <figure class="featured-product__image">
                                <img src="{{asset('payne/assets/img/products/product-15-500x466.jpg')}}" alt="Featured Product">
                            </figure>
                            <div class="featured-product__info wow pbounceInLeft" data-wow-delay=".3s" data-wow-duration=".8s">
                                <div class="featured-product__info-inner">
                                    <h4 class="featured-product__text">Mega Sale Offer</h4>
                                    <h2 class="featured-product__name">Maxica Bag</h2>
                                </div>
                            </div>
                            <span class="featured-product__badge">53% off</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Featured Product Area End -->

    <section class="method-area mb--11pt5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="sr-only">Methods</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-sm--50">
                    <div class="method-box shipment-method">
                        <i class="flaticon-truck"></i>
                        <h3>Free Home Delivery</h3>
                    </div>
                </div>
                <div class="col-md-4 mb-sm--50">
                    <div class="method-box money-back-method">
                        <i class="flaticon-money"></i>
                        <h3>MONEY BACK GUARANTEE</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="method-box support-method">
                        <i class="flaticon-support"></i>
                        <h3>SUPORT 24/7</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
