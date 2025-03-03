{{-- resources/views/purchases/show.blade.php --}}
@extends('layouts.app')

@section('content')
    <!-- Screen-only header with actions -->
    <div class="d-print-none mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h4>
                <i class='bx bx-file me-1'></i> Detail Pembelian
            </h4>
            <div>
                <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary me-1">
                    <i class='bx bx-arrow-back'></i> Kembali
                </a>
                <button onclick="printPO()" class="btn btn-primary me-1">
                    <i class='bx bx-printer'></i> Print
                </button>
                @if($purchase->status === 'pending')
                    <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-warning">
                        <i class='bx bx-edit'></i> Edit
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Printable Content -->
    <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
            <!-- Screen-only navigation/header will be hidden when printing -->
            <div class="d-none d-print-block mb-4">
                <div class="text-center">
                    <h3 class="fw-bold mb-0">Daftar Pembelian</h3>
                    <p class="text-muted">#{{ $purchase->invoice_number }}</p>
                </div>
            </div>

            <!-- Main content section -->
            <div class="printable-content">
                <!-- Purchase and Supplier Information -->
                <div class="row mb-4">
                    <div class="col-md-6 col-print-6">
                        <div class="d-flex align-items-center mb-4">
                            <i class='bx bx-info-circle me-2 text-primary'></i>
                            <h5 class="mb-0">Informasi Pembelian</h5>
                        </div>
                        <table class="table table-sm">
                            <tr>
                                <td width="40%" class="border-0 ps-0"><strong>No. Invoice</strong></td>
                                <td class="border-0">{{ $purchase->invoice_number }}</td>
                            </tr>
                            <tr>
                                <td class="border-0 ps-0"><strong>Tanggal Pembelian</strong></td>
                                <td class="border-0">{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td class="border-0 ps-0"><strong>Status</strong></td>
                                <td class="border-0">
                                <span class="badge bg-{{ $purchase->status == 'pending' ? 'warning' : ($purchase->status == 'completed' ? 'success' : 'danger') }}">
                                    {{ ucfirst($purchase->status) }}
                                </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="border-0 ps-0"><strong>Tipe Pembayaran</strong></td>
                                <td class="border-0">{{ ucfirst($purchase->payment_type) }}</td>
                            </tr>
                            @if($purchase->reference_number)
                                <tr>
                                    <td class="border-0 ps-0"><strong>No. Referensi</strong></td>
                                    <td class="border-0">{{ $purchase->reference_number }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6 col-print-6">
                        <div class="d-flex align-items-center mb-4">
                            <i class='bx bx-user me-2 text-primary'></i>
                            <h5 class="mb-0">Informasi Supplier</h5>
                        </div>
                        <table class="table table-sm">
                            <tr>
                                <td width="40%" class="border-0 ps-0"><strong>Nama</strong></td>
                                <td class="border-0">{{ $purchase->supplier->name }}</td>
                            </tr>
                            <tr>
                                <td class="border-0 ps-0"><strong>Telpon</strong></td>
                                <td class="border-0">{{ $purchase->supplier->phone }}</td>
                            </tr>
                            @if(isset($purchase->supplier->address))
                                <tr>
                                    <td class="border-0 ps-0"><strong>Alamat</strong></td>
                                    <td class="border-0">{{ $purchase->supplier->address }}</td>
                                </tr>
                            @endif
                            @if(isset($purchase->supplier->email))
                                <tr>
                                    <td class="border-0 ps-0"><strong>Email</strong></td>
                                    <td class="border-0">{{ $purchase->supplier->email }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-4">
                        <i class='bx bx-package me-2 text-primary'></i>
                        <h5 class="mb-0">Daftar Barang</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>PRODUK</th>
                                <th>UNIT</th>
                                <th class="text-end">QTY</th>
                                <th class="text-end">HARGA UNIT</th>
                                <th class="text-end">DISKON</th>
                                <th class="text-end">SUBTOTAL</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($purchase->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->unit->name }}</td>
                                    <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end">{{ number_format($item->discount, 2) }}</td>
                                    <td class="text-end">{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="6" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold">{{ number_format($purchase->total_amount, 2) }}</td>
                            </tr>
                            @if($purchase->tax_amount > 0)
                                <tr>
                                    <td colspan="6" class="text-end">PAJAK</td>
                                    <td class="text-end">{{ number_format($purchase->tax_amount, 2) }}</td>
                                </tr>
                            @endif
                            @if($purchase->discount_amount > 0)
                                <tr>
                                    <td colspan="6" class="text-end">DISKON</td>
                                    <td class="text-end">{{ number_format($purchase->discount_amount, 2) }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td colspan="6" class="text-end fw-bold">TOTAL PEMBELIAN</td>
                                <td class="text-end fw-bold">{{ number_format($purchase->final_amount, 2) }}</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Notes Section -->
                @if($purchase->notes)
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class='bx bx-note me-2 text-primary'></i>
                            <h5 class="mb-0">Notes</h5>
                        </div>
                        <p class="mb-0">{{ $purchase->notes }}</p>
                    </div>
                @endif

                <!-- Signature Section - Only visible when printing -->
                <div class="row mt-5 mb-4 d-none d-print-flex">
                    <div class="col-4 text-center">
                        <p>Prepared by:</p>
                        <div style="height: 60px;"></div>
                        <p class="mb-0">__________________</p>
                        <p class="mb-0">Admin</p>
                    </div>
                    <div class="col-4 text-center">
                        <p>Approved by:</p>
                        <div style="height: 60px;"></div>
                        <p class="mb-0">__________________</p>
                        <p class="mb-0">Manager</p>
                    </div>
                    <div class="col-4 text-center">
                        <p>Received by:</p>
                        <div style="height: 60px;"></div>
                        <p class="mb-0">__________________</p>
                        <p class="mb-0">Supplier</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons - Only visible on screen -->
            @if($purchase->status === 'pending')
                <div class="mt-4 text-end d-print-none">
                    <form action="{{ route('purchases.update', $purchase) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="btn btn-success"
                                onclick="return confirm('Are you sure you want to complete this purchase?')">
                            <i class='bx bx-check-circle'></i> Complete Purchase
                        </button>
                    </form>
                    <form action="{{ route('purchases.update', $purchase) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to cancel this purchase?')">
                            <i class='bx bx-x-circle'></i> Cancel Purchase
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
        <style>
            @media print {
                /* Hide elements that shouldn't print */
                .app-header,
                .app-sidebar,
                .footer,
                .d-print-none,
                .btn,
                nav,
                footer {
                    display: none !important;
                }

                /* Page formatting */
                @page {
                    size: A4;
                    margin: 10mm;
                }

                body {
                    font-size: 12pt;
                    background-color: white !important;
                    padding: 0 !important;
                    margin: 0 !important;
                }

                /* Ensure content uses full page */
                .container,
                .content-wrapper,
                .content-inner,
                .content,
                .card {
                    padding: 0 !important;
                    margin: 0 !important;
                    border: none !important;
                    box-shadow: none !important;
                    width: 100% !important;
                    max-width: none !important;
                }

                /* Column sizes for printing */
                .col-print-6 {
                    width: 50%;
                    float: left;
                }

                /* Table formatting */
                .table {
                    width: 100%;
                    border-collapse: collapse;
                }

                .table th,
                .table td {
                    border: 1px solid #ddd;
                }

                /* Ensure badges print correctly */
                .badge {
                    border: 1px solid #000;
                    color: #000 !important;
                    background-color: white !important;
                    padding: 2px 5px;
                }

                .badge.bg-success {
                    border-color: #28a745;
                }

                .badge.bg-warning {
                    border-color: #ffc107;
                }

                .badge.bg-danger {
                    border-color: #dc3545;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function printPO() {
                window.print();
            }
        </script>
    @endpush
@endsection
