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
                    <h3 class="limited-product__subtitle">FLASH SALE! HARGA SPESIAL UNTUK WAKTU TERBATAS!</h3>
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
