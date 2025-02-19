<section class="homepage-slider mb--11pt5">
    <div class="element-carousel slick-right-bottom" data-slick-options='{
                    "slidesToShow": 1,
                    "dots": true
                }'>
        <div class="item">
            <div class="single-slide height-2 d-flex align-items-center bg-image"
                 data-bg-image="#">
                <div class="container">
                    <div class="row align-items-center g-0 w-100">
                        <div class="col-lg-6 col-md-8">
                            <div class="slider-content py-0">
                                <div class="slider-content__text mb--95 md-lg--80 mb-md--40 mb-sm--15">
                                    <h3 class="text-uppercase font-weight-light" data-animation="fadeInUp"
                                        data-duration=".3s" data-delay=".3s">KEMASAN KEREN, RASA JUARA</h3>
                                    <h1 class="heading__primary mb--40 mb-md--20" data-animation="fadeInUp"
                                        data-duration=".3s" data-delay=".3s" style="color: red">SNACK LOCAL PRIDE</h1>
                                    <p class="font-weight-light" data-animation="fadeInUp"
                                       data-duration=".3s" data-delay=".3s">Rasakan sensasi snack premium dalam kemasan kekinian yang Instagramable. Desain yang eye-catching dengan cita rasa yang bikin ketagihan untuk generasi yang ekspresif.</p>
                                </div>
                                <div class="slider-content__btn">
                                    <a href="{{ route('guest.shop') }}" class="btn" data-animation="fadeInUp"
                                       data-duration=".3s" data-delay=".6s" style="background-color:red; border: none">Belanja Sekarang</a>
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
                                        data-duration=".3s" data-delay=".3s">PREMIUM TASTE, STYLISH LOOK</h3>
                                    <h1 class="heading__primary mb--40 mb-md--20" data-animation="fadeInUp"
                                        data-duration=".3s" data-delay=".3s" style="color: red">SNACK WITH ATTITUDE</h1>
                                    <p class="font-weight-light" data-animation="fadeInUp"
                                       data-duration=".3s" data-delay=".3s">Tanpa pengawet, tanpa MSG, tapi penuh karakter. Cemilan kekinian dengan desain yang unik dan photogenic untuk gaya hidup yang aktif dan sosial-media worthy.</p>
                                </div>
                                <div class="slider-content__btn">
                                    <a href="{{ route('guest.shop') }}" class="btn" data-animation="fadeInUp"
                                       data-duration=".3s" data-delay=".6s" style="background-color:red; border: none">Belanja Sekarang</a>
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
