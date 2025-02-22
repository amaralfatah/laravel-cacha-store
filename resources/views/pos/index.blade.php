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

    <div class="container-fluid p-4">
        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Action Buttons -->
                <div class="d-flex gap-2 mb-4">
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                        <i class="bx bx-arrow-back"></i>
                    </a>
                    <button type="button" class="btn btn-danger" id="btn-clear-cart">
                        <i class="bx bx-trash"></i>
                    </button>
                    <button type="button" class="btn btn-warning" id="btn-show-pending">
                        <i class="bx bx-time"></i> Pending
                    </button>
                </div>

                <!-- Transaction Info -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="pos_invoice_number">Nomor Faktur</label>
                                <input type="text" class="form-control" id="pos_invoice_number" value="{{ $invoiceNumber }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="pos_store_id">Toko</label>
                                @if(Auth::user()->role === 'admin')
                                    <select class="form-select" id="pos_store_id">
                                        @foreach ($stores as $store)
                                            <option value="{{ $store->id }}" {{ $selectedStore && $selectedStore->id === $store->id ? 'selected' : '' }}>
                                                {{ $store->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" class="form-control" value="{{ Auth::user()->store->name }}" readonly>
                                    <input type="hidden" id="pos_store_id" value="{{ Auth::user()->store_id }}">
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="pos_customer_id">Pelanggan</label>
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

                <!-- Product Search -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="pos_barcode">
                                    <i class='bx bx-barcode me-1'></i>Scan Barcode [F1]
                                </label>
                                <input type="text" class="form-control" id="pos_barcode" autofocus>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="pos_search_product">
                                    <i class='bx bx-search me-1'></i>Cari Produk
                                </label>
                                <select class="form-control" id="pos_search_product"></select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shopping Cart -->
                <div class="card">
                    <div class="card-header bg-light py-3">
                        <h5 class="card-title mb-0">Keranjang Belanja</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive cart-table">
                            <table class="table table-hover" id="cart-table">
                                <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Satuan</th>
                                    <th style="width: 100px;">Qty</th>
                                    <th>Harga</th>
                                    <th>Diskon</th>
                                    <th>Subtotal</th>
                                    <th>Aksi</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="col-lg-4">
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body bg-light border-bottom">
                        <div class="text-muted small mb-1">Total Pembayaran</div>
                        <input type="text" class="form-control-plaintext h2 mb-0 fw-bold"
                               id="pos_final_amount" readonly>
                    </div>

                    <div class="card-body">
                        <!-- Transaction Summary -->
                        <div class="mb-4">
                            <div class="amount-detail">
                                <span>Subtotal</span>
                                <input type="text" class="form-control-plaintext text-end" id="pos_subtotal" readonly>
                            </div>
                            <div class="amount-detail">
                                <span>Pajak</span>
                                <input type="text" class="form-control-plaintext text-end" id="pos_tax_amount" readonly>
                            </div>
                            <div class="amount-detail">
                                <span>Diskon</span>
                                <input type="text" class="form-control-plaintext text-end" id="pos_discount_amount" readonly>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="payment-methods">
                            <select class="form-select form-select-lg mb-3" id="pos_payment_type">
                                <option value="cash">💵 Tunai</option>
                                <option value="transfer">🏦 Transfer</option>
                            </select>

                            <!-- Cash Payment -->
                            <div id="pos_cash_amount_container" class="mb-3">
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="pos_cash_amount"
                                           placeholder="Jumlah Tunai" step="100" min="0">
                                </div>
                            </div>

                            <div id="pos_change_container" class="mb-3">
                                <div class="bg-light rounded p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Kembalian</span>
                                        <input type="text" class="form-control-plaintext text-end h5 mb-0"
                                               id="pos_change" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Transfer Payment -->
                            <!-- Transfer Payment -->
                            <div id="pos_reference_number_container" style="display: none;">
                                <input type="text" class="form-control form-control-lg"
                                       id="pos_reference_number"
                                       placeholder="Nomor Referensi Transfer">
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <div class="d-grid gap-2">
                            <button class="btn btn-warning d-flex align-items-center justify-content-center gap-2"
                                    id="btn-pending">
                                <i class="bx bx-save"></i>
                                <span>Simpan Draft</span>
                            </button>
                            <button class="btn btn-primary btn-lg d-flex align-items-center justify-content-center gap-2"
                                    id="btn-save">
                                <i class="bx bx-check-circle"></i>
                                <span>Selesaikan</span>
                            </button>
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
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0" id="pending-transactions-table">
                        <thead class="bg-light">
                        <tr>
                            <th class="py-3">Faktur</th>
                            <th class="py-3">Tanggal</th>
                            <th class="py-3">Pelanggan</th>
                            <th class="py-3">Total</th>
                            <th class="py-3">Aksi</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notifications -->
@include('components.toast')

<!-- Scripts -->
@include('pos.partials.scripts')

</body>
</html>
