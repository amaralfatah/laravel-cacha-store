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
