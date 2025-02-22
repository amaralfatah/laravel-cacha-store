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
                <span>Total Transaksi: <span id="today_count">0</span></span>
            </div>
            <div class="ticker-divider"></div>
            <div class="ticker-item">
                <i class='bx bx-money'></i>
                <span>Total: <span id="today_total">Rp 0</span></span>
            </div>
            <div class="ticker-divider"></div>
            <div class="ticker-item">
                <i class='bx bx-wallet'></i>
                <span>Tunai: <span id="cash_total">Rp 0</span></span>
            </div>
            <div class="ticker-divider"></div>
            <div class="ticker-item">
                <i class='bx bx-credit-card'></i>
                <span>Transfer: <span id="transfer_total">Rp 0</span></span>
            </div>
            <div class="ticker-divider"></div>
            <div class="ticker-item">
                <i class='bx bx-line-chart'></i>
                <span>Rata-rata: <span id="average_transaction">Rp 0</span></span>
            </div>
            <div class="ticker-divider"></div>
            <div class="ticker-item">
                <i class='bx bx-time'></i>
                <span>Update: <span id="last_update">-</span></span>
            </div>
        </div>
    </div>

    <div class="container-xxl flex-grow-1 mt-3">
        <div class="row">
            <!-- Kolom Kiri - Input Produk & Keranjang -->
            <div class="col-lg-8">

                {{--action header--}}
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="action-header d-flex justify-content-between align-items-center">
                            <div class="d-flex gap-2">
                                <a href="{{ url('/dashboard') }}" class="btn btn-outline-primary">
                                    <i class="bx bx-arrow-back me-1"></i> Dashboard
                                </a>
                                <button type="button" class="btn btn-outline-danger" id="btn-clear-cart">
                                    <i class="bx bx-trash me-1"></i> Clear Cart
                                </button>
                                <button type="button" class="btn btn-outline-warning" id="btn-show-pending">
                                    <i class="bx bx-time me-1"></i> Pending
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Transaksi -->
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

                <!-- Pencarian Produk -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="pos_barcode">Pindai Barcode [F1]</label>
                            <input type="text" class="form-control" id="pos_barcode" autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="pos_search_product">Cari Produk</label>
                            <select class="form-control" id="pos_search_product"></select>
                        </div>
                    </div>
                </div>

                <!-- Keranjang Belanja -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive cart-table">
                            <table class="table" id="cart-table">
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

            <!-- Kolom Kanan - Pembayaran -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Rincian Pembayaran</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column gap-2">
                            <div>
                                <label class="form-label" for="pos_subtotal">Subtotal</label>
                                <input type="text" class="form-control amount-field" id="pos_subtotal" readonly>
                            </div>

                            <div>
                                <label class="form-label" for="pos_tax_amount">Pajak</label>
                                <input type="text" class="form-control amount-field" id="pos_tax_amount" readonly>
                            </div>

                            <div>
                                <label class="form-label" for="pos_discount_amount">Diskon</label>
                                <input type="text" class="form-control amount-field" id="pos_discount_amount" readonly>
                            </div>

                            <div>
                                <label class="form-label" for="pos_final_amount">Total</label>
                                <input type="text" class="form-control amount-field" id="pos_final_amount" readonly>
                            </div>

                            <div id="pos_cash_amount_container">
                                <label class="form-label" for="pos_cash_amount">Uang Tunai</label>
                                <input type="number" class="form-control amount-field" id="pos_cash_amount" step="100" min="0">
                            </div>

                            <div id="pos_change_container">
                                <label class="form-label" for="pos_change">Kembalian</label>
                                <input type="text" class="form-control amount-field" id="pos_change" readonly>
                            </div>

                            <div>
                                <label class="form-label" for="pos_payment_type">Metode Pembayaran</label>
                                <select class="form-select" id="pos_payment_type">
                                    <option value="cash">Tunai</option>
                                    <option value="transfer">Transfer</option>
                                </select>
                            </div>

                            <div id="pos_reference_number_container" style="display: none;">
                                <label class="form-label" for="pos_reference_number">Nomor Referensi</label>
                                <input type="text" class="form-control" id="pos_reference_number">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex flex-column gap-2">
                            <button class="btn btn-warning" id="btn-pending">
                                <i class="bx bx-save me-1"></i> Simpan Sebagai Pending
                            </button>
                            <button class="btn btn-primary" id="btn-save">
                                <i class="bx bx-check-circle me-1"></i> Selesaikan Transaksi
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
            <div class="modal-header">
                <h5 class="modal-title">Transaksi Pending</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table" id="pending-transactions-table">
                        <thead>
                        <tr>
                            <th>Faktur</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('components.toast')
@include('pos.partials.scripts')

</body>
</html>
