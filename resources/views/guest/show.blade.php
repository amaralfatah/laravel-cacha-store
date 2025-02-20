@extends('guest.layouts.app')

@section('content')
    <div class="container py-5 mt-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('guest.home')}}" class="text-decoration-none text-primary-cacha">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{route('guest.shop')}}" class="text-decoration-none text-primary-cacha">Shop</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="row g-4">
            <!-- Product Images -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <img src="{{ asset('storage/' . $mainImage->image_path) }}" class="img-fluid" alt="{{ $product->name }}" id="mainImage">
                </div>
                <div class="row g-2 mt-2">
                    @foreach($otherImages as $image)
                        <div class="col-3">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden product-thumbnail">
                                <img src="{{ asset('storage/' . $image->image_path) }}" class="img-fluid" alt="Thumbnail">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Product Details -->
            <div class="col-lg-6">
                <div class="ps-lg-4">
                    @if($product->featured)
                        <span class="badge bg-danger mb-2">Terlaris</span>
                    @endif
                    <h1 class="fw-bold mb-3">{{ $product->name }}</h1>

                    <div class="d-flex align-items-center mb-3">
                        <div class="rating-stars me-2">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <span class="text-muted">({{ $totalReviews }} ulasan)</span>
                    </div>

                    <div class="mb-4">
                        <h2 class="product-price fs-2 fw-bold text-primary-cacha mb-2">
                            Rp{{ number_format($discountPrice ?? $defaultUnit->selling_price, 0, ',', '.') }}
                            @if($discountPrice)
                                <span class="product-price-old fs-5">Rp{{ number_format($defaultUnit->selling_price, 0, ',', '.') }}</span>
                                <span class="badge bg-danger ms-2">-{{ $discountPercentage }}%</span>
                            @endif
                        </h2>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">Deskripsi</h5>
                        <p class="text-muted">{{ $product->description }}</p>
                    </div>

                    @if($productUnits->count() > 1)
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3">Ukuran Kemasan</h5>
                            <div class="d-flex gap-2">
                                @foreach($productUnits as $unit)
                                    <button class="btn {{ $unit->is_default ? 'btn-primary-cacha' : 'btn-outline-primary-cacha' }} px-4">
                                        {{ $unit->unit->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="d-flex gap-3 mb-4">
                        <div class="input-group" style="width: 140px;">
                            <button class="btn btn-outline-secondary" type="button">-</button>
                            <input type="text" class="form-control text-center" value="1">
                            <button class="btn btn-outline-secondary" type="button">+</button>
                        </div>
                        <button class="btn btn-primary-cacha flex-grow-1">
                            <i class="fas fa-cart-plus me-2"></i>Tambah ke Keranjang
                        </button>
                    </div>

                    <!-- Tombol Share dan Lainnya tetap sama -->
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="row mt-5">
            <div class="col-12">
                <ul class="nav nav-tabs nav-fill" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold text-dark" data-bs-toggle="tab" data-bs-target="#description">
                            <i class="fas fa-file-alt me-2"></i>Deskripsi
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold text-dark" data-bs-toggle="tab" data-bs-target="#information">
                            <i class="fas fa-info-circle me-2"></i>Informasi
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold text-dark" data-bs-toggle="tab" data-bs-target="#reviews">
                            <i class="fas fa-star me-2"></i>Ulasan ({{ $totalReviews }})
                        </button>
                    </li>
                </ul>

                <div class="tab-content p-4 bg-white shadow-sm rounded-bottom">
                    <!-- Description Tab -->
                    <div class="tab-pane fade show active" id="description">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="mb-4">Tentang Produk</h4>
                                <div class="product-description">
                                    {!! $product->description !!}
                                </div>

                                @if($product->short_description)
                                    <div class="highlights mt-4">
                                        <h5 class="mb-3">Highlight Produk</h5>
                                        <div class="card border-0 bg-light">
                                            <div class="card-body">
                                                {{ $product->short_description }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Spesifikasi</h5>
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2">
                                                <span class="text-muted">Kategori:</span>
                                                <span class="float-end">{{ $product->category->name }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="text-muted">Kode Produk:</span>
                                                <span class="float-end">{{ $product->code }}</span>
                                            </li>
                                            @if($product->barcode)
                                                <li class="mb-2">
                                                    <span class="text-muted">Barcode:</span>
                                                    <span class="float-end">{{ $product->barcode }}</span>
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
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">
                                            <i class="fas fa-box me-2"></i>Informasi Kemasan
                                        </h5>
                                        <div class="table-responsive">
                                            <table class="table table-borderless">
                                                <thead class="table-light">
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
                                                        <td>Rp{{ number_format($unit->selling_price, 0, ',', '.') }}</td>
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
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">
                                            <i class="fas fa-shipping-fast me-2"></i>Informasi Pengiriman
                                        </h5>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item border-0 ps-0">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                Pengiriman ke seluruh Indonesia
                                            </li>
                                            <li class="list-group-item border-0 ps-0">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                Dikemas dengan aman
                                            </li>
                                            <li class="list-group-item border-0 ps-0">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                Estimasi 2-3 hari pengiriman
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reviews Tab -->
                    <div class="tab-pane fade" id="reviews">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="overall-rating text-center p-4">
                                    <h4 class="mb-4">Rating Keseluruhan</h4>
                                    <div class="display-4 fw-bold text-primary-cacha mb-3">4.5</div>
                                    <div class="rating-stars mb-3">
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                    </div>
                                    <p class="text-muted">Berdasarkan {{ $totalReviews }} ulasan</p>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="reviews-list">
                                    <!-- Sample Review Item -->
                                    <div class="review-item mb-4 pb-4 border-bottom">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h6 class="mb-1">John Doe</h6>
                                                <div class="rating-stars small">
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                </div>
                                            </div>
                                            <small class="text-muted">2 hari yang lalu</small>
                                        </div>
                                        <p class="mb-0">Produk sangat berkualitas dan pengiriman cepat. Recommended seller!</p>
                                    </div>

                                    <!-- Add more review items here -->
                                </div>

                                <div class="text-center mt-4">
                                    <button class="btn btn-outline-primary-cacha">
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
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const thumbnails = document.querySelectorAll('.product-thumbnail img');
            const mainImage = document.getElementById('mainImage');

            thumbnails.forEach(thumb => {
                thumb.addEventListener('click', function() {
                    mainImage.src = this.src;
                });
            });
        });
    </script>
@endpush
