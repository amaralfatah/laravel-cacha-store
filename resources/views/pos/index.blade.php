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
            <div class="col-8">
                <!-- Actions -->
                <div class="mb-3 d-flex gap-2">
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary">Home</a>
                    <button type="button" class="btn btn-danger" id="btn-clear-cart">Hapus</button>
                    <button type="button" class="btn btn-warning" id="btn-show-pending">Pending</button>
                </div>

                <!-- Transaction Info -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4">
                                <label class="form-label" for="pos_invoice_number">Nomor Faktur</label>
                                <input type="text" class="form-control" id="pos_invoice_number" value="{{ $invoiceNumber }}" readonly>
                            </div>
                            <div class="col-4">
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
                            <div class="col-4">
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
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label" for="pos_barcode">Scan Barcode</label>
                                <input type="text" class="form-control" id="pos_barcode" autofocus>
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="pos_search_product">Cari Produk</label>
                                <select class="form-select" id="pos_search_product"></select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shopping Cart -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title m-0">Keranjang Belanja</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table" id="cart-table">
                            <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Satuan</th>
                                <th>Qty</th>
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

            <!-- Payment Section -->
            <div class="col-4">
                <div class="card">
                    <!-- Total Payment -->
                    <div class="card-body bg-primary text-white">
                        <div class="small">Total Pembayaran</div>
                        <input type="text" class="form-control-plaintext text-white fw-bold fs-3"
                               id="pos_final_amount" readonly>
                    </div>

                    <div class="card-body">
                        <!-- Summary -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span>Subtotal</span>
                                <input type="text" class="form-control-plaintext text-end w-auto" id="pos_subtotal" readonly>
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

                        <!-- Payment -->
                        <select class="form-select mb-3" id="pos_payment_type">
                            <option value="cash">Tunai</option>
                            <option value="transfer">Transfer</option>
                        </select>

                        <div id="pos_cash_amount_container" class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="pos_cash_amount" placeholder="Jumlah Tunai">
                            </div>
                        </div>

                        <div id="pos_change_container" class="mb-3">
                            <div class="bg-light p-2">
                                <div class="d-flex justify-content-between">
                                    <span>Kembalian</span>
                                    <input type="text" class="form-control-plaintext text-end" id="pos_change" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-grid gap-2 mt-3">
                            <button class="btn btn-warning" id="btn-pending">Pending</button>
                            <button class="btn btn-primary" id="btn-save">Selesaikan</button>
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
