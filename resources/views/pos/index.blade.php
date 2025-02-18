<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>
        @php
            $segments = request()->segments();
            $title = !empty($segments) ? ucwords(str_replace(['-', '_'], ' ', end($segments))) : '';
        @endphp
        {{ $title ? $title . ' | ' : '' }}Toko Cacha</title>
    <meta name="description" content="" />
    @include('pos.partials.styles')

</head>

<body>
<div class="content-wrapper pos-container">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- Left Column - Product Input & Cart -->
            <div class="col-lg-8">
                <!-- Header Actions -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex action-buttons">
                        <a href="{{ url('/dashboard') }}" class="btn btn-success me-2">
                            <i class="bx bx-arrow-back me-1"></i> Back to Dashboard
                        </a>
                        <button type="button" class="btn btn-danger" id="btn-clear-cart">
                            <i class="bx bx-trash me-1"></i> Clear Cart
                        </button>
                        <button type="button" class="btn btn-warning" id="btn-show-pending">
                            <i class="bx bx-time me-1"></i> Pending
                        </button>
                    </div>
                    <button type="button" class="btn btn-primary btn-icon" id="btn-fullscreen">
                        <i class="bx bx-expand"></i>
                    </button>
                </div>

                <!-- Transaction Info -->
                <div class="product-search-container mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="pos_invoice_number">Invoice Number</label>
                            <input type="text" class="form-control" id="pos_invoice_number" value="{{ $invoiceNumber }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="pos_store_id">Store</label>
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
                            <label class="form-label" for="pos_customer_id">Customer</label>
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

                <!-- Product Search -->
                <div class="product-search-container">
                    <div class="mb-3">
                        <label class="form-label" for="pos_barcode">Scan Barcode</label>
                        <input type="text" class="form-control" id="pos_barcode" autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="pos_search_product">Search Product</label>
                        <input type="text" class="form-control" id="pos_search_product">
                        <div id="pos_product_list"></div>
                    </div>
                </div>

                <!-- Cart -->
                <div class="cart-container">
                    <div class="table-responsive cart-table">
                        <table class="table" id="cart-table">
                            <thead>
                            <tr>
                                <th>Product</th>
                                <th>Unit</th>
                                <th style="width: 100px;">Qty</th>
                                <th>Price</th>
                                <th>Discount</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column - Payment -->
            <div class="col-lg-4">
                <div class="payment-container">
                    <h4 class="mb-4">Payment Details</h4>

                    <div class="mb-3">
                        <label class="form-label" for="pos_subtotal">Subtotal</label>
                        <input type="text" class="form-control amount-field" id="pos_subtotal" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="pos_tax_amount">Tax</label>
                        <input type="text" class="form-control amount-field" id="pos_tax_amount" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="pos_discount_amount">Discount</label>
                        <input type="text" class="form-control amount-field" id="pos_discount_amount" readonly>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="pos_final_amount">Total Amount</label>
                        <input type="text" class="form-control amount-field" id="pos_final_amount" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="pos_payment_type">Payment Method</label>
                        <select class="form-select" id="pos_payment_type">
                            <option value="cash">Cash</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>

                    <div class="mb-4" id="pos_reference_number_container" style="display: none;">
                        <label class="form-label" for="pos_reference_number">Reference Number</label>
                        <input type="text" class="form-control" id="pos_reference_number">
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-warning" id="btn-pending">
                            <i class="bx bx-save me-1"></i> Save as Draft
                        </button>
                        <button class="btn btn-primary" id="btn-save">
                            <i class="bx bx-check-circle me-1"></i> Complete Transaction
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Transactions Modal -->
<div class="modal fade" id="pendingTransactionsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pending Transactions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table" id="pending-transactions-table">
                        <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Action</th>
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
