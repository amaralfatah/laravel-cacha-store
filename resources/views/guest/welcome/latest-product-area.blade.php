<section class="product-area mb--50 mb-xl--40 mb-lg--25 mb-md--30 mb-sm--20">
    <div class="container">
        <div class="row mb--42">
            <div class="col-xl-5 col-lg-6 col-sm-10">
                <h2 class="heading__secondary">PRODUK TERBARU</h2>
                <p>Temukan produk-produk terbaru kami yang baru saja ditambahkan ke koleksi snack premium dengan kemasan kekinian.</p>
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
