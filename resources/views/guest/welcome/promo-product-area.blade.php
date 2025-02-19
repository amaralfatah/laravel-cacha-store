
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
