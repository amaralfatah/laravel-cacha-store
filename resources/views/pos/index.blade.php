<!DOCTYPE html>
<html lang="id" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Kasir | Toko Cacha</title>
    <meta name="description" content="" />
    @include('pos.partials.styles')

</head>

<body>
<div class="content-wrapper pos-container">
    <div class="container-fluid">
        <!-- Transaction Ticker Stats -->
        <div class="row mb-4">
            <div class="transaction-ticker">
                <div class="ticker-content">
                    <div class="ticker-item">
                        <i class='bx bx-calendar'></i>
                        <span id="today_date"></span>
                    </div>
                    <div class="ticker-divider"></div>
                    <div class="ticker-item">
                        <i class='bx bx-receipt'></i>
                        <span>Transaksi: <span id="today_count">0</span></span>
                    </div>
                    <div class="ticker-divider"></div>
                    <div class="ticker-item">
                        <i class='bx bx-money'></i>
                        <span>Total: <span id="today_total">Rp 0</span></span>
                    </div>
                    <div class="ticker-divider"></div>
                    <div class="ticker-item d-none d-md-flex">
                        <i class='bx bx-wallet'></i>
                        <span>Tunai: <span id="cash_total">Rp 0</span></span>
                    </div>
                    <div class="ticker-divider d-none d-md-block"></div>
                    <div class="ticker-item d-none d-md-flex">
                        <i class='bx bx-credit-card'></i>
                        <span>Transfer: <span id="transfer_total">Rp 0</span></span>
                    </div>
                    <div class="ticker-divider d-none d-md-block"></div>
                    <div class="ticker-item d-none d-md-flex">
                        <i class='bx bx-line-chart'></i>
                        <span>Rata-rata: <span id="average_transaction">Rp 0</span></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8 col-md-12">
                <!-- Actions -->
                <div class="mb-3 d-flex flex-wrap gap-2">
                    <a href="{{ url('/dashboard') }}" class="btn btn-outline-primary action-btn">
                        <i class='bx bx-home-alt'></i> Dashboard
                    </a>
                    <button type="button" class="btn btn-outline-danger action-btn" id="btn-clear-cart">
                        <i class='bx bx-trash'></i> Hapus Keranjang
                    </button>
                    <button type="button" class="btn btn-outline-warning action-btn" id="btn-show-pending">
                        <i class='bx bx-time'></i> Transaksi Tertunda
                    </button>
                </div>

                <!-- Transaction Info -->
                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h5 class="card-title m-0 fw-semibold">Informasi Transaksi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="pos_invoice_number">Nomor Faktur</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class='bx bx-receipt'></i></span>
                                    <input type="text" class="form-control" id="pos_invoice_number" value="{{ $invoiceNumber }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="pos_store_id">Toko</label>
                                @if(Auth::user()->role === 'admin')
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-store'></i></span>
                                        <select class="form-select" id="pos_store_id">
                                            @foreach ($stores as $store)
                                                <option value="{{ $store->id }}" {{ $selectedStore && $selectedStore->id === $store->id ? 'selected' : '' }}>
                                                    {{ $store->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-store'></i></span>
                                        <input type="text" class="form-control" value="{{ Auth::user()->store->name }}" readonly>
                                        <input type="hidden" id="pos_store_id" value="{{ Auth::user()->store_id }}">
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="pos_customer_id">Pelanggan</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class='bx bx-user'></i></span>
                                    <select class="form-select" id="pos_customer_id">
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ $customer->id === 1 ? 'selected' : '' }}>
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Search -->
                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h5 class="card-title m-0 fw-semibold">Pencarian Produk</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="pos_barcode">
                                    <i class='bx bx-barcode me-1'></i> Scan Barcode
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class='bx bx-scan'></i></span>
                                    <input type="text" class="form-control" id="pos_barcode" placeholder="Scan atau masukkan barcode" autofocus>
                                    <button class="btn btn-outline-primary" type="button" id="btn-camera">
                                        <i class='bx bx-camera'></i>
                                    </button>
                                </div>
                                <small class="text-muted mt-1 d-block">Tekan Enter setelah scan barcode</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="pos_search_product">
                                    <i class='bx bx-search me-1'></i> Cari Produk
                                </label>
                                <div class="product-search-container">
                                    <select class="form-select" id="pos_search_product"></select>
                                    <div id="pos_product_list"></div>
                                </div>

                                <!-- Recent products can be added here -->
                                <div class="d-flex flex-wrap gap-1 mt-2 d-none" id="recent-products">
                                    <!-- Will be populated by JS -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shopping Cart -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title m-0 fw-semibold">Keranjang Belanja</h5>
{{--                        <span class="badge bg-primary" id="cart-item-count">0 item</span>--}}
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover" id="cart-table">
                                <thead class="bg-light">
                                <tr>
                                    <th>Produk</th>
                                    <th>Satuan</th>
                                    <th width="80">Qty</th>
                                    <th>Harga</th>
                                    <th>Diskon</th>
                                    <th>Subtotal</th>
                                    <th width="80">Aksi</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div id="empty-cart-message" class="text-center py-4 d-none">
                            <i class='bx bx-cart text-muted' style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Keranjang belanja kosong</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="col-lg-4 col-md-12">
                <div class="card shadow-sm">
                    <!-- Total Payment -->
                    <div class="card-body bg-primary text-white total-payment">
                        <div class="payment-label">Total Pembayaran</div>
                        <div class="d-flex align-items-center">
                            <input type="text" class="form-control-plaintext text-white payment-value"
                                   id="pos_final_amount" readonly>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Summary -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3">Ringkasan</h6>
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span class="fw-medium">Subtotal</span>
                                <input type="text" class="form-control-plaintext text-end w-auto fw-semibold" id="pos_subtotal" readonly>
                            </div>
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span>Pajak</span>
                                <input type="text" class="form-control-plaintext text-end w-auto" id="pos_tax_amount" readonly>
                            </div>
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span>Diskon</span>
                                <input type="text" class="form-control-plaintext text-end w-auto" id="pos_discount_amount" readonly>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <h6 class="fw-semibold mb-3">Metode Pembayaran</h6>

                        <!-- Menggunakan select untuk compatibility dengan JS karena script menggunakan getElementById('pos_payment_type').value -->
                        <select class="form-select mb-3" id="pos_payment_type">
                            <option value="cash">Tunai</option>
                            <option value="transfer">Transfer</option>
                        </select>

                        <div id="pos_cash_amount_container" class="mb-4">
                            <label class="form-label fw-medium" for="pos_cash_amount">Jumlah Tunai</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="pos_cash_amount" placeholder="0">
                            </div>

                            <!-- Quick amount buttons -->
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary quick-amount" data-amount="5000">5rb</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary quick-amount" data-amount="10000">10rb</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary quick-amount" data-amount="20000">20rb</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary quick-amount" data-amount="50000">50rb</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary quick-amount" data-amount="100000">100rb</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary quick-amount" data-amount="200000">200rb</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary quick-amount" data-amount="500000">500rb</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary quick-amount" data-amount="1000000">1jt</button>
                            </div>
                        </div>

                        <div id="pos_change_container" class="mb-4">
                            <div class="bg-light p-3 rounded">
                                <label class="form-label fw-medium">Kembalian</label>
                                <div class="d-flex align-items-center">
                                    <input type="text" class="form-control-plaintext fs-3 fw-bold" id="pos_change" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-grid gap-2 mt-4">
                            <button class="btn btn-warning btn-lg" id="btn-pending">
                                <i class='bx bx-time-five me-1'></i> Pending
                            </button>
                            <button class="btn btn-primary btn-lg" id="btn-save">
                                <i class='bx bx-check-circle me-1'></i> Selesaikan Transaksi
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Shortcuts Reference Card (optional) -->
                <div class="card mt-3 mb-5 shadow-sm d-none d-lg-block">
                    <div class="card-header bg-light py-3">
                        <h5 class="card-title m-0 fw-semibold">
                            <i class='bx bx-keyboard me-2'></i> Shortcut Keys
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                <span>Scan Barcode</span>
                                <span class="badge bg-light text-dark">F3</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                <span>Transaksi Baru</span>
                                <span class="badge bg-light text-dark">F2</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                <span>Selesaikan</span>
                                <span class="badge bg-light text-dark">F8</span>
                            </div>
                        </div>
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

<!-- Script untuk Quick Amount dan perbaikan perhitungan kembalian -->
<script>
    // Script untuk tombol Quick Amount
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil semua tombol quick amount
        const quickAmountButtons = document.querySelectorAll('.quick-amount');

        // Tambahkan event listener untuk setiap tombol
        quickAmountButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Ambil nilai amount dari tombol
                const amount = this.getAttribute('data-amount');

                // Set nilai ke input cash amount
                document.getElementById('pos_cash_amount').value = amount;

                // Trigger event input untuk kalkulasi kembalian
                const inputEvent = new Event('input', { bubbles: true });
                document.getElementById('pos_cash_amount').dispatchEvent(inputEvent);
            });
        });

        // Event listener untuk payment type
        document.getElementById('pos_payment_type').addEventListener('change', function() {
            const isCash = this.value === 'cash';
            document.getElementById('pos_cash_amount_container').style.display = isCash ? 'block' : 'none';
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
    });
</script>

</body>
</html>
