@extends('guest.layouts.app')

@section('content')
    <div class="x-shop-section">
        <div class="container py-5 mt-5">
            <!-- Breadcrumb with brand colors -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('guest.home')}}" class="text-decoration-none" style="color: var(--x-primary-red);">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{route('guest.shop')}}" class="text-decoration-none" style="color: var(--x-primary-red);">Shop</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
                </ol>
            </nav>

            <div class="row g-4">
                <!-- Product Images with enhanced hover effects -->
                <div class="col-lg-6">
                    <div class="x-shop-product-image card border-0 overflow-hidden" style="border-radius: var(--x-card-radius); box-shadow: var(--x-card-shadow);">
                        <div class="image-container position-relative overflow-hidden">
                            <img src="{{ asset('storage/' . $mainImage->image_path) }}" class="img-fluid" alt="{{ $product->name }}" id="mainImage">
                            <!-- Subtle vignette overlay -->
                            <div class="x-shop-image-overlay"></div>
                        </div>
                    </div>
                    <div class="row g-2 mt-2">
                        @foreach($otherImages as $image)
                            <div class="col-3">
                                <div class="x-shop-product-thumbnail card border-0 overflow-hidden cursor-hover" style="border-radius: var(--x-card-radius); box-shadow: var(--x-card-shadow);">
                                    <div class="image-container position-relative overflow-hidden" data-src="{{ asset('storage/' . $image->image_path) }}">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" class="img-fluid" alt="Thumbnail">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Product Details with brand styling -->
                <div class="col-lg-6">
                    <div class="ps-lg-4">
                        <!-- Enhanced badge with pulse effect -->
                        @if($product->featured)
                            <div class="x-shop-badge-container mb-2">
                                <span class="x-shop-badge" style="background: var(--x-primary-gradient);">
                                    <i class="fas fa-fire me-1"></i>Terlaris
                                </span>
                            </div>
                        @endif

                        <!-- Product title with brand typography -->
                        <h1 class="x-shop-product-title fw-bold mb-3" style="font-weight: var(--x-title-weight); letter-spacing: var(--x-letter-spacing);">{{ $product->name }}</h1>

                        <!-- Rating with branded colors -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="x-shop-rating me-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <span class="text-muted">({{ $totalReviews }} ulasan)</span>
                        </div>

                        <!-- Enhanced price display with gradient effect for discounted items -->
                        <div class="mb-4">
                            <h2 class="x-shop-product-price fs-2 fw-bold mb-2" style="color: var(--x-primary-red);">
                                Rp{{ number_format($discountPrice ?? $defaultUnit->selling_price, 0, ',', '.') }}
                                @if($discountPrice)
                                    <span class="x-shop-product-price-old fs-5" style="text-decoration: line-through; color: var(--x-text-secondary);">
                                        Rp{{ number_format($defaultUnit->selling_price, 0, ',', '.') }}
                                    </span>
                                    <span class="x-shop-discount-badge ms-2">
                                        -{{ $discountPercentage }}%
                                    </span>
                                @endif
                            </h2>
                        </div>

                        <!-- Product description -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3" style="color: var(--x-text-primary);">Deskripsi</h5>
                            <p style="color: var(--x-text-secondary);">{{ $product->description }}</p>
                        </div>

                        <!-- Enhanced package size selection -->
                        @if($productUnits->count() > 1)
                            <div class="mb-4">
                                <h5 class="fw-bold mb-3" style="color: var(--x-text-primary);">Ukuran Kemasan</h5>
                                <div class="x-shop-package-options d-flex gap-2">
                                    @foreach($productUnits as $unit)
                                        <button class="x-shop-package-btn {{ $unit->is_default ? 'active' : '' }}"
                                                data-unit-id="{{ $unit->id }}"
                                                style="border-radius: var(--x-button-radius);">
                                            {{ $unit->unit->name }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Quantity selector and Add to Cart button with animations -->
                        <div class="d-flex gap-3 mb-4">
                            <div class="x-shop-quantity-selector input-group" style="width: 140px;">
                                <button class="btn btn-outline-secondary x-shop-qty-btn" type="button" data-action="decrease">-</button>
                                <input type="text" class="form-control text-center x-shop-qty-input" value="1">
                                <button class="btn btn-outline-secondary x-shop-qty-btn" type="button" data-action="increase">+</button>
                            </div>
                            <button class="x-shop-add-cart-btn flex-grow-1" style="border-radius: var(--x-button-radius);">
                                <i class="fas fa-cart-plus me-2"></i>Tambah ke Keranjang
                                <span class="btn-highlight"></span>
                            </button>
                        </div>

                        <!-- Social proof elements -->
                        <div class="x-shop-social-proof mb-4 d-flex gap-3">
                            <div class="x-shop-counter bg-white px-3 py-2 rounded-pill" style="box-shadow: var(--x-card-shadow);">
                                <i class="fas fa-eye me-2" style="color: var(--x-primary-red);"></i>
                                <span class="counter" style="color: var(--x-primary-red);">120+</span> orang lagi lihat
                            </div>
                            <div class="x-shop-timer bg-white px-3 py-2 rounded-pill" style="box-shadow: var(--x-card-shadow);">
                                <i class="fas fa-clock me-2" style="color: var(--x-primary-red);"></i>
                                Stok terbatas: <span class="timer" style="color: var(--x-primary-red);">24</span> tersisa
                            </div>
                        </div>

                        <!-- Social sharing buttons -->
                        <div class="x-shop-share-container mt-4">
                            <p class="small mb-2" style="color: var(--x-text-secondary);">Bagikan produk ini:</p>
                            <div class="d-flex gap-2">
                                <a href="#" class="x-shop-social-button" data-platform="whatsapp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <a href="#" class="x-shop-social-button" data-platform="instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="x-shop-social-button" data-platform="tiktok">
                                    <i class="fab fa-tiktok"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Section with brand styling -->
            <div class="row mt-5">
                <div class="col-12">
                    <ul class="nav nav-tabs nav-fill x-shop-tabs" id="productTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#description">
                                <i class="fas fa-file-alt me-2"></i>Deskripsi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#information">
                                <i class="fas fa-info-circle me-2"></i>Informasi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#reviews">
                                <i class="fas fa-star me-2"></i>Ulasan ({{ $totalReviews }})
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content p-4 bg-white shadow-sm rounded-bottom">
                        <!-- Description Tab -->
                        <div class="tab-pane fade show active" id="description">
                            <div class="row">
                                <div class="col-md-8">
                                    <h4 class="mb-4 fw-bold" style="color: var(--x-text-primary);">Tentang Produk</h4>
                                    <div class="product-description">
                                        {!! $product->description !!}
                                    </div>

                                    @if($product->short_description)
                                        <div class="highlights mt-4">
                                            <h5 class="mb-3 fw-bold" style="color: var(--x-text-primary);">Highlight Produk</h5>
                                            <div class="card border-0" style="background-color: var(--x-bg-secondary); border-radius: var(--x-card-radius);">
                                                <div class="card-body">
                                                    {{ $product->short_description }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-4">
                                    <div class="card border-0" style="background-color: var(--x-bg-secondary); border-radius: var(--x-card-radius);">
                                        <div class="card-body">
                                            <h5 class="card-title mb-3 fw-bold" style="color: var(--x-text-primary);">Spesifikasi</h5>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2 d-flex justify-content-between">
                                                    <span style="color: var(--x-text-secondary);">Kategori:</span>
                                                    <span class="fw-medium">{{ $product->category->name }}</span>
                                                </li>
                                                <li class="mb-2 d-flex justify-content-between">
                                                    <span style="color: var(--x-text-secondary);">Kode Produk:</span>
                                                    <span class="fw-medium">{{ $product->code }}</span>
                                                </li>
                                                @if($product->barcode)
                                                    <li class="mb-2 d-flex justify-content-between">
                                                        <span style="color: var(--x-text-secondary);">Barcode:</span>
                                                        <span class="fw-medium">{{ $product->barcode }}</span>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Information Tab -->
                        <div class="tab-pane fade" id="information">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm" style="border-radius: var(--x-card-radius);">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4 fw-bold" style="color: var(--x-text-primary);">
                                                <i class="fas fa-box me-2" style="color: var(--x-primary-red);"></i>Informasi Kemasan
                                            </h5>
                                            <div class="table-responsive">
                                                <table class="table table-borderless">
                                                    <thead style="background-color: var(--x-bg-secondary);">
                                                    <tr>
                                                        <th>Ukuran</th>
                                                        <th>Harga</th>
                                                        <th>Stok</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($productUnits as $unit)
                                                        <tr>
                                                            <td>{{ $unit->unit->name }}</td>
                                                            <td style="color: var(--x-primary-red);">Rp{{ number_format($unit->selling_price, 0, ',', '.') }}</td>
                                                            <td>{{ $unit->stock }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm" style="border-radius: var(--x-card-radius);">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4 fw-bold" style="color: var(--x-text-primary);">
                                                <i class="fas fa-shipping-fast me-2" style="color: var(--x-primary-red);"></i>Informasi Pengiriman
                                            </h5>
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item border-0 ps-0">
                                                    <i class="fas fa-check-circle me-2" style="color: var(--x-primary-red);"></i>
                                                    Pengiriman ke seluruh Indonesia
                                                </li>
                                                <li class="list-group-item border-0 ps-0">
                                                    <i class="fas fa-check-circle me-2" style="color: var(--x-primary-red);"></i>
                                                    Dikemas dengan aman
                                                </li>
                                                <li class="list-group-item border-0 ps-0">
                                                    <i class="fas fa-check-circle me-2" style="color: var(--x-primary-red);"></i>
                                                    Estimasi 2-3 hari pengiriman
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reviews Tab with enhanced styling -->
                        <div class="tab-pane fade" id="reviews">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="overall-rating text-center p-4">
                                        <h4 class="mb-4 fw-bold" style="color: var(--x-text-primary);">Rating Keseluruhan</h4>
                                        <div class="display-4 fw-bold mb-3" style="color: var(--x-primary-red);">4.5</div>
                                        <div class="rating-stars mb-3">
                                            <i class="fas fa-star" style="color: var(--x-accent-yellow);"></i>
                                            <i class="fas fa-star" style="color: var(--x-accent-yellow);"></i>
                                            <i class="fas fa-star" style="color: var(--x-accent-yellow);"></i>
                                            <i class="fas fa-star" style="color: var(--x-accent-yellow);"></i>
                                            <i class="fas fa-star-half-alt" style="color: var(--x-accent-yellow);"></i>
                                        </div>
                                        <p style="color: var(--x-text-secondary);">Berdasarkan {{ $totalReviews }} ulasan</p>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="x-shop-reviews-list">
                                        <!-- Review Item with enhanced styling -->
                                        <div class="x-shop-review-item mb-4 pb-4 border-bottom">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <h6 class="mb-1 fw-bold" style="color: var(--x-text-primary);">John Doe</h6>
                                                    <div class="rating-stars small">
                                                        <i class="fas fa-star" style="color: var(--x-accent-yellow);"></i>
                                                        <i class="fas fa-star" style="color: var(--x-accent-yellow);"></i>
                                                        <i class="fas fa-star" style="color: var(--x-accent-yellow);"></i>
                                                        <i class="fas fa-star" style="color: var(--x-accent-yellow);"></i>
                                                        <i class="fas fa-star" style="color: var(--x-accent-yellow);"></i>
                                                    </div>
                                                </div>
                                                <small style="color: var(--x-text-secondary);">2 hari yang lalu</small>
                                            </div>
                                            <p class="mb-0" style="color: var(--x-text-secondary);">Produk sangat berkualitas dan pengiriman cepat. Recommended seller!</p>
                                        </div>

                                        <!-- Add more review items here -->
                                    </div>

                                    <div class="text-center mt-4">
                                        <button class="x-shop-view-all-btn" style="border-radius: var(--x-button-radius);">
                                            Lihat Semua Ulasan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Shop section base styles with "Snack Attack" theme */
        .x-shop-section {
            color: var(--x-text-primary);
        }

        /* Product image effects */
        .x-shop-product-image {
            transition: all 0.3s var(--x-transition-timing);
        }

        .x-shop-product-image:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: var(--x-card-shadow-hover);
        }

        .image-container {
            position: relative;
            transition: all 0.4s var(--x-transition-timing);
        }

        .x-shop-image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, transparent 70%, rgba(0,0,0,0.03) 100%);
            pointer-events: none;
        }

        /* Thumbnail styling */
        .x-shop-product-thumbnail {
            cursor: pointer;
            transition: all 0.3s var(--x-transition-timing);
            overflow: hidden;
        }

        .x-shop-product-thumbnail:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: var(--x-card-shadow-hover);
        }

        .x-shop-product-thumbnail:hover img {
            transform: scale(1.15);
        }

        .x-shop-product-thumbnail img {
            transition: transform 0.5s var(--x-transition-timing);
        }

        /* Badge styling */
        .x-shop-badge {
            display: inline-block;
            color: white;
            padding: 6px 12px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 0.8rem;
            position: relative;
            overflow: hidden;
        }

        .x-shop-badge::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255,255,255,0.1);
            transform: rotate(30deg);
            animation: badgePulse 3s infinite;
        }

        @keyframes badgePulse {
            0% { transform: rotate(30deg) translateX(-100%); }
            50% { transform: rotate(30deg) translateX(0%); }
            100% { transform: rotate(30deg) translateX(100%); }
        }

        /* Discount badge */
        .x-shop-discount-badge {
            background-color: var(--x-primary-red);
            color: white;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            display: inline-block;
        }

        /* Rating stars */
        .x-shop-rating i {
            color: var(--x-accent-yellow);
            margin-right: 2px;
        }

        /* Package selection buttons */
        .x-shop-package-btn {
            padding: 8px 16px;
            background-color: white;
            border: 2px solid #eaeaea;
            transition: all 0.3s var(--x-transition-timing);
            font-weight: 600;
            cursor: pointer;
        }

        .x-shop-package-btn.active {
            background: var(--x-primary-red);
            color: white;
            border-color: var(--x-primary-red);
        }

        .x-shop-package-btn:hover:not(.active) {
            border-color: var(--x-primary-red);
            color: var(--x-primary-red);
            transform: translateY(-3px);
        }

        /* Quantity selector */
        .x-shop-quantity-selector {
            border-radius: 30px;
            overflow: hidden;
        }

        .x-shop-qty-btn {
            border: 1px solid #eaeaea;
            background: white;
            transition: all 0.2s ease;
        }

        .x-shop-qty-btn:hover {
            background: var(--x-primary-red);
            color: white;
            border-color: var(--x-primary-red);
        }

        .x-shop-qty-input {
            border: 1px solid #eaeaea;
            font-weight: 600;
        }

        /* Add to cart button */
        .x-shop-add-cart-btn {
            background: var(--x-primary-red);
            color: white;
            border: none;
            font-weight: 700;
            padding: 10px 24px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s var(--x-transition-timing);
            box-shadow: var(--x-button-shadow);
        }

        .x-shop-add-cart-btn:hover {
            transform: translateY(-5px);
            box-shadow: var(--x-button-shadow-hover);
        }

        .x-shop-add-cart-btn:active {
            transform: scale(0.98);
        }

        .x-shop-add-cart-btn .btn-highlight {
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255,255,255,0.1);
            transform: rotate(30deg);
            transition: all 0.5s var(--x-transition-timing);
            z-index: 1;
        }

        .x-shop-add-cart-btn:hover .btn-highlight {
            transform: rotate(30deg) translateX(90%);
        }

        /* Social proof elements */
        .x-shop-counter, .x-shop-timer {
            font-size: 0.9rem;
            transition: all 0.3s var(--x-transition-timing);
        }

        .x-shop-counter:hover, .x-shop-timer:hover {
            transform: translateY(-3px);
        }

        /* Social share buttons */
        .x-shop-social-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--x-bg-secondary);
            color: var(--x-primary-red);
            transition: all 0.3s var(--x-transition-timing);
        }

        .x-shop-social-button:hover {
            background: var(--x-primary-red);
            color: white;
            transform: translateY(-3px) rotate(5deg);
        }

        /* Tabs styling */
        .x-shop-tabs .nav-link {
            color: var(--x-text-primary);
            border: none;
            position: relative;
            transition: all 0.3s var(--x-transition-timing);
        }

        .x-shop-tabs .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 3px;
            background: var(--x-primary-red);
            transition: all 0.3s var(--x-transition-timing);
            transform: translateX(-50%);
        }

        .x-shop-tabs .nav-link:hover::after {
            width: 30%;
        }

        .x-shop-tabs .nav-link.active {
            color: var(--x-primary-red);
            background: transparent;
            border-bottom: none;
        }

        .x-shop-tabs .nav-link.active::after {
            width: 80%;
        }

        /* Reviews styling */
        .x-shop-review-item {
            transition: all 0.3s var(--x-transition-timing);
            padding: 15px;
            border-radius: 10px;
        }

        .x-shop-review-item:hover {
            background-color: var(--x-bg-secondary);
            transform: translateX(5px);
        }

        /* View all reviews button */
        .x-shop-view-all-btn {
            background: transparent;
            color: var(--x-primary-red);
            border: 2px solid var(--x-primary-red);
            padding: 8px 24px;
            font-weight: 600;
            transition: all 0.3s var(--x-transition-timing);
        }

        .x-shop-view-all-btn:hover {
            background: var(--x-primary-red);
            color: white;
            transform: translateY(-3px);
            box-shadow: var(--x-button-shadow);
        }

        /* Cursor effect for product images */
        .cursor-hover {
            cursor: pointer;
        }

        /* Confetti animation for add to cart */
        .x-shop-confetti {
            position: absolute;
            top: 50%;
            left: var(--x);
            width: 8px;
            height: 8px;
            background: var(--color);
            border-radius: 50%;
            pointer-events: none;
            animation: confettiFall 1s forwards;
        }

        @keyframes confettiFall {
            0% {
                transform: translateY(0) rotate(0);
                opacity: 1;
            }
            100% {
                transform: translateY(var(--y)) rotate(var(--r));
                opacity: 0;
            }
        }

        /* Click effects */
        .clicked {
            animation: clickEffect 0.3s var(--x-transition-timing);
        }

        @keyframes clickEffect {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(0.96); }
        }

        .tab-clicked {
            animation: tabPulse 0.3s var(--x-transition-timing);
        }

        @keyframes tabPulse {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }

        /* Staggered animation for reviews */
        .x-shop-review-item {
            opacity: 0;
            transform: translateY(20px);
        }

        .x-shop-review-item.visible {
            opacity: 1;
            transform: translateY(0);
            transition: all 0.5s var(--x-transition-timing);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .x-shop-social-proof {
                flex-direction: column;
            }

            .x-shop-tabs .nav-link {
                font-size: 0.9rem;
                padding: 10px 5px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Thumbnail click handler with animation
            const thumbnails = document.querySelectorAll('.x-shop-product-thumbnail');
            const mainImage = document.getElementById('mainImage');

            thumbnails.forEach(thumb => {
                thumb.addEventListener('click', function() {
                    // Add fade out effect
                    mainImage.style.opacity = 0;

                    // Get the image container inside the thumbnail
                    const imgContainer = this.querySelector('.image-container');
                    const newSrc = imgContainer.dataset.src || imgContainer.querySelector('img').src;

                    // Change image after a short delay
                    setTimeout(() => {
                        mainImage.src = newSrc;
                        mainImage.style.opacity = 1;
                    }, 200);

                    // Highlight active thumbnail
                    thumbnails.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Package size selection
            const packageBtns = document.querySelectorAll('.x-shop-package-btn');
            packageBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    packageBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    // Here you would add code to update price based on selected package size
                });
            });

            // Quantity selector
            const qtyInput = document.querySelector('.x-shop-qty-input');
            const qtyBtns = document.querySelectorAll('.x-shop-qty-btn');

            qtyBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const action = this.dataset.action;
                    let currentQty = parseInt(qtyInput.value);

                    if (action === 'increase') {
                        qtyInput.value = currentQty + 1;
                    } else if (action === 'decrease' && currentQty > 1) {
                        qtyInput.value = currentQty - 1;
                    }

                    // Add animation effect
                    this.classList.add('clicked');
                    setTimeout(() => {
                        this.classList.remove('clicked');
                    }, 300);
                });
            });

            // Add to cart button with animation
            const addToCartBtn = document.querySelector('.x-shop-add-cart-btn');
            if (addToCartBtn) {
                addToCartBtn.addEventListener('click', function(e) {
                    // Prevent default only if you don't want to navigate away
                    // e.preventDefault();

                    // Add success animation
                    this.classList.add('clicked');

                    // Create confetti effect
                    for (let i = 0; i < 20; i++) {
                        createConfetti(this);
                    }

                    setTimeout(() => {
                        this.classList.remove('clicked');
                    }, 500);
                });
            }

            // Confetti effect for add to cart
            function createConfetti(button) {
                const confetti = document.createElement('div');
                confetti.className = 'x-shop-confetti';
                confetti.style.setProperty('--x', Math.random() * 100 + '%');
                confetti.style.setProperty('--y', Math.random() * -50 - 50 + 'px');
                confetti.style.setProperty('--r', Math.random() * 360 + 'deg');
                confetti.style.setProperty('--color', `hsl(${Math.random() * 60 + 340}, 100%, 50%)`);

                button.appendChild(confetti);

                setTimeout(() => {
                    confetti.remove();
                }, 1000);
            }

            // Social proof counter animation
            const counterElement = document.querySelector('.counter');
            let currentCount = 120;

            if (counterElement) {
                setInterval(() => {
                    // Randomly increase counter slightly to simulate live visitors
                    if (Math.random() > 0.7) {
                        currentCount += Math.floor(Math.random() * 3) + 1;
                        counterElement.textContent = `${currentCount}+`;

                        // Add a quick pulse animation
                        counterElement.style.transform = 'scale(1.1)';
                        setTimeout(() => {
                            counterElement.style.transform = 'scale(1)';
                        }, 300);
                    }
                }, 5000);
            }

            // Timer countdown animation
            const timerElement = document.querySelector('.timer');
            if (timerElement) {
                let stockCount = 24;

                // Occasionally decrease stock for urgency
                setInterval(() => {
                    if (Math.random() > 0.9 && stockCount > 1) {
                        stockCount--;
                        timerElement.textContent = stockCount;

                        // Add pulse animation with color change for urgency
                        timerElement.style.transform = 'scale(1.2)';
                        if (stockCount < 10) {
                            timerElement.style.color = '#C80000'; // Dark red for urgency
                        }

                        setTimeout(() => {
                            timerElement.style.transform = 'scale(1)';
                        }, 300);
                    }
                }, 10000);
            }

            // Tab animation
            const tabButtons = document.querySelectorAll('.nav-link');
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Add click effect
                    this.classList.add('tab-clicked');
                    setTimeout(() => {
                        this.classList.remove('tab-clicked');
                    }, 300);
                });
            });

            // Scroll reveal animation for reviews
            const reviewItems = document.querySelectorAll('.x-shop-review-item');

            function checkReveal() {
                reviewItems.forEach((item, index) => {
                    const rect = item.getBoundingClientRect();
                    if (rect.top <= window.innerHeight * 0.8) {
                        setTimeout(() => {
                            item.classList.add('visible');
                        }, index * 100); // Staggered animation
                    }
                });
            }

            // Check on scroll and initial load
            window.addEventListener('scroll', checkReveal);
            checkReveal();

            // Magnetic effect for social buttons
            const socialButtons = document.querySelectorAll('.x-shop-social-button');

            socialButtons.forEach(button => {
                button.addEventListener('mousemove', (e) => {
                    const rect = button.getBoundingClientRect();
                    const x = e.clientX - rect.left - rect.width / 2;
                    const y = e.clientY - rect.top - rect.height / 2;

                    button.style.transform = `translate(${x * 0.2}px, ${y * 0.2}px) scale(1.1)`;
                });

                button.addEventListener('mouseleave', () => {
                    button.style.transform = '';
                });
            });
        });
    </script>
