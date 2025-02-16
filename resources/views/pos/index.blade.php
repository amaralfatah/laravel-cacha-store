<!-- resources/views/pos/index.blade.php -->
@extends('layouts.app')

@push('styles')
    @include('pos.partials.styles')
@endpush

@section('content')
    <div class="row">
        <!-- Left Column - Product Input -->
        <div class="col-md-8">
            <div class="mb-3">
                <button type="button" class="btn btn-primary" id="btn-show-pending">
                    Transaksi Pending
                </button>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4>Transaksi Penjualan</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>No. Invoice</label>
                            <input type="text" class="form-control" id="invoice_number" value="{{ $invoiceNumber }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label>Customer</label>
                            <select class="form-select" id="customer_id">
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ $customer->id === 1 ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="pos_invoice_number">No. Invoice</label>
                            <input type="text" class="form-control" id="pos_invoice_number" name="invoice_number" value="{{ $invoiceNumber }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="pos_store_id">Store</label>
                            @if(Auth::user()->role === 'admin')
                                <select class="form-select" id="pos_store_id" name="store_id">
                                    @foreach ($stores as $store)
                                        <option value="{{ $store->id }}" {{ $selectedStore && $selectedStore->id === $store->id ? 'selected' : '' }}>
                                            {{ $store->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" class="form-control" id="pos_store_name" value="{{ Auth::user()->store->name }}" readonly>
                                <input type="hidden" id="pos_store_id" name="store_id" value="{{ Auth::user()->store_id }}">
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label for="pos_customer_id">Customer</label>
                            <select class="form-select" id="pos_customer_id" name="customer_id">
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ $customer->id === 1 ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="pos_barcode">Scan Barcode</label>
                        <input type="text" class="form-control" id="pos_barcode" name="barcode" autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="pos_search_product">Cari Produk</label>
                        <input type="text" class="form-control" id="pos_search_product" name="search_product">
                        <div id="pos_product_list"></div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped" id="cart-table">
                            <thead>
                                <tr>
                                    <th style="min-width: 150px;">Produk</th>
                                    <th style="min-width: 150px;">Unit</th>
                                    <th>Quantity</th>
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

        <!-- Right Column - Payment -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>Pembayaran</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="pos_subtotal">Subtotal</label>
                        <input type="text" class="form-control" id="pos_subtotal" name="subtotal" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="pos_tax_amount">Pajak</label>
                        <input type="text" class="form-control" id="pos_tax_amount" name="tax_amount" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="pos_discount_amount">Diskon</label>
                        <input type="text" class="form-control" id="pos_discount_amount" name="discount_amount" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="pos_final_amount">Total</label>
                        <input type="text" class="form-control" id="pos_final_amount" name="final_amount" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="pos_payment_type">Metode Pembayaran</label>
                        <select class="form-select" id="pos_payment_type" name="payment_type">
                            <option value="cash">Cash</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>

                    <div class="mb-3" id="pos_reference_number_container" style="display: none;">
                        <label for="pos_reference_number">Nomor Referensi</label>
                        <input type="text" class="form-control" id="pos_reference_number" name="reference_number">
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-warning mb-2" id="btn-pending">Simpan Sebagai Draft</button>
                        <button class="btn btn-primary" id="btn-save">Simpan Transaksi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pendingTransactionsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Daftar Transaksi Pending</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="pending-transactions-table">
                            <thead>
                            <tr>
                                <th>No. Invoice</th>
                                <th>Tanggal</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('pos.partials.scripts')
@endpush
