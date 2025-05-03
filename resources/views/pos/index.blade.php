<!DOCTYPE html>
<html lang="id" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Kasir | Toko Cacha</title>
    <meta name="description" content="" />
    @include('pos.partials.styles')

    <style>
        /* Warna kustom yang tetap diperlukan */
        :root {
            --primary-color: #696cff;
            --primary-hover: #5f62e6;
            --primary-light: #e7e7ff;
            --primary-dark: #5a5cb7;
            --green-color: #00B886;
            --green-light: #e7fff3;
        }

        /* Khusus layout POS */
        .pos-container {
            height: 100vh;
            overflow: hidden;
        }

        /* Main content dan sidebar */
        .pos-main-column {
            height: 100vh;
            overflow-y: auto;
            position: relative;
            width: calc(100% - 320px) !important;
            flex: 0 0 calc(100% - 320px) !important;
            max-width: calc(100% - 320px) !important;
            padding: 1.25rem !important;
        }

        /* Input group responsif */
        .input-group {
            display: flex;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            height: 42px;
            width: 100%;
        }

        /* Adaptasi untuk pos-bill-column */
        .pos-bill-column {
            width: 320px !important;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            /* Mencegah horizontal scroll */
            position: relative;
            flex: 0 0 320px !important;
            max-width: 320px !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        /* Divider vertikal */
        .pos-divider {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 1px;
            background-color: #e0e0e0;
        }

        /* Page title */
        .page-title {
            font-size: 1.375rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            padding-bottom: 0.625rem;
            border-bottom: 1px solid #e0e0e0;
        }

        /* Section cart header */
        .card-header {
            font-size: 1rem;
            font-weight: 600;
            padding: 0.75rem 0;
            background-color: transparent !important;
            border-bottom: none;
            margin-bottom: 0.9375rem;
            position: relative;
        }

        .card-header:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background-color: var(--primary-color);
            border-radius: 1.5px;
        }

        /* Bill details */
        .bill-details {
            width: 100%;
            height: 100%;
            background-color: white;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            /* Tambahkan ini untuk mencegah horizontal overflow */
            max-width: 100%;
            /* Pastikan tidak melebihi lebar parent */
        }


        .bill-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            padding-bottom: 0.625rem;
            border-bottom: 1px solid #eee;
        }

        .bill-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .bill-id {
            font-size: 0.8125rem;
            color: #666;
            background-color: #f7f8fa;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
        }

        /* Summary section */
        .summary-section {
            margin-bottom: 0.9375rem;
            width: 100%;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f0f0f0;
            width: 100%;
            flex-wrap: nowrap;
            /* Mencegah baris wrapping */
        }

        .summary-label {
            font-size: 0.875rem;
            color: #555;
            flex: 1;
            /* Tambahkan flex grow */
            min-width: 0;
            /* Untuk mencegah overflow */
            white-space: nowrap;
            /* Teks tidak wrap */
        }

        .summary-value {
            font-size: 0.875rem;
            color: #333;
            font-weight: 500;
            text-align: right;
            min-width: 80px;
            /* Menjaga minimal lebar */
        }

        .value-input {
            border: none;
            background: transparent;
            text-align: right;
            padding: 0;
            margin: 0;
            font-size: 0.875rem;
            color: #333;
            font-weight: 500;
            width: 100%;
            /* Mengisi parent container */
            min-width: 0;
            /* Mencegah overflow */
        }

        .discount {
            color: #ff6b6b;
        }

        /* Total row */
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 0.9375rem 0;
            margin: 0.9375rem 0;
            border-top: 1px solid #e0e0e0;
            border-bottom: 1px solid #e0e0e0;
            width: 100%;
            flex-wrap: nowrap;
        }

        .total-label {
            font-size: 1.125rem;
            font-weight: 600;
            flex: 1;
            min-width: 0;
        }

        .total-value {
            text-align: right;
            min-width: 80px;
        }

        .total-input {
            border: none;
            background: transparent;
            text-align: right;
            padding: 0;
            margin: 0;
            font-size: 1.125rem;
            color: var(--primary-color);
            font-weight: 700;
            width: 100%;
            min-width: 0;
        }

        /* Change container */
        .change-container {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 0.625rem 0.75rem;
        }

        .change-input {
            border: none;
            background: transparent;
            width: 100%;
            font-size: 1rem;
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Action buttons */
        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.625rem;
            margin-top: auto;
        }

        .btn-pending {
            padding: 0.75rem;
            border: 1px solid #ddd;
            background-color: white;
            color: #555;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
            height: 2.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-pending:hover {
            background-color: #f5f5f5;
        }

        .btn-pending i {
            margin-right: 0.375rem;
            font-size: 1rem;
        }

        .btn-process {
            padding: 0.75rem;
            border: none;
            background-color: var(--primary-color);
            color: white;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            height: 2.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-process:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-process i {
            margin-right: 0.375rem;
            font-size: 1rem;
        }

        /* Product list dropdown */
        #pos_product_list {
            position: absolute;
            width: 100%;
            max-height: 300px;
            overflow-y: auto;
            background: white;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            z-index: 1000;
            display: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .product-item {
            padding: 0.625rem;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }

        .product-item:hover {
            background-color: var(--primary-light);
        }

        .product-item.active {
            background-color: var(--primary-light);
        }

        /* Shortcut badges */
        .shortcut-badge {
            background-color: #e9ecef;
            color: #566a7f;
            font-family: monospace;
            font-size: 0.75rem;
            padding: 2px 5px;
            border-radius: 3px;
            margin: 0 2px;
        }

        /* Quick amount buttons */
        .quick-amount-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .quick-amount {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            background-color: #f8f9fa;
            color: #566a7f;
            border: 1px solid #ddd;
            border-radius: 0.25rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .quick-amount:hover {
            background-color: #e9ecef;
        }

        /* Empty cart message */
        #empty-cart-message {
            background-color: white;
            border-radius: 0.5rem;
            padding: 2.5rem 1.25rem;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .empty-cart-icon {
            margin-bottom: 1rem;
        }

        .empty-cart-icon i {
            font-size: 3.5rem;
            color: #d9dbe9;
        }

        /* Media queries */
        @media (max-height: 700px) {
            .bill-details {
                padding: 0.9375rem;
            }

            .form-group {
                margin-bottom: 0.625rem;
            }

            .summary-row {
                padding: 0.375rem 0;
            }

            .total-row {
                padding: 0.75rem 0;
                margin: 0.75rem 0;
            }
        }

        /* Fix untuk tampilan mobile pada zoom tinggi */
        @media (max-width: 768px),
        (min-resolution: 150dpi) {
            .bill-details {
                padding: 0.75rem;
            }

            .summary-label,
            .summary-value,
            .value-input {
                font-size: 0.8125rem;
            }

            .total-label,
            .total-input {
                font-size: 1rem;
            }
        }

        @media (max-width: 992px) {

            .pos-main-column,
            .pos-bill-column {
                width: 100% !important;
                flex: 0 0 100% !important;
                max-width: 100% !important;
                height: auto;
                overflow-x: hidden;
            }

            .bill-details {
                padding: 1rem;
            }

            .pos-bill-column {
                margin-top: 1.25rem !important;
                border-top: 1px solid #e0e0e0;
            }

            .pos-divider {
                display: none;
            }

            .summary-value,
            .total-value {
                min-width: 70px;
            }
        }
    </style>
</head>

<body>
    <div class="content-wrapper pos-container">
        <div class="container-fluid h-100 p-0">
            <div class="row h-100 g-0">
                <!-- Main Content -->
                <div class="col-lg-8 col-md-12 pos-main-column">
                    <!-- Page Title -->
                    <h1 class="page-title">Kasir Toko Cacha</h1>

                    <!-- Action Buttons - Disederhanakan & Dioptimalkan -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <a href="{{ url('/dashboard') }}" class="btn btn-sm btn-primary">
                            <i class='bx bx-home-alt me-1'></i> Dashboard
                        </a>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-warning" id="btn-show-pending">
                                <i class='bx bx-time me-1'></i> Tertunda
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" id="btn-clear-cart">
                                <i class='bx bx-trash me-1'></i> Hapus
                            </button>
                        </div>
                    </div>

                    <!-- Hidden Transaction Info -->
                    <div class="d-none">
                        <input type="text" id="pos_invoice_number" value="{{ $invoiceNumber }}" readonly>
                        @if (Auth::user()->role === 'admin')
                            <select id="pos_store_id">
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}"
                                        {{ $selectedStore && $selectedStore->id === $store->id ? 'selected' : '' }}>
                                        {{ $store->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="hidden" id="pos_store_id" value="{{ Auth::user()->store_id }}">
                        @endif
                    </div>

                    <!-- Product Search -->
                    <div class="mb-4 card">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6 pe-md-3">
                                    <label class="form-label d-flex align-items-center fw-semibold text-uppercase small mb-2"
                                        for="pos_barcode">
                                        <i class='bx bx-barcode text-primary me-1'></i> Scan Barcode
                                    </label>
                                    <div class="input-group" style="height: 38px;">
                                        <span class="input-group-text"><i class='bx bx-scan'></i></span>
                                        <input type="text" class="form-control" id="pos_barcode"
                                            placeholder="Scan atau masukkan barcode" autofocus>
                                        <button class="btn btn-outline-primary" type="button" id="btn-camera">
                                            <i class='bx bx-camera'></i>
                                        </button>
                                    </div>
                                    <small class="text-muted mt-2 d-block">Tekan Enter setelah scan</small>
                                </div>
                                <div class="col-md-6 ps-md-3">
                                    <label class="form-label d-flex align-items-center fw-semibold text-uppercase small mb-2"
                                        for="pos_search_product">
                                        <i class='bx bx-search text-primary me-1'></i> Cari Produk
                                    </label>
                                    <div class="position-relative" style="height: 38px;">
                                        <select class="form-control form-select" id="pos_search_product"></select>
                                        <div id="pos_product_list"></div>
                                    </div>
                                    <small class="text-muted mt-2 d-block text-end">
                                        <span class="shortcut-badge">F2</span> Hapus
                                        <span class="shortcut-badge">F3</span> Barcode
                                        <span class="shortcut-badge">F8</span> Bayar
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shopping Cart -->
                    <div>
                        <h6 class="card-header mb-3">Keranjang Belanja</h6>
                        <div class="cart-container card">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0" id="cart-table">
                                    <thead>
                                        <tr>
                                            <th>PRODUK</th>
                                            <th>SATUAN</th>
                                            <th width="90">QTY</th>
                                            <th>HARGA</th>
                                            <th>DISKON</th>
                                            <th>SUBTOTAL</th>
                                            <th width="60">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <!-- Empty Cart Message - Ditingkatkan -->
                            <div id="empty-cart-message" class="text-center py-4 rounded shadow-sm">
                                <div class="empty-cart-icon mb-3">
                                    <i class='bx bx-cart-alt' style="font-size: 3.5rem; color: #d9dbe9;"></i>
                                </div>
                                <h6 class="mb-2">Keranjang Belanja Kosong</h6>
                                <p class="text-muted mb-3">Silakan scan barcode atau cari produk untuk memulai transaksi
                                </p>
                                <button class="btn btn-sm btn-primary" id="btn-start-shopping">
                                    <i class='bx bx-search me-1'></i> Cari Produk
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Section -->
                <div class="col-lg-4 col-md-12 pos-bill-column">
                    <div class="pos-divider"></div>

                    <div class="bill-details">
                        <!-- Header -->
                        <div class="bill-header">
                            <div class="bill-title">Detail Tagihan</div>
                            <div class="bill-id">#{{ $invoiceNumber }}</div>
                        </div>

                        <!-- Customer Selection -->
                        <div class="form-group mb-4">
                            <label class="form-label">Pelanggan</label>
                            <select class="form-select" id="pos_customer_id">
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ $customer->id === 1 ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Bill Summary -->
                        <div class="summary-section">
                            <div class="summary-row">
                                <div class="summary-label">Item</div>
                                <div class="summary-value" id="item-count">0 (Items)</div>
                            </div>

                            <div class="summary-row">
                                <div class="summary-label">Subtotal</div>
                                <div class="summary-value">
                                    <input type="text" class="value-input" id="pos_subtotal" readonly>
                                </div>
                            </div>

                            <div class="summary-row">
                                <div class="summary-label">Diskon</div>
                                <div class="summary-value">
                                    <input type="text" class="value-input discount" id="pos_discount_amount"
                                        readonly>
                                </div>
                            </div>

                            <div class="summary-row">
                                <div class="summary-label">Pajak (10%)</div>
                                <div class="summary-value">
                                    <input type="text" class="value-input" id="pos_tax_amount" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Total Amount -->
                        <div class="total-row">
                            <div class="total-label">Total</div>
                            <div class="total-value">
                                <input type="text" class="total-input" id="pos_final_amount" readonly>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="form-group mb-3">
                            <label class="form-label">Metode Pembayaran</label>
                            <select class="form-select" id="pos_payment_type">
                                <option value="cash">Tunai</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>

                        <!-- Cash Amount - Ditambahkan Quick Amount Buttons -->
                        <div id="pos_cash_amount_container" class="form-group mb-3">
                            <label class="form-label">Jumlah Tunai</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="pos_cash_amount" placeholder="0">
                            </div>
                        </div>

                        <!-- Change Amount -->
                        <div id="pos_change_container" class="form-group mb-4">
                            <label class="form-label">Kembalian</label>
                            <div class="change-container">
                                <input type="text" class="change-input" id="pos_change" readonly>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button class="btn-pending" id="btn-pending">
                                <i class='bx bx-time-five'></i> Pending
                            </button>
                            <button class="btn-process" id="btn-save">
                                <i class='bx bx-check-circle'></i> Selesaikan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Transaksi Tertunda -->
    <div class="modal fade" id="pendingTransactionsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title">
                        <i class='bx bx-time-five me-2'></i>
                        Transaksi Tertunda
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Search bar untuk transaksi tertunda -->
                <div class="px-4 py-3 border-bottom">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0">
                            <i class='bx bx-search'></i>
                        </span>
                        <input type="text" id="pending-search" class="form-control border-0 bg-light"
                            placeholder="Cari berdasarkan faktur atau pelanggan...">
                    </div>
                </div>

                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0" id="pending-transactions-table">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3">Faktur</th>
                                    <th class="py-3">Tanggal</th>
                                    <th class="py-3">Pelanggan</th>
                                    <th class="py-3">Total</th>
                                    <th class="py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notifications -->
    @include('components.toast')

    <!-- Scripts -->
    @include('pos.partials.scripts')

    <!-- Script untuk perhitungan kembalian dan inisialisasi lainnya -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Event listener untuk payment type
            document.getElementById('pos_payment_type').addEventListener('change', function() {
                const isCash = this.value === 'cash';
                document.getElementById('pos_cash_amount_container').style.display = isCash ? 'block' :
                    'none';
                document.getElementById('pos_change_container').style.display = isCash ? 'block' : 'none';

                // Reset nilai cash amount jika bukan cash
                if (!isCash) {
                    document.getElementById('pos_cash_amount').value = '';
                    document.getElementById('pos_change').value = '';
                }
            });

            // Panggil trigger change untuk menginisialisasi tampilan berdasarkan nilai awal
            const event = new Event('change');
            document.getElementById('pos_payment_type').dispatchEvent(event);

            // Tambahkan event listener untuk cash amount input secara terpisah
            const cashAmountInput = document.getElementById('pos_cash_amount');
            if (cashAmountInput) {
                cashAmountInput.addEventListener('input', function() {
                    if (!this.value) {
                        document.getElementById('pos_change').value = '';
                        return;
                    }

                    const cashAmount = parseFloat(this.value) || 0;
                    const finalAmountStr = document.getElementById('pos_final_amount').value;
                    const cleanAmount = finalAmountStr
                        .replace(/[Rp\s]/g, '')
                        .replace(/\./g, '')
                        .replace(/,/g, '.');

                    const finalAmount = parseFloat(cleanAmount) || 0;
                    const change = cashAmount - finalAmount;
                    const displayChange = Math.max(0, Math.round(change * 100) / 100);

                    document.getElementById('pos_change').value = formatCurrency(displayChange);
                });
            }

            document.getElementById('btn-start-shopping')?.addEventListener('click', function() {
                document.getElementById('pos_search_product').focus();
            });

            // Show empty cart message if cart is empty
            function updateEmptyCartMessage() {
                const cartTable = document.querySelector('#cart-table tbody');
                const emptyCartMessage = document.getElementById('empty-cart-message');

                if (cartTable && emptyCartMessage) {
                    if (cartTable.children.length === 0) {
                        emptyCartMessage.classList.remove('d-none');
                    } else {
                        emptyCartMessage.classList.add('d-none');
                    }
                }
            }

            // Initial check
            updateEmptyCartMessage();

            // Check after cart operations might have completed
            setTimeout(updateEmptyCartMessage, 500);

            // Setup observer for cart changes
            const observer = new MutationObserver(updateEmptyCartMessage);
            observer.observe(document.querySelector('#cart-table tbody'), {
                childList: true
            });

            // Tambahan: Perbaikan tampilan pada tabel
            enhanceTableRows();
            improveEmptyCartMessage();

            // Observer untuk tabel yang berubah
            const rowObserver = new MutationObserver(function() {
                enhanceTableRows();
            });

            const cartTableBody = document.querySelector('#cart-table tbody');
            if (cartTableBody) {
                rowObserver.observe(cartTableBody, {
                    childList: true
                });
            }
        });

        // Fungsi untuk menambahkan kelas pada baris tabel saat ada data
        function enhanceTableRows() {
            const tableRows = document.querySelectorAll('#cart-table tbody tr');
            tableRows.forEach((row, index) => {
                // Tambahkan zebra striping
                if (index % 2 === 0) {
                    row.classList.add('table-light-row');
                    row.style.backgroundColor = '#f9fafb';
                }

                // Tambahkan hover effect
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f0f1ff';
                });

                row.addEventListener('mouseleave', function() {
                    if (index % 2 === 0) {
                        this.style.backgroundColor = '#f9fafb';
                    } else {
                        this.style.backgroundColor = '';
                    }
                });
            });
        }

        // Perbaikan tampilan kosong pada keranjang
        function improveEmptyCartMessage() {
            const emptyMessage = document.getElementById('empty-cart-message');
            if (emptyMessage) {
                // Tambahkan ilustrasi yang lebih baik
                const cartIcon = emptyMessage.querySelector('i');
                if (cartIcon) {
                    cartIcon.style.color = '#d9dbe9';
                    cartIcon.style.fontSize = '4rem';
                }

                // Perbaiki teks
                const messageText = emptyMessage.querySelector('p');
                if (messageText) {
                    messageText.style.color = '#8a909d';
                    messageText.style.fontSize = '14px';
                    messageText.style.marginTop = '15px';
                }
            }
        }
    </script>
</body>

</html>
