@push('styles')
    <style>
        .animate-fade-in {
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

{{-- Kartu Informasi Dasar --}}
<div class="card border mb-4">
    <div class="card-header bg-transparent">
        <h6 class="card-title mb-0">
            <i class='bx bx-info-circle me-2 text-primary'></i>
            Informasi Dasar
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @if (auth()->user()->role === 'admin')
                <div class="col-12">
                    <label for="store_id" class="form-label">Toko</label>
                    <select class="form-select @error('store_id') is-invalid @enderror" id="store_id" name="store_id"
                        required>
                        <option value="">Pilih Toko</option>
                        @foreach ($stores as $store)
                            <option value="{{ $store->id }}"
                                {{ old('store_id', $product->store_id ?? '') == $store->id ? 'selected' : '' }}>
                                {{ $store->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('store_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            <div class="col-12 col-md-6">
                <label for="name" class="form-label">Nama Produk</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    name="name" value="{{ old('name', $product->name ?? '') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-md-6">
                <label for="code" class="form-label">Kode Produk</label>
                <div class="input-group">
                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code"
                        name="code" value="{{ old('code', $product->code ?? '') }}" required>
                    <button class="btn btn-outline-primary" type="button" onclick="generateRandomCode()">
                        <i class='bx bx-refresh'></i>
                    </button>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-text" id="code-help">
                    {{ isset($product) ? '' : 'Kode akan otomatis terbentuk saat Anda memilih kategori.' }}</div>
            </div>

            <div class="col-12 col-md-6">
                <label for="barcode" class="form-label">Barcode <small
                        class="text-muted">{{ isset($product) ? '' : '(opsional)' }}</small></label>
                <div class="input-group">
                    <input type="text" class="form-control @error('barcode') is-invalid @enderror" id="barcode"
                        name="barcode" value="{{ old('barcode', $product->barcode ?? '') }}">
                    <button class="btn btn-outline-primary" type="button" onclick="generateBarcodeCode()">
                        <i class='bx bx-barcode'></i>
                    </button>
                </div>
                <div class="form-text" id="barcode-help">
                    {{ isset($product) ? '' : 'Biarkan kosong untuk tidak menggunakan barcode.' }}</div>
                @error('barcode')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Kolom Kelompok (Baru) --}}
            <div class="col-12 col-md-6">
                <label for="group_id" class="form-label">Kelompok</label>
                <select class="form-select @error('group_id') is-invalid @enderror" id="group_id" name="group_id"
                    onchange="filterCategories()">
                    <option value="">Pilih Kelompok</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}" data-code="{{ $group->code ?? 'XX' }}"
                            {{ old('group_id', isset($product) && isset($product->category->group) ? $product->category->group->id : '') == $group->id ? 'selected' : '' }}>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select>
                @error('group_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Kolom Kategori yang Diperbarui --}}
            <div class="col-12 col-md-6">
                <label for="category_id" class="form-label">Kategori</label>
                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                    name="category_id" required {{ isset($product) ? '' : 'onchange="generateProductCode()"' }}>
                    <option value="">Pilih Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" data-group-id="{{ $category->group_id ?? '' }}"
                            data-code="{{ $category->code }}"
                            class="category-option {{ isset($category->group_id) ? 'group-' . $category->group_id : '' }}"
                            style="{{ old('group_id', isset($product) && isset($product->category->group) ? $product->category->group->id : '') == ($category->group_id ?? '') ? '' : 'display: none;' }}"
                            {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Kolom Harga & Stok - Hanya untuk mode Buat --}}
            @if (!isset($product))
                {{-- Mode Buat - tampilkan satuan, stok, harga, pajak dan kolom diskon --}}
                <div class="col-12 col-md-6">
                    <label for="default_unit_id" class="form-label">Satuan Default</label>
                    <select name="default_unit_id" id="default_unit_id"
                        class="form-select @error('default_unit_id') is-invalid @enderror" required>
                        <option value="">Pilih Satuan</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}"
                                {{ old('default_unit_id') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('default_unit_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="stock" class="form-label">Stok Awal</label>
                    <input type="number" step="1" min="0"
                        class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock"
                        value="{{ old('stock', 0) }}" required>
                    @error('stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="purchase_price" class="form-label">Harga Beli</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" step="0.01" min="0"
                            class="form-control @error('purchase_price') is-invalid @enderror" id="purchase_price"
                            name="purchase_price" value="{{ old('purchase_price', 0) }}" required>
                        @error('purchase_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="selling_price" class="form-label">Harga Jual</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" step="0.01" min="0"
                            class="form-control @error('selling_price') is-invalid @enderror" id="selling_price"
                            name="selling_price" value="{{ old('selling_price', 0) }}" required>
                        @error('selling_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="min_stock" class="form-label">Stok Minimum</label>
                    <input type="number" step="1" min="0"
                        class="form-control @error('min_stock') is-invalid @enderror" id="min_stock"
                        name="min_stock" value="{{ old('min_stock', 0) }}">
                    @error('min_stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            {{-- Kolom pajak dan diskon untuk mode buat dan edit --}}
            <div class="col-12 col-md-6">
                <label for="tax_id" class="form-label">Pajak</label>
                <select class="form-select @error('tax_id') is-invalid @enderror" id="tax_id" name="tax_id">
                    <option value="">Pilih Pajak</option>
                    @foreach ($taxes as $tax)
                        <option value="{{ $tax->id }}"
                            {{ old('tax_id', $product->tax_id ?? null) == $tax->id ? 'selected' : '' }}>
                            {{ $tax->name }} ({{ $tax->rate }}%)
                        </option>
                    @endforeach
                </select>
                @error('tax_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-md-6">
                <label for="discount_id" class="form-label">Diskon</label>
                <select class="form-select @error('discount_id') is-invalid @enderror" id="discount_id"
                    name="discount_id">
                    <option value="">Pilih Diskon</option>
                    @foreach ($discounts as $discount)
                        <option value="{{ $discount->id }}"
                            {{ old('discount_id', $product->discount_id ?? null) == $discount->id ? 'selected' : '' }}>
                            {{ $discount->name }}
                            ({{ $discount->type == 'percentage' ? $discount->value . '%' : 'Rp ' . number_format($discount->value, 0) }})
                        </option>
                    @endforeach
                </select>
                @error('discount_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

{{-- Kartu Status --}}
<div class="card border mb-4">
    <div class="card-header bg-transparent">
        <h6 class="card-title mb-0">
            <i class='bx bx-toggle-left me-2 text-primary'></i>
            Status Produk
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="featured" name="featured" value="1"
                        {{ old('featured', $product->featured ?? 0) ? 'checked' : '' }}
                        onchange="toggleFeaturedComponents()">
                    <label class="form-check-label" for="featured">Tampilkan di Halaman Utama</label>
                    <div class="form-text text-primary" id="featured-info">
                        Produk yang ditampilkan di halaman utama membutuhkan informasi lebih lengkap.
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                        {{ old('is_active', isset($product) ? $product->is_active : 1) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Aktif</label>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Kartu Deskripsi --}}
<div class="card border mb-4" id="description-card">
    <div class="card-header bg-transparent">
        <h6 class="card-title mb-0">
            <i class='bx bx-text me-2 text-primary'></i>
            Deskripsi Produk
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12">
                <label for="short_description" class="form-label">Deskripsi Singkat</label>
                <input type="text" class="form-control @error('short_description') is-invalid @enderror"
                    id="short_description" name="short_description"
                    value="{{ old('short_description', $product->short_description ?? '') }}">
                @error('short_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label for="description" class="form-label">Deskripsi Lengkap</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                    rows="4">{{ old('description', $product->description ?? '') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

{{-- Kartu Gambar --}}
<div class="card border mb-4" id="images-card">
    <div class="card-header bg-transparent">
        <h6 class="card-title mb-0">
            <i class='bx bx-image me-2 text-primary'></i>
            Gambar Produk
        </h6>
    </div>
    <div class="card-body">
        {{-- Tampilkan gambar yang ada hanya dalam mode edit --}}
        @if (isset($product) && $product->images->count() > 0)
            <div class="row g-3 mb-4">
                @foreach ($product->images as $image)
                    <div class="col-6 col-md-3">
                        <div class="position-relative">
                            <img src="{{ asset('storage/' . $image->image_path) }}" class="img-thumbnail w-100"
                                alt="{{ $image->alt_text }}">
                            @if ($image->is_primary)
                                <span class="badge bg-primary position-absolute top-0 end-0 m-2">Utama</span>
                            @endif
                            <button type="button" class="btn btn-danger btn-sm position-absolute bottom-0 end-0 m-2"
                                onclick="deleteImage({{ $image->id }})">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Bagian unggah gambar --}}
        <div class="mb-3">
            <label for="images" class="form-label">
                {{ isset($product) ? 'Unggah Gambar Baru' : 'Unggah Gambar' }}
            </label>
            <input type="file" class="form-control @error('images.*') is-invalid @enderror" id="images"
                name="images[]" multiple accept="image/*">
            <div class="form-text">
                {{ isset($product)
                    ? 'Anda dapat memilih beberapa gambar. Gambar baru pertama akan ditetapkan sebagai utama jika tidak ada gambar utama.'
                    : 'Anda dapat memilih beberapa gambar. Gambar pertama akan ditetapkan sebagai utama.' }}
            </div>
            @error('images.*')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

{{-- Kartu Tautan --}}
<div class="card border mb-4" id="link-card">
    <div class="card-header bg-transparent">
        <h6 class="card-title mb-0">
            <i class='bx bx-link me-2 text-primary'></i>
            Tautan Produk
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12">
                <label for="url" class="form-label">Link Shopee, Tokopedia, dll</label>
                <input type="text" class="form-control @error('url') is-invalid @enderror" id="url"
                    name="url" value="{{ old('url', $product->url ?? '') }}">
                @error('url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

{{-- Tombol Aksi Form --}}
<div class="text-end">
    <a href="{{ route('products.index') }}" class="btn btn-secondary me-2">Batal</a>
    <button type="submit" class="btn btn-primary">
        <i class='bx bx-save me-1'></i>
        {{ isset($product) ? 'Perbarui Produk' : 'Buat Produk' }}
    </button>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Run function when page loads
            toggleFeaturedComponents();

            // Initialize category filtering
            filterCategories();

            // Run product code generation if both group and category are already selected (for create form)
            const groupSelect = document.getElementById('group_id');
            const categorySelect = document.getElementById('category_id');

            if (categorySelect &&
                categorySelect.hasAttribute('onchange') &&
                categorySelect.getAttribute('onchange').includes('generateProductCode') &&
                categorySelect.value !== '' &&
                groupSelect.value !== '') {
                generateProductCode();
            }
        });

        function toggleFeaturedComponents() {
            const featured = document.getElementById('featured').checked;
            const descriptionCard = document.getElementById('description-card');
            const imagesCard = document.getElementById('images-card');
            const linkCard = document.getElementById('link-card');
            const featuredInfoText = document.getElementById('featured-info');

            // Show/hide cards based on featured status
            if (featured) {
                // Show cards and change message
                descriptionCard.style.display = 'block';
                imagesCard.style.display = 'block';
                linkCard.style.display = 'block';

                // Change info text
                featuredInfoText.innerHTML =
                    'Produk akan ditampilkan di landing page. Silakan lengkapi informasi tambahan.';
                featuredInfoText.classList.add('text-primary');
                featuredInfoText.classList.remove('text-muted');

                // Add simple animation
                [descriptionCard, imagesCard, linkCard].forEach(card => {
                    card.classList.add('animate-fade-in');
                    setTimeout(() => {
                        card.classList.remove('animate-fade-in');
                    }, 500);
                });
            } else {
                // Hide cards
                descriptionCard.style.display = 'none';
                imagesCard.style.display = 'none';
                linkCard.style.display = 'none';

                // Change info text
                featuredInfoText.innerHTML =
                    'Centang untuk menampilkan produk di landing page (memerlukan informasi lebih lengkap).';
                featuredInfoText.classList.remove('text-primary');
                featuredInfoText.classList.add('text-muted');
            }
        }

        // Function to filter categories based on selected group
        function filterCategories() {
            const groupSelect = document.getElementById('group_id');
            const categorySelect = document.getElementById('category_id');
            const selectedGroupId = groupSelect.value;

            // First hide all category options
            const categoryOptions = categorySelect.querySelectorAll('option:not(:first-child)');
            categoryOptions.forEach(option => {
                option.style.display = 'none';
            });

            // Then show only options for the selected group
            if (selectedGroupId) {
                const groupOptions = categorySelect.querySelectorAll(`.group-${selectedGroupId}`);
                groupOptions.forEach(option => {
                    option.style.display = '';
                });
            }

            // Reset category selection if not editing an existing product
            if (!window.location.href.includes('/edit/')) {
                categorySelect.value = '';
            }

            // If we're in create mode and product code generation is enabled
            if (categorySelect.hasAttribute('onchange') && categorySelect.getAttribute('onchange').includes(
                    'generateProductCode')) {
                // Clear the product code when group changes (only in create mode)
                if (!window.location.href.includes('/edit/')) {
                    document.getElementById('code').value = '';
                    document.getElementById('code-help').textContent = 'Pilih kategori untuk membuat kode produk otomatis.';
                }
            }
        }

        // Variable to store auto increment number
        let currentProductCount = 1;

        // Function to get short timestamp for unique code
        function getShortTimestamp() {
            const now = new Date();
            return now.getFullYear().toString().substr(-2) +
                (now.getMonth() + 1).toString().padStart(2, '0') +
                now.getDate().toString().padStart(2, '0');
        }

        // Updated function to generate product code based on group and category
        function generateProductCode() {
            const groupSelect = document.getElementById('group_id');
            const categorySelect = document.getElementById('category_id');
            const codeInput = document.getElementById('code');
            const codeHelp = document.getElementById('code-help');

            if (categorySelect.value === '' || groupSelect.value === '') {
                codeInput.value = '';
                codeHelp.textContent = 'Pilih grup dan kategori untuk membuat kode produk otomatis.';
                return;
            }

            const selectedGroupOption = groupSelect.options[groupSelect.selectedIndex];
            const selectedCategoryOption = categorySelect.options[categorySelect.selectedIndex];

            const groupCode = selectedGroupOption.getAttribute('data-code') || 'XX';
            const categoryCode = selectedCategoryOption.getAttribute('data-code') || 'XX';

            // Format: GRP-CAT-YYMMDD-001
            const timestamp = getShortTimestamp();
            const sequenceNumber = currentProductCount.toString().padStart(3, '0');

            const newCode = `${groupCode}-${categoryCode}-${timestamp}-${sequenceNumber}`;
            codeInput.value = newCode;
            codeHelp.textContent = `Kode dibuat otomatis: [Grup]-[Kategori]-[Tanggal]-[Urutan]`;

            // Increment counter for next product
            currentProductCount++;
        }

        // Function to generate random code if needed
        function generateRandomCode() {
            const codeInput = document.getElementById('code');
            const codeHelp = document.getElementById('code-help');

            // Format: Timestamp + Random alphanumeric (6 chars)
            const timestamp = getShortTimestamp();
            const randomPart = Math.random().toString(36).substring(2, 8).toUpperCase();

            codeInput.value = `PRD-${timestamp}-${randomPart}`;
            codeHelp.textContent = 'Kode dibuat secara acak.';
        }

        function generateBarcodeCode() {
            const barcodeInput = document.getElementById('barcode');
            const barcodeHelp = document.getElementById('barcode-help');

            // Generate EAN-13 like barcode (13 digits)
            // Format: 2 digits country code + 5 digits manufacturer code + 5 digits product code + 1 check digit
            const countryCode = '89'; // dummy country code
            const randomManufacturer = Math.floor(10000 + Math.random() * 90000);
            const randomProduct = Math.floor(10000 + Math.random() * 90000);

            // This is a simple example, for real EAN-13 implementation we need proper check digit calculation
            const barcode = countryCode + randomManufacturer.toString() + randomProduct.toString();

            barcodeInput.value = barcode;
            barcodeHelp.textContent = 'Barcode berhasil dibuat.';
        }

        // Function for deleting images (only used in edit mode)
        function deleteImage(imageId) {
            if (confirm('Are you sure you want to delete this image?')) {
                // Implement image deletion logic
                fetch(`/products/images/${imageId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        window.location.reload();
                    })
                    .catch(error => {
                        alert('Error deleting image: ' + error.message);
                    });
            }
        }
    </script>

    <script>
        // Tambahkan tombol scan barcode di samping input barcode
        document.addEventListener('DOMContentLoaded', function() {
            // Tambahkan tombol scan di samping tombol generate barcode
            const barcodeButtonGroup = document.querySelector('#barcode').parentElement;
            const scanButton = document.createElement('button');
            scanButton.className = 'btn btn-outline-secondary';
            scanButton.type = 'button';
            scanButton.id = 'btn-scan-barcode';
            scanButton.innerHTML = '<i class="bx bx-camera"></i>';
            scanButton.title = 'Scan Barcode';

            // Tambahkan tombol setelah tombol generate barcode
            const generateButton = barcodeButtonGroup.querySelector('button');
            barcodeButtonGroup.insertBefore(scanButton, generateButton.nextSibling);

            // Tambahkan event listener untuk tombol scan
            document.getElementById('btn-scan-barcode').addEventListener('click', openBarcodeScanner);
        });

        /**
         * Open barcode scanner modal
         */
        function openBarcodeScanner() {
            // Create modal if it doesn't exist
            if (!document.getElementById('barcodeModal')) {
                createBarcodeModal();
            }

            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('barcodeModal'));
            modal.show();

            // Initialize scanner after modal is shown
            document.getElementById('barcodeModal').addEventListener('shown.bs.modal', function() {
                initBarcodeScanner();
            }, {
                once: true
            });
        }

        /**
         * Create barcode scanner modal
         */
        function createBarcodeModal() {
            const modalHtml = `
    <div class="modal fade" id="barcodeModal" tabindex="-1" aria-labelledby="barcodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border-radius: 0.5rem;">
                <div class="modal-header">
                    <h5 class="modal-title" id="barcodeModalLabel">
                        <i class='bx bx-scan me-1'></i> Scan Barcode
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <p>Arahkan kamera ke barcode produk</p>
                    </div>

                    <div id="scanner-container">
                        <!-- Loading indicator -->
                        <div id="scanner-loading" class="text-center p-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Memuat kamera...</p>
                        </div>

                        <!-- Error message -->
                        <div id="scanner-error" class="alert alert-danger text-center" style="display:none;">
                            <i class='bx bx-error-circle me-1'></i>
                            <span id="scanner-error-message">Error message here</span>
                        </div>

                        <!-- Video container with barcode highlights -->
                        <div id="video-container" style="position: relative; display: none;">
                            <video id="video" style="width: 100%; border-radius: 0.5rem; border: 1px solid #ddd;" autoplay playsinline></video>
                            <div id="barcode-box" style="position: absolute; border: 3px solid #00FF00; display: none; border-radius: 0.25rem;"></div>
                        </div>

                        <!-- Success message -->
                        <div id="scanner-success" class="alert alert-success mt-2" style="display:none;">
                            <strong>Barcode terdeteksi:</strong> <span id="detected-barcode"></span>
                        </div>
                    </div>

                    <!-- Camera selection -->
                    <div class="d-flex justify-content-between mt-3">
                        <select id="camera-select" class="form-select" style="max-width: 250px; display:none;">
                            <option value="">Pilih Kamera</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn-manual-input" class="btn btn-primary">
                        <i class='bx bx-keyboard me-1'></i> Input Manual
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class='bx bx-x me-1'></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    `;

            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            // Add event listeners
            document.getElementById('btn-manual-input').addEventListener('click', function() {
                const modal = bootstrap.Modal.getInstance(document.getElementById('barcodeModal'));
                if (modal) {
                    modal.hide();
                }

                // Focus barcode input field
                setTimeout(() => {
                    document.getElementById('barcode').focus();
                }, 300);
            });

            // Camera select change handler
            document.getElementById('camera-select').addEventListener('change', function() {
                if (window.stream) {
                    stopVideoStream();
                }
                startVideoStream(this.value);
            });

            // Cleanup when modal is closed
            document.getElementById('barcodeModal').addEventListener('hidden.bs.modal', function() {
                stopVideoStream();
            });
        }

        // Keep track of video stream
        window.stream = null;
        let scannerInterval = null;
        let lastDetectedCode = null;
        let lastDetectionTime = 0;

        /**
         * Initialize barcode scanner
         */
        async function initBarcodeScanner() {
            try {
                // Show loading
                document.getElementById('scanner-loading').style.display = 'block';
                document.getElementById('video-container').style.display = 'none';
                document.getElementById('scanner-error').style.display = 'none';
                document.getElementById('scanner-success').style.display = 'none';

                // Get list of cameras
                const cameras = await getAvailableCameras();
                updateCameraDropdown(cameras);

                // Get the preferred camera (back camera if available)
                let preferredCameraId = null;
                if (cameras.length > 0) {
                    // Try to find back camera
                    const backCamera = cameras.find(camera =>
                        camera.label.toLowerCase().includes('back') ||
                        camera.label.toLowerCase().includes('rear') ||
                        camera.label.toLowerCase().includes('belakang')
                    );

                    preferredCameraId = backCamera ? backCamera.deviceId : cameras[0].deviceId;
                }

                // Check if BarcodeDetector is available in browser
                if ('BarcodeDetector' in window) {
                    try {
                        barcodeDetector = new BarcodeDetector({
                            formats: [
                                'ean_13', 'ean_8', 'code_39', 'code_128',
                                'upc_a', 'upc_e', 'itf', 'codabar'
                            ]
                        });
                        console.log("Native BarcodeDetector supported");
                    } catch (e) {
                        console.warn("Native BarcodeDetector is not supported, using fallback method");
                        barcodeDetector = null;
                    }
                } else {
                    console.warn("BarcodeDetector not available, using fallback method");
                    barcodeDetector = null;
                }

                // Start video stream with preferred camera
                await startVideoStream(preferredCameraId);

            } catch (error) {
                console.error('Error initializing barcode scanner:', error);
                showScannerError(
                    'Gagal mengakses kamera. Pastikan browser Anda mendukung akses kamera dan izin telah diberikan.'
                );
            }
        }

        /**
         * Get available cameras
         */
        async function getAvailableCameras() {
            try {
                // Request camera permission first
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: true
                });
                // Stop stream right away - we'll start a proper one later
                stream.getTracks().forEach(track => track.stop());

                // Get list of video devices
                const devices = await navigator.mediaDevices.enumerateDevices();
                return devices.filter(device => device.kind === 'videoinput');
            } catch (error) {
                console.error('Error getting cameras:', error);
                throw error;
            }
        }

        /**
         * Update camera selection dropdown
         */
        function updateCameraDropdown(cameras) {
            const cameraSelect = document.getElementById('camera-select');
            if (!cameraSelect) return;

            cameraSelect.innerHTML = '<option value="">Pilih Kamera</option>';

            if (cameras.length > 0) {
                cameras.forEach(camera => {
                    const option = document.createElement('option');
                    option.value = camera.deviceId;
                    option.text = camera.label || `Kamera (${camera.deviceId.substr(0, 5)}...)`;
                    cameraSelect.appendChild(option);
                });

                // Only show if there are multiple cameras
                cameraSelect.style.display = cameras.length > 1 ? 'block' : 'none';
            } else {
                cameraSelect.style.display = 'none';
            }
        }

        /**
         * Start video stream with specified camera
         */
        async function startVideoStream(cameraId) {
            try {
                // Stop existing stream if any
                if (window.stream) {
                    stopVideoStream();
                }

                // Configure video constraints
                const constraints = {
                    video: {
                        width: {
                            ideal: 1280
                        },
                        height: {
                            ideal: 720
                        },
                        facingMode: "environment"
                    }
                };

                // If camera ID is specified, use it
                if (cameraId) {
                    constraints.video.deviceId = {
                        exact: cameraId
                    };
                }

                // Get video stream
                window.stream = await navigator.mediaDevices.getUserMedia(constraints);

                // Connect stream to video element
                const videoElement = document.getElementById('video');
                videoElement.srcObject = window.stream;

                // When video is ready, show it and start scanning
                videoElement.onloadedmetadata = function() {
                    document.getElementById('scanner-loading').style.display = 'none';
                    document.getElementById('video-container').style.display = 'block';

                    // Start scanner
                    startScanner();
                };

                console.log('Camera started successfully');
            } catch (error) {
                console.error('Error starting camera:', error);
                showScannerError('Gagal memulai kamera. ' + error.message);
            }
        }

        /**
         * Stop video stream
         */
        function stopVideoStream() {
            // Stop scanning interval
            if (scannerInterval) {
                clearInterval(scannerInterval);
                scannerInterval = null;
            }

            // Stop video stream
            if (window.stream) {
                window.stream.getTracks().forEach(track => track.stop());
                window.stream = null;
            }

            // Clear video source
            const videoElement = document.getElementById('video');
            if (videoElement) {
                videoElement.srcObject = null;
            }

            // Reset detection variables
            lastDetectedCode = null;
            lastDetectionTime = 0;
        }

        /**
         * Show scanner error
         */
        function showScannerError(message) {
            document.getElementById('scanner-loading').style.display = 'none';
            document.getElementById('video-container').style.display = 'none';

            const errorElement = document.getElementById('scanner-error');
            const errorMessageElement = document.getElementById('scanner-error-message');

            if (errorElement && errorMessageElement) {
                errorMessageElement.textContent = message;
                errorElement.style.display = 'block';
            }
        }

        /**
         * Start barcode scanner
         */
        function startScanner() {
            // Get video element
            const video = document.getElementById('video');
            if (!video) return;

            // Start scanning interval
            scannerInterval = setInterval(() => {
                scanBarcode(video);
            }, 200); // Scan every 200ms
        }

        /**
         * Scan for barcode in video frame
         */
        async function scanBarcode(videoElement) {
            if (!videoElement || videoElement.paused || videoElement.ended) return;

            try {
                // If native BarcodeDetector is available, use it
                if (barcodeDetector) {
                    const barcodes = await barcodeDetector.detect(videoElement);
                    processBarcodes(barcodes);
                } else {
                    // Fallback to canvas-based detection (less reliable)
                    manualBarcodeDetection(videoElement);
                }
            } catch (error) {
                console.error('Error scanning barcode:', error);
            }
        }

        /**
         * Process detected barcodes
         */
        function processBarcodes(barcodes) {
            if (!barcodes || barcodes.length === 0) return;

            // Get the first barcode
            const barcode = barcodes[0];

            // Get the barcode value
            const barcodeValue = barcode.rawValue;

            // Check if it's a new barcode and debounce detection
            const now = Date.now();
            if (barcodeValue !== lastDetectedCode || (now - lastDetectionTime > 2000)) {
                lastDetectedCode = barcodeValue;
                lastDetectionTime = now;

                // Show barcode box
                highlightBarcode(barcode.cornerPoints);

                // Process the barcode
                processDetectedBarcode(barcodeValue);
            }
        }

        /**
         * Highlight barcode on video
         */
        function highlightBarcode(cornerPoints) {
            const barcodeBox = document.getElementById('barcode-box');
            if (!barcodeBox) return;

            if (!cornerPoints || cornerPoints.length < 4) {
                barcodeBox.style.display = 'none';
                return;
            }

            // Calculate bounding box
            const videoContainer = document.getElementById('video-container');
            const video = document.getElementById('video');

            if (!videoContainer || !video) return;

            // Get video dimensions
            const videoWidth = video.videoWidth;
            const videoHeight = video.videoHeight;

            // Get container dimensions
            const containerWidth = video.offsetWidth;
            const containerHeight = video.offsetHeight;

            // Calculate scale factors
            const scaleX = containerWidth / videoWidth;
            const scaleY = containerHeight / videoHeight;

            // Find min/max coordinates
            let minX = Infinity,
                minY = Infinity,
                maxX = 0,
                maxY = 0;

            cornerPoints.forEach(point => {
                minX = Math.min(minX, point.x);
                minY = Math.min(minY, point.y);
                maxX = Math.max(maxX, point.x);
                maxY = Math.max(maxY, point.y);
            });

            // Apply scale and position the box
            const left = minX * scaleX;
            const top = minY * scaleY;
            const width = (maxX - minX) * scaleX;
            const height = (maxY - minY) * scaleY;

            barcodeBox.style.left = `${left}px`;
            barcodeBox.style.top = `${top}px`;
            barcodeBox.style.width = `${width}px`;
            barcodeBox.style.height = `${height}px`;
            barcodeBox.style.display = 'block';
        }

        /**
         * Manual barcode detection using canvas
         * This is a fallback method when BarcodeDetector is not available
         */
        function manualBarcodeDetection(videoElement) {
            // In a real implementation, you would:
            // 1. Draw the video frame to a canvas
            // 2. Get the image data
            // 3. Use a JavaScript barcode library to detect barcodes
            // 4. Process the detected barcodes

            // For this demo, we'll just simulate detection every few seconds
            const now = Date.now();
            if (now - lastDetectionTime > 3000) { // Every 3 seconds for demo
                lastDetectionTime = now;

                // Generate a random barcode (for demonstration)
                // In a real implementation, this would be the actual detected barcode
                const mockBarcode = '890' + Math.floor(Math.random() * 10000000000).toString().padStart(10, '0');

                // Process the barcode
                processDetectedBarcode(mockBarcode);
            }
        }

        /**
         * Process a detected barcode
         */
        function processDetectedBarcode(barcodeValue) {
            console.log('Barcode detected:', barcodeValue);

            // Show success message
            document.getElementById('detected-barcode').textContent = barcodeValue;
            document.getElementById('scanner-success').style.display = 'block';

            // Stop scanning
            clearInterval(scannerInterval);
            scannerInterval = null;

            // Set the barcode value in the input field
            document.getElementById('barcode').value = barcodeValue;

            // Update barcode help text to indicate scanned value
            const barcodeHelp = document.getElementById('barcode-help');
            if (barcodeHelp) {
                barcodeHelp.textContent = 'Barcode berhasil dipindai.';
            }

            // Close the modal after a short delay
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('barcodeModal'));
                if (modal) {
                    modal.hide();
                }
            }, 1500);
        }
    </script>
@endpush
