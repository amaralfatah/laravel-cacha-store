@extends('guest.layouts.app')

@section('header-class', '')

@section('breadcrumb')
    <section class="page-title-area bg-color" data-bg-color="#f4f4f4">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="page-title">{{ $product->name }}</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ route('guest.home') }}">Home</a></li>
                        <li><a href="{{ route('guest.shop') }}">Shop</a></li>
                        <li class="current"><span>{{ $product->name }}</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <div class="page-content-inner pt--80 pt-md--60">
        <div class="container">
            <div class="row g-0 mb--80 mb-md--57">
                <div class="col-lg-7 product-main-image">
                    <div class="product-image">
                        <div class="product-gallery vertical-slide-nav">
                            <div class="product-gallery__large-image mb-sm--30">
                                <div class="product-gallery__wrapper">
                                    <div class="element-carousel main-slider image-popup" data-slick-options='{
                                                "slidesToShow": 1,
                                                "slidesToScroll": 1,
                                                "infinite": true,
                                                "arrows": false,
                                                "asNavFor": ".nav-slider"
                                            }'>
                                        @foreach($product->productImages as $image)
                                            <div class="item">
                                                <figure class="product-gallery__image zoom">
                                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                                         alt="{{ $product->name }}">
                                                    @if($product->productUnits->whereNotNull('discount_id')->count() > 0)
                                                        <span class="product-badge sale">Sale</span>
                                                    @endif
                                                    <div class="product-gallery__actions">
                                                        <button class="action-btn btn-zoom-popup"><i
                                                                class="fa fa-eye"></i></button>
                                                        @if(isset($product->video_url))
                                                            <a href="{{ $product->video_url }}"
                                                               class="action-btn video-popup"><i
                                                                    class="fa fa-play"></i></a>
                                                        @endif
                                                    </div>
                                                </figure>
                                            </div>
                                        @endforeach

                                        @if($product->productImages->count() == 0)
                                            <div class="item">
                                                <figure class="product-gallery__image zoom">
                                                    <img src="{{ asset('assets/img/products/default-snack-500x555.jpg') }}"
                                                         alt="{{ $product->name }}">
                                                    @if($product->productUnits->whereNotNull('discount_id')->count() > 0)
                                                        <span class="product-badge sale">Sale</span>
                                                    @endif
                                                    <div class="product-gallery__actions">
                                                        <button class="action-btn btn-zoom-popup"><i
                                                                class="fa fa-eye"></i></button>
                                                    </div>
                                                </figure>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="product-gallery__nav-image">
                                <div class="element-carousel nav-slider product-slide-nav slick-center-bottom"
                                     data-slick-options='{
                                            "spaceBetween": 10,
                                            "slidesToShow": 3,
                                            "slidesToScroll": 1,
                                            "vertical": true,
                                            "swipe": true,
                                            "verticalSwiping": true,
                                            "infinite": true,
                                            "focusOnSelect": true,
                                            "asNavFor": ".main-slider",
                                            "arrows": true,
                                            "prevArrow": {"buttonClass": "slick-btn slick-prev", "iconClass": "fa fa-angle-up" },
                                            "nextArrow": {"buttonClass": "slick-btn slick-next", "iconClass": "fa fa-angle-down" }
                                        }' data-slick-responsive='[
                                            {
                                                "breakpoint":1200,
                                                "settings": {
                                                    "slidesToShow": 2
                                                }
                                            },
                                            {
                                                "breakpoint":992,
                                                "settings": {
                                                    "slidesToShow": 3
                                                }
                                            },
                                            {
                                                "breakpoint":767,
                                                "settings": {
                                                    "slidesToShow": 4,
                                                    "vertical": false
                                                }
                                            },
                                            {
                                                "breakpoint":575,
                                                "settings": {
                                                    "slidesToShow": 3,
                                                    "vertical": false
                                                }
                                            },
                                            {
                                                "breakpoint":480,
                                                "settings": {
                                                    "slidesToShow": 2,
                                                    "vertical": false
                                                }
                                            }
                                        ]'>
                                    @foreach($product->productImages as $image)
                                        <div class="item">
                                            <figure class="product-gallery__nav-image--single">
                                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                                     alt="{{ $product->name }}">
                                            </figure>
                                        </div>
                                    @endforeach

                                    @if($product->productImages->count() == 0)
                                        <div class="item">
                                            <figure class="product-gallery__nav-image--single">
                                                <img src="{{ asset('assets/img/products/default-snack-270x300.jpg') }}"
                                                     alt="{{ $product->name }}">
                                            </figure>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 offset-xl-1 col-lg-5 product-main-details mt-md--50">
                    <div class="product-summary pl-lg--30 pl-md--0">
                        <div class="product-navigation text-end mb--20">
                            <a href="#" class="prev"><i class="fa fa-angle-double-left"></i></a>
                            <a href="#" class="next"><i class="fa fa-angle-double-right"></i></a>
                        </div>
                        <div class="product-rating d-flex mb--20">
                            <div class="star-rating star-five">
                                <span>Rated <strong class="rating">5.00</strong> out of 5</span>
                            </div>
                        </div>
                        <h3 class="product-title mb--20">{{ $product->name }}</h3>
                        <p class="product-short-description mb--20">{{ $product->short_description }}</p>
                        <div class="product-price-wrapper mb--25">
                            @php
                                $defaultUnit = $product->productUnits->where('is_default', true)->first();
                                $price = $defaultUnit ? $defaultUnit->selling_price : 0;
                                $hasDiscount = $defaultUnit && $defaultUnit->discount_id;
                                $discountPrice = $hasDiscount ? ($defaultUnit->selling_price * 0.8) : null; // Assuming 20% discount
                            @endphp

                            @if($hasDiscount)
                                <span class="money">Rp {{ number_format($discountPrice, 0, ',', '.') }}</span>
                                <span class="price-separator">-</span>
                                <span class="money old-price">Rp {{ number_format($price, 0, ',', '.') }}</span>
                            @else
                                <span class="money">Rp {{ number_format($price, 0, ',', '.') }}</span>
                            @endif
                        </div>

                        @if($product->productUnits->count() > 1)
                            <form action="#" class="variation-form mb--20">
                                <div class="product-size-variations d-flex align-items-center mb--15">
                                    <p class="variation-label">Ukuran:</p>
                                    <div class="product-size-variation variation-wrapper">
                                        @foreach($product->productUnits as $productUnit)
                                            <div class="variation">
                                                <a class="product-size-variation-btn {{ $productUnit->is_default ? 'selected' : '' }}"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   data-unit-id="{{ $productUnit->id }}"
                                                   data-price="{{ $productUnit->selling_price }}"
                                                   title="{{ $productUnit->unit->name }}">
                                                    <span class="product-size-variation-label">{{ $productUnit->unit->code }}</span>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <a href="#" class="reset_variations">Clear</a>
                            </form>
                        @endif

                        <div class="product-action d-flex flex-sm-row align-items-sm-center flex-column align-items-start mb--30">
                            <div class="quantity-wrapper d-flex align-items-center mr--30 mr-xs--0 mb-xs--30">
                                <label class="quantity-label" for="pro-qty">Quantity:</label>
                                <div class="quantity">
                                    <input type="number" class="quantity-input" name="pro-qty" id="pro-qty"
                                           value="1" min="1" max="{{ $defaultUnit ? $defaultUnit->stock : 10 }}">
                                </div>
                            </div>
                            <button type="button" class="btn btn-shape-square btn-size-sm" id="add-to-cart"
                                    data-product-id="{{ $product->id }}"
                                    data-unit-id="{{ $defaultUnit ? $defaultUnit->id : '' }}">
                                Add To Cart
                            </button>
                        </div>
                        <div class="product-footer-meta">
                            <p><span>Category:</span>
                                <a href="{{ route('guest.shop', ['category' => $product->category->id]) }}">{{ $product->category->name }}</a>
                            </p>
                            @if($product->supplier)
                                <p><span>Brand:</span>
                                    <a href="#">{{ $product->supplier->name }}</a>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center mb--77 mb-md--57">
                <div class="col-12">
                    <div class="tab-style-1">
                        <div class="nav nav-tabs mb--35 mb-sm--25" id="product-tab" role="tablist">
                            <button type="button" class="nav-link active" id="nav-description-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-description" role="tab" aria-selected="true">
                                <span>Description</span>
                            </button>
                            <button type="button" class="nav-link" id="nav-info-tab" data-bs-toggle="tab" data-bs-target="#nav-info" role="tab"
                                    aria-selected="true">
                                <span>Additional Information</span>
                            </button>
                            <button type="button" class="nav-link" id="nav-reviews-tab" data-bs-toggle="tab" data-bs-target="#nav-reviews"
                                    role="tab" aria-selected="true">
                                <span>Reviews</span>
                            </button>
                        </div>
                        <div class="tab-content" id="product-tabContent">
                            <div class="tab-pane fade show active" id="nav-description" role="tabpanel"
                                 aria-labelledby="nav-description-tab">
                                <div class="product-description">
                                    {!! $product->description ?? '<p>Deskripsi produk belum tersedia</p>' !!}

                                    @if(!$product->description)
                                        <p>{{ $product->name }} merupakan produk snack tradisional berkualitas tinggi. Diproduksi dengan standar kebersihan yang ketat dan menggunakan bahan-bahan pilihan terbaik untuk memberikan pengalaman menikmati snack yang tiada duanya.</p>

                                        <p>Cocok untuk disantap kapan saja, bersama keluarga maupun teman. Kemasan dirancang agar produk tetap renyah dan segar hingga saat dikonsumsi.</p>

                                        <h5 class="product-description__heading">Keunggulan Produk:</h5>
                                        <ul>
                                            <li><i class="fa fa-circle"></i><span>Terbuat dari bahan alami pilihan</span></li>
                                            <li><i class="fa fa-circle"></i><span>Tanpa pengawet berbahaya</span></li>
                                            <li><i class="fa fa-circle"></i><span>Diproses dengan teknologi modern namun tetap mempertahankan cita rasa tradisional</span></li>
                                        </ul>
                                    @endif
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-info" role="tabpanel"
                                 aria-labelledby="nav-info-tab">
                                <div class="table-content table-responsive">
                                    <table class="table shop_attributes">
                                        <tbody>
                                        <tr>
                                            <th>Berat</th>
                                            <td>{{ $defaultUnit ? $defaultUnit->unit->name : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Varian</th>
                                            <td>{{ $product->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kategori</th>
                                            <td>
                                                <a href="{{ route('guest.shop', ['category' => $product->category->id]) }}">{{ $product->category->name }}</a>
                                            </td>
                                        </tr>
                                        @if($product->barcode)
                                            <tr>
                                                <th>Kode Produk</th>
                                                <td>{{ $product->barcode }}</td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-reviews" role="tabpanel"
                                 aria-labelledby="nav-reviews-tab">
                                <div class="product-reviews">
                                    <h3 class="review__title">Customer Reviews</h3>
                                    <div class="review-form-wrapper">
                                        <div class="row">
                                            <div class="col-lg-8">
                                                <span class="reply-title">Add a review</span>
                                                <form action="#" class="form pr--30">
                                                    <div class="form-notes mb--20">
                                                        <p>Your email address will not be published. Required
                                                            fields are marked <span class="required">*</span>
                                                        </p>
                                                    </div>
                                                    <div class="form__group mb--10">
                                                        <label class="form__label d-block mb--10">Your Ratings</label>
                                                        <div class="rating">
                                                            <span><i class="fa fa-star"></i></span>
                                                            <span><i class="fa fa-star"></i></span>
                                                            <span><i class="fa fa-star"></i></span>
                                                            <span><i class="fa fa-star"></i></span>
                                                            <span><i class="fa fa-star"></i></span>
                                                        </div>
                                                    </div>
                                                    <div class="form__group mb--10">
                                                        <label class="form__label d-block mb--10" for="review">Your
                                                            Review<span class="required">*</span></label>
                                                        <textarea name="review" id="review"
                                                                  class="form__input form__input--textarea"></textarea>
                                                    </div>
                                                    <div class="form__group mb--20">
                                                        <label class="form__label d-block mb--10" for="name">Name<span
                                                                class="required">*</span></label>
                                                        <input type="text" name="name" id="name"
                                                               class="form__input">
                                                    </div>
                                                    <div class="form__group mb--20">
                                                        <label class="form__label d-block mb--10"
                                                               for="email">Email<span
                                                                class="required">*</span></label>
                                                        <input type="email" name="email" id="email"
                                                               class="form__input">
                                                    </div>
                                                    <div class="form__group">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <input type="submit" value="Submit Review"
                                                                       class="btn btn-size-md">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb--77 mb-md--57">
                <div class="col-12">
                    <div class="section-title mb--30">
                        <h2>Related Products</h2>
                    </div>
                    <div class="element-carousel slick-vertical-center" data-slick-options='{
                                "spaceBetween": 30,
                                "slidesToShow": 4,
                                "slidesToScroll": 1,
                                "arrows": true,
                                "prevArrow": {"buttonClass": "slick-btn slick-prev", "iconClass": "la la-angle-double-left" },
                                "nextArrow": {"buttonClass": "slick-btn slick-next", "iconClass": "la la-angle-double-right" }
                            }' data-slick-responsive='[
                                {"breakpoint":1199, "settings": {
                                    "slidesToShow": 3
                                }},
                                {"breakpoint":991, "settings": {
                                    "slidesToShow": 2
                                }},
                                {"breakpoint":575, "settings": {
                                    "slidesToShow": 1
                                }}
                            ]'>

                        @foreach($relatedProducts as $relatedProduct)
                            <div class="item">
                                <div class="payne-product">
                                    <div class="product__inner">
                                        <div class="product__image">
                                            <figure class="product__image--holder">
                                                <img src="{{ $relatedProduct->image }}" alt="{{ $relatedProduct->name }}">
                                            </figure>
                                            <a href="{{ route('guest.product-details', $relatedProduct->slug) }}" class="product__overlay"></a>
                                            <div class="product__action">
                                                <a data-bs-toggle="modal" data-bs-target="#productModal"
                                                   class="action-btn quick-view" data-product-id="{{ $relatedProduct->id }}">
                                                    <i class="fa fa-eye"></i>
                                                    <span class="sr-only">Quick View</span>
                                                </a>
                                                <a href="#" class="action-btn add-to-wishlist" data-product-id="{{ $relatedProduct->id }}">
                                                    <i class="fa fa-heart-o"></i>
                                                    <span class="sr-only">Add to wishlist</span>
                                                </a>
                                                <a href="#" class="action-btn add-to-compare" data-product-id="{{ $relatedProduct->id }}">
                                                    <i class="fa fa-repeat"></i>
                                                    <span class="sr-only">Add To Compare</span>
                                                </a>
                                                <a href="#" class="action-btn add-to-cart" data-product-id="{{ $relatedProduct->id }}">
                                                    <i class="fa fa-shopping-cart"></i>
                                                    <span class="sr-only">Add To Cart</span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="product__info">
                                            <div class="product__info--left">
                                                <h3 class="product__title">
                                                    <a href="{{ route('guest.product-details', $relatedProduct->slug) }}">{{ $relatedProduct->name }}</a>
                                                </h3>
                                                <div class="product__price">
                                                    <span class="money">{{ number_format($relatedProduct->price, 0, ',', '.') }}</span>
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
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Handle unit selection
            $('.product-size-variation-btn').on('click', function(e) {
                e.preventDefault();

                // Update selected class
                $('.product-size-variation-btn').removeClass('selected');
                $(this).addClass('selected');

                // Update price display
                var price = $(this).data('price');
                var formattedPrice = new Intl.NumberFormat('id-ID').format(price);
                $('.product-price-wrapper .money').text('Rp ' + formattedPrice);

                // Update unit ID for add to cart button
                $('#add-to-cart').data('unit-id', $(this).data('unit-id'));
            });

            // Handle add to cart
            $('#add-to-cart').on('click', function() {
                var productId = $(this).data('product-id');
                var unitId = $(this).data('unit-id');
                var quantity = $('#pro-qty').val();

                // Add to cart functionality
                // This would typically be an AJAX call to your cart controller
                console.log('Adding to cart:', productId, unitId, quantity);

                // Show success message
                alert('Product added to cart!');
            });
        });
    </script>
@endsection
