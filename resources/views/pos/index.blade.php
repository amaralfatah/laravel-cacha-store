<!-- resources/views/pos/index.blade.php -->
@extends('layouts.app')

@push('styles')
    @include('pos.partials.styles')
@endpush

@section('content')
    <div class="row">
        <!-- Left Column - Product Input -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Transaksi Penjualan</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>No. Invoice</label>
                            <input type="text" class="form-control" id="invoice_number" value="{{ $invoiceNumber }}"
                                readonly>
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

                    <div class="mb-3">
                        <label>Scan Barcode</label>
                        <input type="text" class="form-control" id="barcode" autofocus>
                    </div>

                    <div class="mb-3">
                        <label>Cari Produk</label>
                        <input type="text" class="form-control" id="search_product">
                        <div id="product_list"></div>
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
                        <label>Subtotal</label>
                        <input type="text" class="form-control" id="subtotal" readonly>
                    </div>
                    <div class="mb-3">
                        <label>Pajak</label>
                        <input type="text" class="form-control" id="tax_amount" readonly>
                    </div>
                    <div class="mb-3">
                        <label>Diskon</label>
                        <input type="text" class="form-control" id="discount_amount" readonly>
                    </div>
                    <div class="mb-3">
                        <label>Total</label>
                        <input type="text" class="form-control" id="final_amount" readonly>
                    </div>
                    <div class="mb-3">
                        <label>Metode Pembayaran</label>
                        <select class="form-select" id="payment_type">
                            <option value="cash">Cash</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                    <div class="mb-3" id="reference_number_container" style="display: none;">
                        <label>Nomor Referensi</label>
                        <input type="text" class="form-control" id="reference_number">
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-warning mb-2" id="btn-pending">Simpan Sebagai Draft</button>
                        <button class="btn btn-primary" id="btn-save">Simpan Transaksi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('pos.partials.scripts')
@endpush
