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
                                    <p class="product-pages">Showing Result  08 Among  72</p>
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
{{--                            start - max 8 item--}}
                            <div class="col-xl-3 col-md-4 col-sm-6 mb--50">
                                <div class="payne-product">
                                    <div class="product__inner">
                                        <div class="product__image">
                                            <figure class="product__image--holder">
                                                <img src="{{asset('payne/assets/img/products/product-03-270x300.jpg')}}" alt="Product">
                                            </figure>
                                            <a href="{{route('guest.product-details')}}" class="product__overlay"></a>
                                            <div class="product__action">
                                                <a data-bs-toggle="modal" data-bs-target="#productModal" class="action-btn">
                                                    <i class="fa fa-eye"></i>
                                                    <span class="sr-only">Quick View</span>
                                                </a>
                                                <a href="wishlist.html" class="action-btn">
                                                    <i class="fa fa-heart-o"></i>
                                                    <span class="sr-only">Add to wishlist</span>
                                                </a>
                                                <a href="compare.html" class="action-btn">
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
                                                    <a href="{{route('guest.product-details')}}">Lexbaro Begadi</a>
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
                                <div class="payne-product-list">
                                    <div class="product__inner">
                                        <figure class="product__image">
                                            <a href="{{route('guest.product-details')}}" class="d-block">
                                                <img src="{{asset('payne/assets/img/products/product-03-270x300.jpg')}}" alt="Products">
                                            </a>
                                            <div class="product__thumbnail-action">
                                                <a data-bs-toggle="modal" data-bs-target="#productModal" class="action-btn quick-view">
                                                    <i class="fa fa-eye"></i>
                                                    <span class="sr-only">Quick View</span>
                                                </a>
                                            </div>
                                        </figure>
                                        <div class="product__info">
                                            <h3 class="product__title">
                                                <a href="{{route('guest.product-details')}}">Lexbaro Begadi</a>
                                            </h3>
                                            <div class="product__price">
                                                <span class="money">132.00</span>
                                                <span class="sign">$</span>
                                            </div>
                                            <span class="product__rating">
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                    </span>
                                            <p class="product__short-description">
                                                Donec accumsan auctor iaculis. Sed suscipit arcu ligula, at egestas magna molestie a. Proin ac ex maximus, ultrices justo eget, sodales orci. Aliquam egestas libero ac turpis pharetra
                                            </p>
                                            <div class="d-flex product__list-action">
                                                <a href="cart.html" class="btn btn-size-sm">Add To Cart</a>
                                                <a href="compare.html" class="action-btn">
                                                    <i class="fa fa-repeat"></i>
                                                    <span class="sr-only">Add To Compare</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
{{--                            end - max 8 item--}}
                        </div>
                    </div>
                    <nav class="pagination-wrap">
                        <ul class="pagination">
                            <li><span class="page-number current">1</span></li>
                            <li><a href="#" class="page-number">2</a></li>
                            <li><span class="dot"></span></li>
                            <li><span class="dot"></span></li>
                            <li><span class="dot"></span></li>
                            <li><a href="#" class="page-number">16</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection
