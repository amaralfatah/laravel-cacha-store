<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cacha Snack - Cemilan Kekinian Asli Pangandaran</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{--    @include('guest.partials.styles')--}}
    <style>
        :root {
            --primary: #FF3B30;
            --primary-light: #FFE8E7;
            --secondary: #FFC107;
            --dark: #212529;
            --light: #F8F9FA;
        }

        body {
            font-family: 'Outfit', sans-serif;
            overflow-x: hidden;
        }

        .text-primary-cacha {
            color: var(--primary);
        }

        .bg-primary-cacha {
            background-color: var(--primary);
        }

        .bg-primary-light {
            background-color: var(--primary-light);
        }

        .btn-primary-cacha {
            background-color: var(--primary);
            border-color: var(--primary);
            color: white;
            border-radius: 30px;
            padding: 10px 24px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary-cacha:hover {
            background-color: #E42D22;
            border-color: #E42D22;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 59, 48, 0.3);
        }

        .btn-outline-primary-cacha {
            background-color: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 30px;
            padding: 9px 24px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-outline-primary-cacha:hover {
            background-color: var(--primary);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 59, 48, 0.2);
        }

        .hero-section {
            background: linear-gradient(135deg, #FF3B30 0%, #FF6B61 100%);
            padding-top: 8rem;
            padding-bottom: 6rem;
            position: relative;
            overflow: hidden;
            color: white;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -150px;
            right: -150px;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            bottom: -100px;
            left: -100px;
        }

        .navbar {
            transition: all 0.4s;
            padding: 15px 0;
        }

        .navbar.scrolled {
            background-color: white !important;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            padding: 10px 0;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 800;
        }

        .nav-link {
            font-weight: 500;
            margin: 0 5px;
            position: relative;
            transition: all 0.3s;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background-color: var(--primary);
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            transition: width 0.3s;
        }

        .nav-link:hover::after {
            width: 80%;
        }

        .category-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .category-card:hover .category-icon {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(255, 59, 48, 0.15);
        }

        .product-card {
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s;
            height: 100%;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            overflow: hidden;
            position: relative;
        }

        .product-image img {
            transition: transform 0.5s;
            height: 200px;
            object-fit: cover;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
        }

        .product-price-old {
            font-size: 0.9rem;
            font-weight: normal;
            text-decoration: line-through;
            color: #6c757d;
            margin-left: 5px;
        }

        .badge-product {
            position: absolute;
            top: 15px;
            right: 15px;
            border-radius: 50px;
            padding: 8px 15px;
            font-weight: 600;
            z-index: 2;
        }

        .rating-stars {
            color: var(--secondary);
        }

        .section-title {
            position: relative;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-weight: 700;
            margin-bottom: 15px;
            display: inline-block;
            position: relative;
        }

        .section-title h2:after {
            content: '';
            position: absolute;
            width: 50%;
            height: 4px;
            background-color: var(--primary);
            bottom: -10px;
            left: 25%;
            border-radius: 2px;
        }

        .testimonial-card {
            border-radius: 20px;
            padding: 30px;
            transition: all 0.3s;
            height: 100%;
        }

        .testimonial-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .testimonial-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary);
        }

        .benefit-icon {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background-color: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            transition: all 0.3s;
        }

        .benefit-card:hover .benefit-icon {
            transform: rotateY(180deg);
            background-color: var(--primary);
            color: white;
        }

        .gallery-item {
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .gallery-img {
            transition: all 0.5s;
            height: 250px;
            object-fit: cover;
            width: 100%;
        }

        .gallery-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.7) 100%);
            display: flex;
            align-items: flex-end;
            padding: 20px;
            opacity: 0;
            transition: all 0.3s;
        }

        .gallery-item:hover .gallery-img {
            transform: scale(1.1);
        }

        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }

        .floating-shape {
            position: absolute;
            z-index: -1;
            opacity: 0.1;
        }

        .counter-item {
            text-align: center;
            padding: 20px;
            border-radius: 15px;
            background-color: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .counter-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .counter-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .counter-label {
            font-size: 1rem;
            color: var(--dark);
            font-weight: 500;
        }

        .logo-container {
            border-radius: 50%;
            background-color: white;
            padding: 5px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .cta-section {
            background: linear-gradient(135deg, #FF3B30 0%, #FF6B61 100%);
            padding: 70px 0;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -150px;
            right: -150px;
        }

        .footer {
            padding: 80px 0 30px;
            background-color: #212529;
            color: rgba(255, 255, 255, 0.8);
        }

        .footer-title {
            color: white;
            font-weight: 700;
            margin-bottom: 25px;
            position: relative;
        }

        .footer-title:after {
            content: '';
            position: absolute;
            width: 50px;
            height: 3px;
            background-color: var(--primary);
            bottom: -10px;
            left: 0;
        }

        .footer-links li {
            margin-bottom: 15px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
        }

        .footer-links a:hover {
            color: var(--primary);
            padding-left: 5px;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            background-color: #212529;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            transition: all 0.3s;
        }

        .social-icon:hover {
            background-color: var(--primary);
            transform: translateY(-5px);
        }

        .scroll-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background-color: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
            z-index: 99;
        }

        .scroll-to-top.active {
            opacity: 1;
            visibility: visible;
        }

        .scroll-to-top:hover {
            background-color: #E42D22;
            transform: translateY(-5px);
        }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                background-color: white;
                padding: 20px;
                border-radius: 10px;
                margin-top: 15px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }

            .hero-section {
                padding-top: 7rem;
                padding-bottom: 4rem;
                text-align: center;
            }

            .hero-image {
                margin-top: 40px;
            }

            .section-title {
                margin-bottom: 30px;
            }
        }
    </style>

</head>
<body>
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top bg-white">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <div class="logo-container me-2">
                <img src="{{asset('images/products/telur-gabus.png')}}" alt="Cacha Snack Logo" height="40">
            </div>
            <span class="text-primary-cacha">Cacha Snack</span>
        </a>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#home">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#popular">Terlaris</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#products">Produk</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#testimonials">Testimoni</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">Tentang Kami</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact">Kontak</a>
                </li>
                <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                    <a class="btn btn-primary-cacha" href="#order">
                        <i class="fas fa-shopping-cart me-2"></i>Beli Sekarang
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
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
                        <img src="{{asset('images/products/telur-gabus.png')}}"
                             class="rounded-circle border-2 border-white" alt="Customer"
                             style="max-width: 40px; max-height: 40px;">
                        <img src="{{asset('images/products/telur-gabus.png')}}"
                             class="rounded-circle border-2 border-white ms-n2" alt="Customer"
                             style="max-width: 40px; max-height: 40px;">
                        <img src="{{asset('images/products/telur-gabus.png')}}"
                             class="rounded-circle border-2 border-white ms-n2" alt="Customer"
                             style="max-width: 40px; max-height: 40px;">
                    </div>
                    <div class="ms-3">
                        <p class="mb-0 fw-semibold">4.9 <i class="fas fa-star text-warning"></i> dari 2,500+ review</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 position-relative hero-image">
                <img src="{{asset('images/products/slider-image-02.png')}}" alt="Aneka Snack Kekinian Cacha"
                     class="img-fluid rounded-4 shadow-lg" style="max-width: 600px; max-height: 500px;">
                <div
                    class="position-absolute top-0 end-0 mt-n4 me-n4 bg-white rounded-circle p-3 shadow-lg d-none d-md-block">
                    <div class="bg-primary-cacha text-white rounded-circle p-3 fw-bold"
                         style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                        <span class="fs-4">30%</span>
                        <span class="small">DISKON</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="py-5">
    <div class="container py-4">
        <div class="row">
            <div class="col-md-3 col-6 mb-4">
                <div class="category-card text-center">
                    <div class="category-icon">
                        <img src="{{asset('images/products/telur-gabus.png')}}" alt="Keripik" width="40">
                    </div>
                    <h5 class="fw-bold">Keripik</h5>
                    <p class="text-muted small">12 Produk</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="category-card text-center">
                    <div class="category-icon">
                        <img src="{{asset('images/products/telur-gabus.png')}}" alt="Makaroni" width="40">
                    </div>
                    <h5 class="fw-bold">Makaroni</h5>
                    <p class="text-muted small">8 Produk</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="category-card text-center">
                    <div class="category-icon">
                        <img src="{{asset('images/products/telur-gabus.png')}}" alt="Basreng" width="40">
                    </div>
                    <h5 class="fw-bold">Basreng</h5>
                    <p class="text-muted small">6 Produk</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="category-card text-center">
                    <div class="category-icon">
                        <img src="{{asset('images/products/telur-gabus.png')}}" alt="Kacang" width="40">
                    </div>
                    <h5 class="fw-bold">Kacang</h5>
                    <p class="text-muted small">9 Produk</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Popular Products -->
<section class="py-5 bg-light" id="popular">
    <div class="container py-5">
        <div class="section-title text-center">
            <h2>Produk Terlaris Bulan Ini</h2>
            <p class="text-muted">Cemilan yang paling banyak diburu anak muda</p>
        </div>

        <div class="row g-4">
            <!-- Product 1 - 4 -->
            @for($i = 0; $i < 4; $i++)
                <div class="col-lg-3 col-md-6">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            @if($i == 0)
                                <span class="badge bg-danger badge-product">Terlaris</span>
                            @elseif($i == 1)
                                <span class="badge bg-warning text-dark badge-product">Limited</span>
                            @elseif($i == 2)
                                <span class="badge bg-success badge-product">Baru</span>
                            @endif
                            <div class="product-image">
                                <img src="{{asset('images/products/telur-gabus.png')}}" alt="Makaroni Pedas"
                                     class="card-img-top">
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span
                                    class="badge bg-primary-light text-primary-cacha px-3 py-2 rounded-pill">Makaroni</span>
                                <div class="rating-stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                    <span class="ms-1 small">(432)</span>
                                </div>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Makaroni Pedas</h5>
                            <p class="card-text text-muted small mb-3">Makaroni dengan bumbu pedas khas Pangandaran yang
                                bikin ketagihan. Tersedia dalam 3 level kepedasan.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="product-price">
                                    Rp15.000 <span class="product-price-old">Rp20.000</span>
                                </div>
                                <a href="#" class="btn btn-sm btn-primary-cacha rounded-pill">
                                    <i class="fas fa-cart-plus"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>

        <div class="text-center mt-5">
            <a href="#products" class="btn btn-outline-primary-cacha px-4 py-2">
                Lihat Semua Produk <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- All Products -->
<section class="py-5" id="products">
    <div class="container py-4">
        <div class="section-title text-center">
            <h2>Katalog Produk Kami</h2>
            <p class="text-muted">Temukan berbagai cemilan kekinian khas Pangandaran</p>
        </div>

        <div class="row g-4">
            <!-- Product 1 - 6 -->
            @for($i = 0; $i < 6; $i++)
                <div class="col-lg-4 col-md-6">
                    <div class="card product-card h-100">
                        <div class="position-relative">

                            @if($i == 0)
                                <span class="badge bg-danger badge-product">Terlaris</span>
                            @elseif($i == 1)
                                <span class="badge bg-warning text-dark badge-product">Limited</span>
                            @elseif($i == 3)
                                <span class="badge bg-success badge-product">Baru</span>
                            @elseif($i == 5)
                                <span class="badge bg-info badge-product">Sehat</span>
                            @endif

                            <div class="product-image">
                                <img src="{{asset('images/products/telur-gabus.png')}}" alt="Makaroni Pedas Level 3"
                                     class="card-img-top">
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span
                                    class="badge bg-primary-light text-primary-cacha px-3 py-2 rounded-pill">Makaroni</span>
                                <div class="rating-stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                    <span class="ms-1 small">(432)</span>
                                </div>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Makaroni Pedas Level 3</h5>
                            <p class="card-text text-muted small mb-3">Makaroni dengan bumbu pedas autentik khas
                                Pangandaran, level 3 untuk yang berani tantangan!</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="product-price">
                                    Rp15.000 <span class="product-price-old">Rp22.000</span>
                                </div>
                                <a href="#" class="btn btn-primary-cacha rounded-pill px-3">
                                    <i class="fas fa-cart-plus me-1"></i> Beli
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>

        <div class="text-center mt-5">
            <a href="#" class="btn btn-outline-primary-cacha px-4 py-2">
                Tampilkan Lainnya (12) <i class="fas fa-arrow-down ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Statistics -->
<section class="py-5 bg-primary-cacha text-white">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center mb-5">
                <h2 class="fw-bold mb-3">Cacha Snack dalam Angka</h2>
                <p class="lead opacity-75">Cemilan kekinian terpercaya yang telah melayani ribuan pelanggan di seluruh
                    Indonesia</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="counter-item">
                    <div class="counter-value">35+</div>
                    <div class="counter-label">Varian Produk</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="counter-item">
                    <div class="counter-value">15K+</div>
                    <div class="counter-label">Pelanggan Puas</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="counter-item">
                    <div class="counter-value">150+</div>
                    <div class="counter-label">Kota Terjangkau</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="counter-item">
                    <div class="counter-value">4.9</div>
                    <div class="counter-label">Rating Marketplace</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-5 bg-light" id="testimonials">
    <div class="container py-5">
        <div class="section-title text-center">
            <h2>Apa Kata Mereka?</h2>
            <p class="text-muted">Pengalaman para pelanggan kami yang sudah merasakan kelezatan Cacha Snack</p>
        </div>

        <div class="row g-4">
            <!-- Testimonial 1 -->
            <div class="col-md-4">
                <div class="testimonial-card bg-white shadow-sm h-100">
                    <div class="d-flex align-items-center mb-4">
                        <img src="{{asset('images/products/telur-gabus.png')}}" alt="Testimoni" class="testimonial-img">
                        <div class="ms-3">
                            <h5 class="mb-1 fw-bold">Riska Amelia</h5>
                            <div class="rating-stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mb-3">"Makaroni pedasnya juara banget! Level 3-nya bener-bener bikin keringetan tapi
                        nagih. Packaging-nya juga kekinian banget, cocok buat anak muda. Pengiriman cepat dan customer
                        service-nya ramah."</p>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <small class="text-muted">Surabaya, Jawa Timur</small>
                        <small class="text-muted">2 hari yang lalu</small>
                    </div>
                </div>
            </div>

            <!-- Testimonial 2 -->
            <div class="col-md-4">
                <div class="testimonial-card bg-white shadow-sm h-100">
                    <div class="d-flex align-items-center mb-4">
                        <img src="{{asset('images/products/telur-gabus.png')}}" alt="Testimoni" class="testimonial-img">
                        <div class="ms-3">
                            <h5 class="mb-1 fw-bold">Budi Santoso</h5>
                            <div class="rating-stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mb-3">"Telur gabus kejunya enak banget, teksturnya pas dan rasanya gurih. Cocok banget
                        buat cemilan nonton Netflix. Udah repeat order 3 kali dan selalu konsisten kualitasnya.
                        Recommended!"</p>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <small class="text-muted">Jakarta Selatan</small>
                        <small class="text-muted">1 minggu yang lalu</small>
                    </div>
                </div>
            </div>

            <!-- Testimonial 3 -->
            <div class="col-md-4">
                <div class="testimonial-card bg-white shadow-sm h-100">
                    <div class="d-flex align-items-center mb-4">
                        <img src="{{asset('images/products/telur-gabus.png')}}" alt="Testimoni" class="testimonial-img">
                        <div class="ms-3">
                            <h5 class="mb-1 fw-bold">Dina Fitriani</h5>
                            <div class="rating-stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mb-3">"Cilok garingnya jadi cemilan favorit di kantor. Rasanya otentik banget, berasa lagi
                        jajan di Pangandaran. Porsinya juga pas dan harganya terjangkau. Pasti bakal jadi langganan
                        nih!"</p>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <small class="text-muted">Bandung, Jawa Barat</small>
                        <small class="text-muted">3 hari yang lalu</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="#" class="btn btn-primary-cacha px-4 py-2">
                Lihat Semua Review <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Benefits -->
<section class="py-5" id="about">
    <div class="container py-4">
        <div class="section-title text-center">
            <h2>Mengapa Pilih Cacha Snack?</h2>
            <p class="text-muted">Keunggulan yang membuat kami berbeda dari yang lain</p>
        </div>

        <div class="row g-4">
            <!-- Benefit 1 -->
            <div class="col-md-6 col-lg-3">
                <div class="benefit-card text-center">
                    <div class="benefit-icon">
                        <i class="fas fa-medal text-primary-cacha fa-2x"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Kualitas Premium</h5>
                    <p class="text-muted">Kami hanya menggunakan bahan berkualitas terbaik untuk memastikan cita rasa
                        yang konsisten dan sempurna.</p>
                </div>
            </div>

            <!-- Benefit 2 -->
            <div class="col-md-6 col-lg-3">
                <div class="benefit-card text-center">
                    <div class="benefit-icon">
                        <i class="fas fa-shipping-fast text-primary-cacha fa-2x"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Pengiriman Cepat</h5>
                    <p class="text-muted">Dengan jaringan logistik yang luas, kami memastikan produk sampai ke tangan
                        Anda dengan cepat dan aman.</p>
                </div>
            </div>

            <!-- Benefit 3 -->
            <div class="col-md-6 col-lg-3">
                <div class="benefit-card text-center">
                    <div class="benefit-icon">
                        <i class="fas fa-leaf text-primary-cacha fa-2x"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Ramah Lingkungan</h5>
                    <p class="text-muted">Kemasan produk kami menggunakan material yang dapat didaur ulang untuk menjaga
                        kelestarian lingkungan.</p>
                </div>
            </div>

            <!-- Benefit 4 -->
            <div class="col-md-6 col-lg-3">
                <div class="benefit-card text-center">
                    <div class="benefit-icon">
                        <i class="fas fa-thumbs-up text-primary-cacha fa-2x"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Bebas Pengawet</h5>
                    <p class="text-muted">Produk kami bebas dari bahan pengawet berbahaya, sehingga aman dikonsumsi
                        untuk semua kalangan.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Gallery -->
<section class="py-5 bg-light" id="gallery">
    <div class="container py-4">
        <div class="section-title text-center">
            <h2>Galeri Produk</h2>
            <p class="text-muted">Lihat berbagai produk Cacha Snack dalam kemasan menarik</p>
        </div>

        <div class="row g-4">
            @for($i = 0; $i < 6; $i++)
                <div class="col-md-4 col-6">
                    <div class="gallery-item">
                        <img src="{{asset('images/products/slider-image-02.png')}}" alt="Gallery" class="gallery-img">
                        <div class="gallery-overlay">
                            <div class="text-white">
                                <h6 class="mb-1 fw-bold">Makaroni Pedas</h6>
                                <p class="small mb-0">Level 3 Super Pedas</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section text-white" id="order">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="fw-bold mb-4">Dapatkan Diskon 25% untuk Pembelian Pertama!</h2>
                <p class="lead mb-4">Gunakan kode promo <span
                        class="bg-white text-primary-cacha px-3 py-1 rounded-pill fw-bold mx-2">CACHANEW</span> saat
                    checkout</p>
                <div class="d-flex flex-wrap justify-content-center gap-3 mb-4">
                    <a href="#" class="btn btn-lg btn-light text-primary-cacha fw-bold rounded-pill px-4">
                        <i class="fab fa-whatsapp me-2"></i>Pesan via WhatsApp
                    </a>
                    <a href="#" class="btn btn-lg btn-outline-light fw-bold rounded-pill px-4">
                        <i class="fas fa-shopping-cart me-2"></i>Belanja Sekarang
                    </a>
                </div>
                <div class="mt-4">
                    <p class="mb-3">Atau belanja melalui marketplace favorit kamu:</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#" class="bg-white p-2 rounded-circle">
                            <img src="{{asset('images/products/telur-gabus.png')}}" alt="Tokopedia" width="40"
                                 height="40">
                        </a>
                        <a href="#" class="bg-white p-2 rounded-circle">
                            <img src="{{asset('images/products/telur-gabus.png')}}" alt="Shopee" width="40" height="40">
                        </a>
                        <a href="#" class="bg-white p-2 rounded-circle">
                            <img src="{{asset('images/products/telur-gabus.png')}}" alt="Bukalapak" width="40"
                                 height="40">
                        </a>
                        <a href="#" class="bg-white p-2 rounded-circle">
                            <img src="{{asset('images/products/telur-gabus.png')}}" alt="Lazada" width="40" height="40">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="py-5" id="faq">
    <div class="container py-4">
        <div class="section-title text-center">
            <h2>Pertanyaan Umum</h2>
            <p class="text-muted">Jawaban untuk pertanyaan yang sering ditanyakan</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <!-- FAQ Item 1 -->
                    <div class="accordion-item border-0 mb-3 shadow-sm rounded">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button bg-white fw-bold" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Berapa lama pengiriman sampai ke alamat saya?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Pengiriman kami membutuhkan waktu 1-3 hari kerja untuk wilayah Jawa dan 3-7 hari kerja
                                untuk luar Jawa. Kami menggunakan jasa pengiriman terpercaya seperti JNE, J&T, dan
                                SiCepat untuk memastikan produk Anda sampai dengan aman dan tepat waktu.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="accordion-item border-0 mb-3 shadow-sm rounded">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed bg-white fw-bold" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false"
                                    aria-controls="collapseTwo">
                                Berapa lama makanan ini bisa bertahan?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Produk kami dapat bertahan 3-6 bulan dalam kemasan tertutup. Setelah dibuka, sebaiknya
                                dikonsumsi dalam waktu 2 minggu dan disimpan dalam wadah kedap udara untuk menjaga
                                kerenyahannya. Tanggal kadaluwarsa tertera pada setiap kemasan.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="accordion-item border-0 mb-3 shadow-sm rounded">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed bg-white fw-bold" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false"
                                    aria-controls="collapseThree">
                                Apakah produk Cacha Snack halal?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Ya, semua produk Cacha Snack sudah bersertifikat halal MUI. Kami memastikan semua bahan
                                dan proses produksi memenuhi standar halal, sehingga aman dikonsumsi oleh semua
                                kalangan.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="accordion-item border-0 mb-3 shadow-sm rounded">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed bg-white fw-bold" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false"
                                    aria-controls="collapseFour">
                                Apakah ada paket untuk acara atau reseller?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Tentu saja! Kami menyediakan paket khusus untuk acara dan reseller dengan harga yang
                                lebih terjangkau. Silakan hubungi customer service kami untuk informasi lebih lanjut
                                mengenai paket dan persyaratannya.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 5 -->
                    <div class="accordion-item border-0 mb-3 shadow-sm rounded">
                        <h2 class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed bg-white fw-bold" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false"
                                    aria-controls="collapseFive">
                                Bagaimana cara menukarkan promo atau voucher?
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive"
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Untuk menukarkan kode promo atau voucher, cukup masukkan kode pada kolom "Kode Promo"
                                saat checkout. Sistem akan otomatis menghitung diskon yang Anda dapatkan. Satu transaksi
                                hanya dapat menggunakan satu kode promo.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact -->
<section class="py-5 bg-light" id="contact">
    <div class="container py-4">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">Hubungi Kami</h2>
                <p class="text-muted mb-4">Ada pertanyaan atau ingin menjalin kerjasama? Jangan ragu untuk menghubungi
                    kami!</p>

                <div class="d-flex align-items-center mb-4">
                    <div class="bg-white rounded-circle p-3 shadow-sm me-4">
                        <i class="fas fa-map-marker-alt text-primary-cacha"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Alamat</h5>
                        <p class="mb-0 text-muted">Jl. Pantai Barat No. 123, Pangandaran, Jawa Barat</p>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-4">
                    <div class="bg-white rounded-circle p-3 shadow-sm me-4">
                        <i class="fas fa-phone-alt text-primary-cacha"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Telepon</h5>
                        <p class="mb-0 text-muted">+62 812-3456-7890</p>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-4">
                    <div class="bg-white rounded-circle p-3 shadow-sm me-4">
                        <i class="fas fa-envelope text-primary-cacha"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Email</h5>
                        <p class="mb-0 text-muted">info@cachasnack.id</p>
                    </div>
                </div>

                <div class="mt-5">
                    <h5 class="fw-bold mb-3">Ikuti Kami</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="social-icon">
                            <i class="fab fa-instagram text-white"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-tiktok text-white"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-facebook-f text-white"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-twitter text-white"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-youtube text-white"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 rounded-4 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <h3 class="fw-bold mb-4">Kirim Pesan</h3>
                        <form>
                            <div class="mb-3">
                                <label for="name" class="form-label fw-medium">Nama Lengkap</label>
                                <input type="text" class="form-control form-control-lg rounded-pill border-0 bg-light"
                                       id="name" placeholder="Masukkan nama lengkap">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label fw-medium">Email</label>
                                <input type="email" class="form-control form-control-lg rounded-pill border-0 bg-light"
                                       id="email" placeholder="Masukkan email">
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label fw-medium">Subjek</label>
                                <input type="text" class="form-control form-control-lg rounded-pill border-0 bg-light"
                                       id="subject" placeholder="Masukkan subjek pesan">
                            </div>
                            <div class="mb-4">
                                <label for="message" class="form-label fw-medium">Pesan</label>
                                <textarea class="form-control border-0 bg-light rounded-4" id="message" rows="5"
                                          placeholder="Tulis pesan Anda di sini..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary-cacha btn-lg rounded-pill w-100">Kirim Pesan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer" id="footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 mb-5 mb-lg-0">
                <a href="#" class="d-flex align-items-center mb-4">
                    <div class="logo-container me-2">
                        <img src="{{asset('images/products/telur-gabus.png')}}" alt="Cacha Snack Logo" height="40">
                    </div>
                    <span class="text-white fs-4 fw-bold">Cacha Snack</span>
                </a>
                <p class="mb-4 pe-lg-5">Cacha Snack adalah produsen makanan ringan kekinian asal Pangandaran yang
                    menghadirkan cemilan dengan cita rasa autentik, bahan berkualitas, dan kemasan yang menarik.</p>
                <h6 class="text-white mb-3">Download Aplikasi Kami</h6>
                <div class="d-flex gap-2">
                    <a href="#" class="me-2">
                        <img src="{{asset('images/products/telur-gabus.png')}}" alt="Play Store" height="40">
                    </a>
                    <a href="#">
                        <img src="{{asset('images/products/telur-gabus.png')}}" alt="App Store" height="40">
                    </a>
                </div>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <h5 class="footer-title">Produk</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="#">Makaroni</a></li>
                    <li><a href="#">Keripik</a></li>
                    <li><a href="#">Basreng</a></li>
                    <li><a href="#">Telur Gabus</a></li>
                    <li><a href="#">Kacang</a></li>
                    <li><a href="#">Paket Bundling</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <h5 class="footer-title">Perusahaan</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="#">Tentang Kami</a></li>
                    <li><a href="#">Karir</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Press Kit</a></li>
                    <li><a href="#">Partner</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <h5 class="footer-title">Support</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="#">Pusat Bantuan</a></li>
                    <li><a href="#">Cara Pemesanan</a></li>
                    <li><a href="#">Pengiriman</a></li>
                    <li><a href="#">Pembayaran</a></li>
                    <li><a href="#">Pengembalian</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <h5 class="footer-title">Legal</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="#">Syarat & Ketentuan</a></li>
                    <li><a href="#">Kebijakan Privasi</a></li>
                    <li><a href="#">Kebijakan Cookies</a></li>
                    <li><a href="#">Lisensi</a></li>
                </ul>
            </div>
        </div>
        <hr class="my-5 bg-secondary opacity-25">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="mb-0 text-white-50">&copy; 2025 Cacha Snack. All rights reserved.</p>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-center justify-content-md-end gap-3">
                    <img src="{{asset('images/products/telur-gabus.png')}}" alt="Visa" height="25">
                    <img src="{{asset('images/products/telur-gabus.png')}}" alt="Mastercard" height="25">
                    <img src="{{asset('images/products/telur-gabus.png')}}" alt="BCA" height="25">
                    <img src="{{asset('images/products/telur-gabus.png')}}" alt="Mandiri" height="25">
                    <img src="{{asset('images/products/telur-gabus.png')}}" alt="OVO" height="25">
                    <img src="{{asset('images/products/telur-gabus.png')}}" alt="GoPay" height="25">
                    <img src="{{asset('images/products/telur-gabus.png')}}" alt="DANA" height="25">
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Scroll to Top -->
<div class="scroll-to-top" id="scrollToTop">
    <i class="fas fa-arrow-up"></i>
</div>

<!-- Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<!-- Main JS -->

{{--@include('guest.partials.scripts')--}}
<script>
    // Navbar scroll
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            document.querySelector('.navbar').classList.add('scrolled');
        } else {
            document.querySelector('.navbar').classList.remove('scrolled');
        }

        if (window.scrollY > 300) {
            document.getElementById('scrollToTop').classList.add('active');
        } else {
            document.getElementById('scrollToTop').classList.remove('active');
        }
    });

    // Scroll to top
    document.getElementById('scrollToTop').addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();

            const targetId = this.getAttribute('href');
            if(targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if(targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });
</script>

</body>
</html>
