@extends('layouts.app')

@section('content')
    <x-section-header title="Detail Produk"/>

    <!-- Product Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Main Content Container -->
                    <div class="d-flex flex-column flex-md-row gap-4">
                        <!-- Product Image -->
                        <div class="text-center">
                            @if($product->images->where('is_primary', true)->first())
                                <img
                                    src="{{ asset('storage/' . $product->images->where('is_primary', true)->first()->image_path) }}"
                                    alt="{{ $product->name }}"
                                    class="d-block rounded object-fit-cover"
                                    style="width: 120px; height: 120px;"/>
                            @else
                                <div
                                    class="d-block rounded bg-label-primary d-flex align-items-center justify-content-center"
                                    style="width: 120px; height: 120px;">
                                    <i class='bx bx-package fs-1'></i>
                                </div>
                            @endif
                        </div>

                        <!-- Product Info & Actions -->
                        <div class="flex-grow-1">
                            <!-- Title & Actions -->
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-3">
                                <!-- Title -->
                                <div>
                                    <h4 class="mb-2">{{ $product->name }}</h4>
                                    <div class="text-muted">
                                        <div class="d-flex flex-wrap gap-3">
                                        <span class="d-inline-flex align-items-center">
                                            <i class='bx bx-store me-1'></i>
                                            {{ $product->store->name }}
                                        </span>
                                            <span class="d-inline-flex align-items-center">
                                            <i class='bx bx-tag me-1'></i>
                                            {{ $product->code }}
                                        </span>
                                            <span class="d-inline-flex align-items-center">
                                            <i class='bx bx-barcode me-1'></i>
                                            {{ $product->barcode }}
                                        </span>
                                        </div>
                                        <div class="mt-2 d-flex align-items-center">
                                            <i class='bx bx-folder me-1'></i>
                                            <span>{{ $product->category->group->name }} / {{ $product->category->name }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="d-flex gap-2">
                                        <button type="button"
                                                class="btn btn-outline-primary d-flex align-items-center"
                                                data-bs-toggle="modal"
                                                data-bs-target="#stockHistoryModal">
                                            <i class='bx bx-history me-1'></i>
                                            <span class="d-none d-sm-inline">History</span>
                                        </button>
                                        <a href="{{ route('products.edit', $product) }}"
                                           class="btn btn-primary d-flex align-items-center">
                                            <i class='bx bx-edit-alt me-1'></i>
                                            <span class="d-none d-sm-inline">Edit</span>
                                        </a>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Hapus Produk</button>
                                        </form>
                                </div>
                            </div>

                            <!-- Status Badges -->
                            <div class="mt-3 d-flex flex-wrap gap-2">
                            <span class="badge bg-label-{{ $product->is_active ? 'success' : 'danger' }}">
                                <i class='bx {{ $product->is_active ? 'bx-check' : 'bx-x' }} me-1'></i>
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                                @if($defaultUnit?->stock <= $defaultUnit?->min_stock)
                                    <span class="badge bg-label-warning">
                                    <i class='bx bx-error me-1'></i>
                                    Low Stock
                                </span>
                                @endif
                                @if($product->featured)
                                    <span class="badge bg-label-info">
                                    <i class='bx bx-star me-1'></i>
                                    Featured
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium d-block mb-1">Stok Saat Ini</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">{{ number_format($defaultUnit?->stock ?? 0) }}</h4>
                                <small class="text-muted">{{ $defaultUnit?->unit->code }}</small>
                            </div>
                        </div>
                        <div class="avatar">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class='bx bx-package'></i>
                        </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium d-block mb-1">Harga Jual</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">Rp {{ number_format($defaultUnit?->selling_price ?? 0) }}</h4>
                            </div>
                        </div>
                        <div class="avatar">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class='bx bx-money'></i>
                        </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium d-block mb-1">Margin</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">{{ number_format($statistics['average_margin'], 1) }}%</h4>
                            </div>
                        </div>
                        <div class="avatar">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class='bx bx-trending-up'></i>
                        </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium d-block mb-1">Total Penjualan</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">{{ number_format($statistics['total_sales']) }}</h4>
                                <small class="text-muted">units</small>
                            </div>
                        </div>
                        <div class="avatar">
                        <span class="avatar-initial rounded bg-label-info">
                            <i class='bx bx-shopping-bag'></i>
                        </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="row">
        <!-- Product Information -->
        <div class="col-xl-4 col-lg-5 col-md-5">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Produk</h5>
                </div>
                <div class="card-body">

                    @if($product->supplier)
                        <div class="mb-3">
                            <label class="fw-medium d-block mb-1">Supplier</label>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class='bx bx-building'></i>
                                </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $product->supplier->name }}</h6>
                                    <small class="text-muted">{{ $product->supplier->code }}</small>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($product->tax)
                        <div class="mb-3">
                            <label class="fw-medium d-block mb-1">Pajak</label>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class='bx bx-receipt'></i>
                                </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $product->tax->name }}</h6>
                                    <small class="text-muted">Rate: {{ $product->tax->rate }}%</small>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($product->discount)
                        <div class="mb-3">
                            <label class="fw-medium d-block mb-1">Diskon</label>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class='bx bx-tag'></i>
                                </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $product->discount->name }}</h6>
                                    <small class="text-muted">
                                        {{ $product->discount->type === 'percentage' ? $product->discount->value . '%' : 'Rp ' . number_format($product->discount->value) }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Product Images -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Gambar</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadImagesModal">
                        <i class='bx bx-plus'></i> Tambah
                    </button>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($product->images as $image)
                            <div class="col-6">
                                <div class="position-relative">
                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                         alt="{{ $image->alt_text }}"
                                         class="d-block rounded w-100 h-100 object-fit-cover"/>
                                    @if($image->is_primary)
                                        <span class="badge bg-primary position-absolute top-0 end-0 m-2">Primary</span>
                                    @endif
                                    <div class="position-absolute bottom-0 start-0 w-100 p-2 bg-dark bg-opacity-50">
                                        <button class="btn btn-sm btn-danger w-100"
                                                onclick="deleteImage({{ $image->id }})">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class='bx bx-images fs-1 text-muted mb-2'></i>
                                <p class="mb-0">Gambar tidak tersedia</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Barcode Section -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Barcode</h5>
                </div>
                <div class="card-body text-center">
                    @if($product->barcode_image && Storage::disk('public')->exists($product->barcode_image))
                        <div class="barcode-print-area mb-3">
                            <img src="{{ Storage::url($product->barcode_image) }}"
                                 alt="Product Barcode"
                                 class="img-fluid mb-2"/>
                            <div class="mt-2">
                                <h6 class="mb-1">{{ $product->name }}</h6>
                                <small class="text-muted">{{ $product->barcode }}</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{ Storage::url($product->barcode_image) }}"
                               class="btn btn-label-primary"
                               download="{{ $product->barcode }}.png">
                                <i class='bx bx-download me-1'></i> Download
                            </a>
                            <button type="button" class="btn btn-label-secondary" onclick="window.print()">
                                <i class='bx bx-printer me-1'></i> Print
                            </button>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class='bx bx-barcode fs-1 text-muted mb-2'></i>
                            <p class="mb-0">Barcode tidak tersedia</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7 col-md-7">
            <!-- Product Units -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Satuan</h5>
                    <a href="{{ route('products.units.create', $product) }}"
                       class="btn btn-sm btn-primary">
                        <i class='bx bx-plus me-1'></i> Tambah
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Unit</th>
                            <th>Konversi</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th>Min Stok</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($product->productUnits as $unit)
                            <tr>
                                <td>
                                    <h6 class="mb-0">{{ $unit->unit->name }}</h6>
                                    <small class="text-muted">{{ $unit->unit->code }}</small>
                                </td>
                                <td>1 = {{ $unit->conversion_factor }}</td>
                                <td>Rp {{ number_format($unit->purchase_price) }}</td>
                                <td>Rp {{ number_format($unit->selling_price) }}</td>
                                <td>
                                    <span
                                        class="badge bg-label-{{ $unit->stock <= $unit->min_stock ? 'danger' : 'success' }}">
                                        {{ number_format($unit->stock) }}
                                    </span>
                                </td>
                                <td>{{ number_format($unit->min_stock) }}</td>
                                <td>
                                    <span class="badge bg-label-{{ $unit->is_default ? 'primary' : 'secondary' }}">
                                        {{ $unit->is_default ? 'Default' : 'Alternative' }}
                                    </span>
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-outline-info" href="javascript:void(0);"
                                       data-bs-toggle="modal"
                                       data-bs-target="#adjustStockModal{{ $unit->id }}">
                                        <i class="bx bx-plus"></i>
                                    </a>
                                    <a class="btn btn-sm btn-outline-warning"
                                       href="{{ route('products.units.edit', [$product, $unit]) }}">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    @if(!$unit->is_default)
                                        <a class="btn btn-sm btn-outline-danger "
                                           href="javascript:void(0);"
                                           onclick="event.preventDefault(); document.getElementById('delete-unit-{{ $unit->id }}').submit();">
                                            <i class="bx bx-trash"></i>
                                        </a>
                                        <form id="delete-unit-{{ $unit->id }}"
                                              action="{{ route('products.units.destroy', [$product, $unit]) }}"
                                              method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </td>
                            </tr>

                            <!-- Stock Adjustment Modal -->
                            <div class="modal fade" id="adjustStockModal{{ $unit->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Adjust Stock - {{ $unit->unit->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('stock.adjustments.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_unit_id" value="{{ $unit->id }}">
                                            <input type="hidden" name="redirect_back" value="1">

                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Adjustment Type</label>
                                                    <select name="type" class="form-select" required>
                                                        <option value="in">Addition (+)</option>
                                                        <option value="out">Reduction (-)</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Quantity</label>
                                                    <div class="input-group">
                                                        <input type="number" name="quantity"
                                                               class="form-control" step="0.01"
                                                               min="0.01" required>
                                                        <span class="input-group-text">{{ $unit->unit->code }}</span>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Notes</label>
                                                    <textarea name="notes" class="form-control" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-label-secondary"
                                                        data-bs-dismiss="modal">Cancel
                                                </button>
                                                <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tiered Pricing -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Harga Bertingkat</h5>
                    <a href="{{ route('products.prices.create', $product) }}"
                       class="btn btn-sm btn-primary">
                        <i class='bx bx-plus me-1'></i> Tambah
                    </a>
                </div>
                <div class="table-responsive">
                    @if($product->productUnits->flatMap->prices->isNotEmpty())
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Unit</th>
                                <th>Min. Quantity</th>
                                <th>Price</th>
                                <th>Margin</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($product->productUnits as $productUnit)
                                @foreach($productUnit->prices->sortBy('min_quantity') as $price)
                                    <tr>
                                        <td>
                                            <h6 class="mb-0">{{ $productUnit->unit->name }}</h6>
                                            <small class="text-muted">{{ $productUnit->unit->code }}</small>
                                        </td>
                                        <td>{{ number_format($price->min_quantity) }}</td>
                                        <td>Rp {{ number_format($price->price) }}</td>
                                        <td>
                                            @php
                                                $margin = (($price->price - $productUnit->purchase_price) / $productUnit->purchase_price) * 100;
                                            @endphp
                                            <span class="badge bg-label-{{ $margin >= 20 ? 'success' : 'warning' }}">
                                                {{ number_format($margin, 1) }}%
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('products.prices.edit', [$product, $price]) }}"
                                               class="btn btn-outline-warning"><i class="bx bx-edit-alt"></i></a>
                                            <a class="btn btn-outline-danger"
                                               href="javascript:void(0);"
                                               onclick="event.preventDefault(); document.getElementById('delete-price-{{ $price->id }}').submit();">
                                                <i class="bx bx-trash"></i>
                                            </a>
                                            <form id="delete-price-{{ $price->id }}"
                                                  action="{{ route('products.prices.destroy', [$product, $price]) }}"
                                                  method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-5">
                            <i class='bx bx-tag fs-1 text-muted mb-2'></i>
                            <p class="mb-0">No tiered prices available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Transactions</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Invoice</th>
                            <th>Unit</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($product->transactionItems as $item)
                            <tr>
                                <td>{{ $item->transaction->invoice_date->format('d/m/Y H:i') }}</td>
                                <td>{{ $item->transaction->invoice_number }}</td>
                                <td>{{ $item->unit->code }}</td>
                                <td>{{ number_format($item->quantity) }}</td>
                                <td>Rp {{ number_format($item->unit_price) }}</td>
                                <td>Rp {{ number_format($item->subtotal) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-3">
                                    <i class='bx bx-shopping-bag fs-1 text-muted mb-2'></i>
                                    <p class="mb-0">No transactions available</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock History Modal -->
    <div class="modal fade" id="stockHistoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Stock History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Unit</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Remaining Stock</th>
                                <th>Notes</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($product->stockHistories as $history)
                                <tr>
                                    <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $history->productUnit->unit->code }}</td>
                                    <td>
                                        <span
                                            class="badge bg-label-{{ $history->type === 'in' ? 'success' : ($history->type === 'out' ? 'danger' : 'warning') }}">
                                            {{ $history->type === 'in' ? 'In' : ($history->type === 'out' ? 'Out' : 'Adjustment') }}
                                        </span>
                                    </td>
                                    <td>{{ number_format(abs($history->quantity)) }}</td>
                                    <td>{{ number_format($history->remaining_stock) }}</td>
                                    <td>{{ $history->notes }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Images Modal -->
    <div class="modal fade" id="uploadImagesModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Product Images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('products.images.store', $product) }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Images</label>
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*" required>
                            <div class="form-text">You can select multiple images. Maximum size: 2MB per image.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            @media print {
                body * {
                    visibility: hidden;
                }

                .barcode-print-area, .barcode-print-area * {
                    visibility: visible;
                }

                .barcode-print-area {
                    position: absolute;
                    left: 50%;
                    top: 50%;
                    transform: translate(-50%, -50%);
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function deleteImage(imageId) {
                if (confirm('Are you sure you want to delete this image?')) {
                    axios.delete(`/products/images/${imageId}`)
                        .then(response => {
                            window.location.reload();
                        })
                        .catch(error => {
                            alert('Failed to delete image');
                        });
                }
            }
        </script>
    @endpush

@endsection
