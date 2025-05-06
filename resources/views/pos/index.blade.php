<!DOCTYPE html>
<html lang="id" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Kasir | Toko Cacha</title>
    <meta name="description" content="" />
    @include('pos.partials.styles')

</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar layout-without-menu">
        <div class="layout-container">
            <!-- Layout page -->
            <div class="layout-page">
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <div class="container-fluid h-100 p-0">
                        <div class="row h-100 g-0">
                            <!-- Main Content Column -->
                            <div class="col-lg-8 col-md-12 pos-main-column">
                                <div class="grid-container main-grid">
                                    <!-- Page Header -->
                                    <div class="page-header">
                                        <h4 class="fw-bold mb-0">Kasir Toko Cacha</h4>
                                        <div class="d-flex gap-2">
                                            <a href="{{ url('/dashboard') }}"
                                                class="btn btn-primary d-flex align-items-center">
                                                <i class='bx bx-home-alt me-1'></i> Dashboard
                                            </a>
                                            <button type="button" class="btn btn-warning d-flex align-items-center"
                                                id="btn-show-pending">
                                                <i class='bx bx-time me-1'></i> Tertunda
                                            </button>
                                            <button type="button" class="btn btn-danger d-flex align-items-center"
                                                id="btn-clear-cart">
                                                <i class='bx bx-trash me-1'></i> Hapus
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Hidden Transaction Info -->
                                    <div class="d-none">
                                        <input type="text" id="pos_invoice_number" value="{{ $invoiceNumber }}"
                                            readonly>
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
                                            <input type="hidden" id="pos_store_id"
                                                value="{{ Auth::user()->store_id }}">
                                        @endif
                                    </div>

                                    <!-- Product Search Section -->
                                    <div class="grid-section">
                                        <div class="grid-section-header">
                                            <i class='bx bx-search me-1'></i> Pencarian Produk
                                        </div>
                                        <div class="grid-section-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label
                                                        class="form-label d-flex align-items-center fw-bold text-uppercase mb-1"
                                                        for="pos_barcode">
                                                        <i class='bx bx-barcode me-1'></i> Scan Barcode
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class='bx bx-scan'></i></span>
                                                        <input type="text" class="form-control" id="pos_barcode"
                                                            placeholder="Scan atau masukkan barcode" autofocus>
                                                        <button class="btn btn-outline-secondary" type="button"
                                                            id="btn-camera">
                                                            <i class='bx bx-camera'></i>
                                                        </button>
                                                    </div>
                                                    <small class="mt-1 d-block">Tekan Enter setelah scan</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <label
                                                        class="form-label d-flex align-items-center fw-bold text-uppercase mb-1"
                                                        for="pos_search_product">
                                                        <i class='bx bx-search me-1'></i> Cari Produk
                                                    </label>
                                                    <div class="position-relative">
                                                        <select class="form-select" id="pos_search_product"></select>
                                                        <div id="pos_product_list"></div>
                                                    </div>
                                                    <small class="mt-1 d-block text-end">
                                                        <span class="badge bg-label-secondary">F2</span> Hapus
                                                        <span class="badge bg-label-secondary">F3</span> Barcode
                                                        <span class="badge bg-label-secondary">F8</span> Bayar
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Shopping Cart Section -->
                                    <div class="grid-section d-flex flex-column">
                                        <div class="grid-section-header">
                                            <i class='bx bx-cart me-1'></i> Keranjang Belanja
                                        </div>
                                        <div class="table-container flex-grow-1">
                                            <div class="table-responsive">
                                                <table class="table mb-0" id="cart-table">
                                                    <thead>
                                                        <tr>
                                                            <th>PRODUK</th>
                                                            <th>SATUAN</th>
                                                            <th width="90">QTY</th>
                                                            <th>HARGA</th>
                                                            <th width="60">AKSI</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>

                                            <!-- Empty Cart Message -->
                                            <div id="empty-cart-message" class="empty-cart">
                                                <i class='bx bx-cart-alt fs-1 mb-2'></i>
                                                <h5 class="mb-2">Keranjang Belanja Kosong</h5>
                                                <p class="mb-3">Silakan scan barcode atau cari produk untuk
                                                    memulai transaksi</p>
                                                <button class="btn btn-primary" id="btn-start-shopping">
                                                    <i class='bx bx-search me-1'></i> Cari Produk
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Column -->
                            <div class="col-lg-4 col-md-12 pos-bill-column">
                                <div class="bill-grid">
                                    <!-- Header -->
                                    <div class="bill-header">
                                        <div class="bill-title">Detail Tagihan</div>
                                        <span class="badge bg-primary">#{{ $invoiceNumber }}</span>
                                    </div>

                                    <!-- Customer Selection -->
                                    <div class="form-group">
                                        <label class="form-label">Pelanggan</label>
                                        <select class="form-select" id="pos_customer_id">
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}"
                                                    {{ $customer->id === 1 ? 'selected' : '' }}>
                                                    {{ $customer->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Bill Summary -->
                                    <div class="bill-summary">
                                        <div class="summary-row">
                                            <div class="summary-label">Item</div>
                                            <div class="summary-value" id="item-count"></div>
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
                                                <input type="text" class="value-input discount"
                                                    id="pos_discount_amount" readonly>
                                            </div>
                                        </div>

                                        <div class="summary-row">
                                            <div class="summary-label">Pajak (10%)</div>
                                            <div class="summary-value">
                                                <input type="text" class="value-input" id="pos_tax_amount"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Total Amount -->
                                    <div class="total-row">
                                        <div class="fw-bold">Total</div>
                                        <div>
                                            <input type="text" class="total-input" id="pos_final_amount" readonly>
                                        </div>
                                    </div>

                                    <!-- Payment Method -->
                                    <div class="form-group">
                                        <label class="form-label">Metode Pembayaran</label>
                                        <select class="form-select" id="pos_payment_type">
                                            <option value="cash">Tunai</option>
                                            <option value="transfer">Transfer</option>
                                        </select>
                                    </div>

                                    <!-- Cash Amount -->
                                    <div id="pos_cash_amount_container" class="form-group">
                                        <label class="form-label">Jumlah Tunai</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" id="pos_cash_amount"
                                                placeholder="0">
                                        </div>
                                    </div>

                                    <!-- Change Amount -->
                                    <div id="pos_change_container" class="form-group">
                                        <label class="form-label">Kembalian</label>
                                        <div class="change-container">
                                            <input type="text" class="change-input" id="pos_change" readonly>
                                        </div>
                                    </div>

                                    <!-- Action Buttons - uses margin-top:auto instead of a spacer -->
                                    <div class="action-buttons">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <button type="button"
                                                    class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center fw-bold"
                                                    id="btn-pending">
                                                    <i class='bx bx-time-five me-1'></i> Pending
                                                </button>
                                            </div>
                                            <div class="col-6">
                                                <button type="button"
                                                    class="btn btn-primary w-100 d-flex align-items-center justify-content-center fw-bold"
                                                    id="btn-save">
                                                    <i class='bx bx-check-circle me-1'></i> Bayar
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
        </div>
    </div>

    <!-- Modal Transaksi Tertunda -->
    @include('pos.partials.pending-modal')

    <!-- Toast Notifications -->
    @include('components.toast')

    <!-- Scripts -->
    @include('pos.partials.scripts')

    <!-- Script for initializing POS -->
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

            // Tambahkan event listener untuk cash amount input
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
                        emptyCartMessage.style.display = 'block';
                    } else {
                        emptyCartMessage.style.display = 'none';
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

            // Enhance table rows
            function enhanceTableRows() {
                const tableRows = document.querySelectorAll('#cart-table tbody tr');
                tableRows.forEach((row, index) => {
                    // Add zebra striping
                    if (index % 2 === 0) {
                        row.classList.add('table-light');
                    }

                    // Add hover effect
                    row.addEventListener('mouseenter', function() {
                        this.classList.add('table-active');
                    });

                    row.addEventListener('mouseleave', function() {
                        if (index % 2 === 0) {
                            this.classList.remove('table-active');
                            this.classList.add('table-light');
                        } else {
                            this.classList.remove('table-active');
                        }
                    });
                });
            }

            // Observer for table changes
            const rowObserver = new MutationObserver(enhanceTableRows);
            const cartTableBody = document.querySelector('#cart-table tbody');
            if (cartTableBody) {
                rowObserver.observe(cartTableBody, {
                    childList: true
                });
                enhanceTableRows();
            }
        });
    </script>
</body>

</html>
