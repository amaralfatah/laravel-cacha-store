@extends('guest.layouts.app')

@section('header-class', '')

@section('breadcrumb')
    <section class="page-title-area bg-color" data-bg-color="#f4f4f4">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="page-title">Shop</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{route('guest.home')}}">Home</a></li>
                        <li class="current"><span>Shop</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <div class="shop-page-wrapper ptb--80">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="shop-toolbar mb--50">
                        <div class="row align-items-center">
                            <div class="col-md-5 mb-sm--30 mb-xs--10">
                                <div class="shop-toolbar__left">
                                    <div class="product-ordering">
                                        <select class="product-ordering__select nice-select">
                                            <option value="0">Default Sorting</option>
                                            <option value="1">Relevance</option>
                                            <option value="2">Name, A to Z</option>
                                            <option value="3">Name, Z to A</option>
                                            <option value="4">Price, low to high</option>
                                            <option value="5">Price, high to low</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="shop-toolbar__right">
                                    <p class="product-pages">Showing Result {{ $products->count() }} Among {{ $products->total() }}</p>
                                    <div class="product-view-mode ml--50 ml-xs--0">
                                        <a class="active" href="#" data-target="grid">
                                            <img src="{{asset('payne/assets/img/icons/grid.png')}}" alt="Grid">
                                        </a>
                                        <a href="#" data-target="list">
                                            <img src="{{asset('payne/assets/img/icons/list.png')}}" alt="Grid">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="shop-products">
                        <div class="row">
                            @forelse($products as $product)
                                <div class="col-xl-3 col-md-4 col-sm-6 mb--50">
                                    <div class="payne-product">
                                        <div class="product__inner">
                                            <div class="product__image">
                                                <figure class="product__image--holder">
                                                    <img src="{{ $product->image }}" alt="{{ $product->name }}">
                                                </figure>
                                                <a href="{{ route('guest.product-details', $product->slug) }}" class="product__overlay"></a>
                                                <div class="product__action">
                                                    <a data-bs-toggle="modal" data-bs-target="#productModal" class="action-btn" data-product-id="{{ $product->id }}">
                                                        <i class="fa fa-eye"></i>
                                                        <span class="sr-only">Quick View</span>
                                                    </a>
                                                    <a href="#" class="action-btn add-to-wishlist" data-product-id="{{ $product->id }}">
                                                        <i class="fa fa-heart-o"></i>
                                                        <span class="sr-only">Add to wishlist</span>
                                                    </a>
                                                    <a href="#" class="action-btn add-to-compare" data-product-id="{{ $product->id }}">
                                                        <i class="fa fa-repeat"></i>
                                                        <span class="sr-only">Add To Compare</span>
                                                    </a>
                                                    <a href="#" class="action-btn add-to-cart" data-product-id="{{ $product->id }}">
                                                        <i class="fa fa-shopping-cart"></i>
                                                        <span class="sr-only">Add To Cart</span>
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
                                    <div class="payne-product-list">
                                        <div class="product__inner">
                                            <figure class="product__image">
                                                <a href="{{ route('guest.product-details', $product->slug) }}" class="d-block">
                                                    <img src="{{ $product->image }}" alt="{{ $product->name }}">
                                                </a>
                                                <div class="product__thumbnail-action">
                                                    <a data-bs-toggle="modal" data-bs-target="#productModal" class="action-btn quick-view" data-product-id="{{ $product->id }}">
                                                        <i class="fa fa-eye"></i>
                                                        <span class="sr-only">Quick View</span>
                                                    </a>
                                                </div>
                                            </figure>
                                            <div class="product__info">
                                                <h3 class="product__title">
                                                    <a href="{{ route('guest.product-details', $product->slug) }}">{{ $product->name }}</a>
                                                </h3>
                                                <div class="product__price">
                                                    <span class="money">{{ number_format($product->price, 0, ',', '.') }}</span>
                                                    <span class="sign">Rp</span>
                                                </div>
                                                <span class="product__rating">
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                            </span>
                                                <p class="product__short-description">
                                                    {{ $product->short_description ?? 'Produk makanan ringan khas lokal dengan kualitas premium dan rasa autentik.' }}
                                                </p>
                                                <div class="d-flex product__list-action">
                                                    <a href="#" class="btn btn-size-sm add-to-cart" data-product-id="{{ $product->id }}">Add To Cart</a>
                                                    <a href="#" class="action-btn add-to-compare" data-product-id="{{ $product->id }}">
                                                        <i class="fa fa-repeat"></i>
                                                        <span class="sr-only">Add To Compare</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center">
                                    <p>Produk tidak tersedia saat ini</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{ $products->links('vendor.pagination.payne', ['class' => 'pagination-wrap']) }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Product sorting functionality
            $('.product-ordering__select').on('change', function() {
                let sortValue = $(this).val();
                let currentUrl = new URL(window.location.href);

                currentUrl.searchParams.set('sort', sortValue);
                window.location.href = currentUrl.toString();
            });

            // Set selected sort option based on URL
            let urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('sort')) {
                $('.product-ordering__select').val(urlParams.get('sort'));
            }

            // Category filter functionality
            $('.category-filter').on('click', function(e) {
                e.preventDefault();
                let categoryId = $(this).data('category-id');
                let currentUrl = new URL(window.location.href);

                currentUrl.searchParams.set('category', categoryId);
                window.location.href = currentUrl.toString();
            });
        });
    </script>
@endsection
