<section class="hero-section" id="home">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <h5 class="fw-semibold mb-3">
                    <i class="fas fa-star me-2"></i>Cemilan Kekinian Asli Pangandaran
                </h5>
                <h1 class="display-4 fw-bold mb-4">Rasakan Sensasi Kelezatan <span
                        class="fw-extrabold">Snack Kekinian</span> Yang Bikin Nagih!</h1>
                <p class="lead mb-4 opacity-75">
                    Cemilan inovasi terkini dengan rasa unik, bahan berkualitas premium, dan kemasan trendy untuk
                    menemani seru-seruan kamu bareng squad!
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#popular" class="btn btn-light text-primary-cacha btn-lg px-4">
                        <i class="fas fa-fire me-2"></i>Produk Terlaris
                    </a>
                    <a href="#about" class="btn btn-outline-light btn-lg px-4">
                        <i class="fas fa-info-circle me-2"></i>Tentang Kami
                    </a>
                </div>
                <div class="mt-5 d-flex align-items-center">
                    <div class="d-flex">
                        <img src="{{asset('images/logo-snack-circle-light.png')}}"
                             class="rounded-circle border-2 border-white" alt="Customer"
                             style="max-width: 40px; max-height: 40px;">
                        <img src="{{asset('images/logo-snack-circle-light.png')}}"
                             class="rounded-circle border-2 border-white ms-n2" alt="Customer"
                             style="max-width: 40px; max-height: 40px;">
                        <img src="{{asset('images/logo-snack-circle-light.png')}}"
                             class="rounded-circle border-2 border-white ms-n2" alt="Customer"
                             style="max-width: 40px; max-height: 40px;">
                    </div>
                    <div class="ms-3">
                        <p class="mb-0 fw-semibold">4.9 <i class="fas fa-star text-warning"></i> dari 2,500+ review</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 position-relative hero-image">
                <img src="{{asset('images/products/slider-image-01.png')}}" alt="Aneka Snack Kekinian Cacha"
                     class="img-fluid rounded-4 shadow-lg" style="max-width: 600px; max-height: 500px;">
                <div
                    class="position-absolute top-0 end-0 mt-n4 me-n4 bg-white rounded-circle p-3 shadow-lg d-none d-md-block">
                    <div class="bg-primary-cacha text-white rounded-circle p-3 fw-bold"
                         style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                        <span class="fs-4">{{number_format($biggestDiscount->value)}}@if($biggestDiscount->type == 'percentage')%@endif</span>
                        <span class="small">DISKON</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
