@extends('guest.layouts.app')

@section('content')
    <div class="container py-5 mt-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('guest.home')}}" class="text-decoration-none text-primary-cacha">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Produk</li>
            </ol>
        </nav>

        <div class="row g-4">
            <!-- Sidebar Filters -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">Kategori</h5>
                        <!-- In the categories filter section -->
                        <div class="categories-list">
                            <!-- Add "All" option at the top -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="category"
                                       id="categoryAll" value="all"
                                    {{ (!request('category') || request('category') == 'all') ? 'checked' : '' }}>
                                <label class="form-check-label d-flex justify-content-between"
                                       for="categoryAll">
                                    Semua Kategori
                                    <span class="badge bg-primary-light text-primary-cacha rounded-pill">
                {{ $totalProducts }}
            </span>
                                </label>
                            </div>

                            <!-- Existing categories -->
                            @foreach($categories as $category)
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="category"
                                           id="category{{ $category->id }}" value="{{ $category->id }}"
                                        {{ request('category') == $category->id ? 'checked' : '' }}>
                                    <label class="form-check-label d-flex justify-content-between"
                                           for="category{{ $category->id }}">
                                        {{ $category->name }}
                                        <span class="badge bg-primary-light text-primary-cacha rounded-pill">
                    {{ $category->products_count }}
                </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">Filter Harga</h5>
                        <div class="price-range">
                            <div class="mb-3">
                                <label class="form-label">Harga Minimum</label>
                                <input type="number" class="form-control" id="priceMin"
                                       value="{{ request('price_min') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Harga Maximum</label>
                                <input type="number" class="form-control" id="priceMax"
                                       value="{{ request('price_max') }}">
                            </div>
                            <button class="btn btn-primary-cacha w-100" id="applyFilter">
                                Terapkan Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="col-lg-9">
                <!-- Sort Options -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-0">Semua Produk</h4>
                        <p class="text-muted mb-0">Menampilkan {{ $products->firstItem() }}
                            - {{ $products->lastItem() }}
                            dari {{ $products->total() }} produk</p>
                    </div>
                    <div class="d-flex align-items-center">
                        <label class="me-2">Urutkan:</label>
                        <select class="form-select" id="sortProducts">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga
                                Terendah
                            </option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga
                                Tertinggi
                            </option>
                            <option value="bestseller" {{ request('sort') == 'bestseller' ? 'selected' : '' }}>
                                Terlaris
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="row g-4">
                    @foreach($products as $product)
                        <div class="col-md-4">
                            <x-product-card
                                :product="$product"
                                :show-badges="false"
                                :show-discount="true"
                            />
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-5">
                    {{ $products->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const sortSelect = document.getElementById('sortProducts');
                const categoryInputs = document.querySelectorAll('input[name="category"]');
                const applyFilterBtn = document.getElementById('applyFilter');

                function updateQueryString() {
                    const params = new URLSearchParams(window.location.search);

                    // Update sort parameter
                    params.set('sort', sortSelect.value);

                    // Update category parameter
                    const selectedCategory = document.querySelector('input[name="category"]:checked');
                    if (selectedCategory) {
                        params.set('category', selectedCategory.value);
                    } else {
                        params.delete('category');
                    }

                    // Update price range parameters
                    const priceMin = document.getElementById('priceMin').value;
                    const priceMax = document.getElementById('priceMax').value;
                    if (priceMin) params.set('price_min', priceMin);
                    if (priceMax) params.set('price_max', priceMax);

                    window.location.search = params.toString();
                }

                sortSelect.addEventListener('change', updateQueryString);
                categoryInputs.forEach(input => {
                    input.addEventListener('change', updateQueryString);
                });
                applyFilterBtn.addEventListener('click', updateQueryString);
            });
        </script>
    @endpush
@endsection
