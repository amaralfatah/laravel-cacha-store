@extends('layouts.app')

@section('content')
    <x-section-header title="Detail Produk"/>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row gap-4">
                        <!-- Product Image -->
                        @if($product->featured == true)
                            @if($product->images->where('is_primary', true)->first())
                                <div class="flex-shrink-0 text-center mb-3 mb-md-0">
                                    <img
                                        src="{{ asset('storage/' . $product->images->where('is_primary', true)->first()->image_path) }}"
                                        alt="{{ $product->name }}"
                                        class="rounded shadow-sm object-fit-cover"
                                        style="width: 120px; height: 120px;"/>
                                </div>
                            @else
                                <div
                                    class="flex-shrink-0 rounded bg-label-primary d-flex align-items-center justify-content-center mb-3 mb-md-0 shadow-sm"
                                    style="width: 120px; height: 120px;">
                                    <i class='bx bx-package fs-1'></i>
                                </div>
                            @endif
                        @endif

                        <!-- Main Product Info - Simplified -->
                        <div class="flex-grow-1">
                            <!-- Product Header Section -->
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-3">
                                <h4 class="fw-bold text-primary mb-2 mb-md-0">{{ $product->name }}</h4>

                                <!-- Action Buttons -->
                                <div class="d-flex gap-2">
                                    <a href="{{ route('products.edit', $product) }}"
                                       class="btn btn-primary">
                                        <span>Edit</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Status Badges - Moved up for better visibility -->
                            <!-- Status Badges - Moved up for better visibility -->
                            <div class="mb-3 d-flex flex-wrap gap-2">
    <span class="badge bg-label-{{ $product->is_active ? 'success' : 'danger' }} rounded-pill px-2 py-1">
        {{ $product->is_active ? 'Active' : 'Inactive' }}
    </span>

                                @if($defaultUnit?->stock <= $defaultUnit?->min_stock)
                                    <span class="badge bg-label-warning rounded-pill px-2 py-1">
            Low Stock
        </span>
                                @endif

                                @if($product->featured)
                                    <span class="badge bg-label-info rounded-pill px-2 py-1">
            Featured
        </span>
                                @endif

                                @if($product->tax)
                                    <span class="badge bg-label-primary rounded-pill px-2 py-1">
            Tax: {{ $product->tax->rate }}%
        </span>
                                @endif

                                @if($product->discount)
                                    <span class="badge bg-label-secondary rounded-pill px-2 py-1">
            Discount: {{ $product->discount->type === 'percentage' ? $product->discount->value . '%' : 'Rp ' . number_format($product->discount->value) }}
        </span>
                                @endif
                            </div>

                            <!-- Simplified Product Info - Only essential details -->
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <small class="text-muted">Code</small>
                                                <div>{{ $product->code }}</div>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center">
                                            <div>
                                                <small class="text-muted">Barcode</small>
                                                <div>{{ $product->barcode }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <small class="text-muted">Group</small>
                                                <div>{{ $product->category->group->name }}</div>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center">
                                            <div>
                                                <small class="text-muted">Category</small>
                                                <div>{{ $product->category->name }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <small class="text-muted">Current Stock</small>
                                                <div>
                                                    <strong>{{ number_format($defaultUnit?->stock ?? 0) }} {{ $defaultUnit?->unit->code }}</strong>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center">
                                            <div>
                                                <small class="text-muted">Selling Price</small>
                                                <div>
                                                    <strong>Rp {{ number_format($defaultUnit?->selling_price ?? 0) }}</strong>
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
    </div>

    <!-- Tab Layout Content -->

    <div class="col-md-12">
        <ul class="nav nav-pills flex-column flex-md-row mb-3">
            <li class="nav-item">
                <a class="nav-link active" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#tab-units">
                    <i class='bx bx-package me-1'></i> Satuan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#tab-pricing">
                    <i class='bx bx-tag me-1'></i> Harga Bertingkat
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#tab-transactions">
                    <i class='bx bx-shopping-bag me-1'></i> Transaksi
                </a>
            </li>
            @if($product->featured)
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#tab-images">
                        <i class='bx bx-images me-1'></i> Gambar
                    </a>
                </li>
            @endif
            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#tab-barcode">
                    <i class='bx bx-barcode me-1'></i> Barcode
                </a>
            </li>
        </ul>
        <!-- Tab content sections -->
        <div class="tab-content p-0">
            <!-- Units Tab -->
            <div class="tab-pane fade show active" id="tab-units" role="tabpanel">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                        <h5 class="card-title mb-0 text-primary">Satuan</h5>
                        <a href="{{ route('products.units.create', $product) }}"
                           class="btn btn-sm btn-primary">Tambah
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th class="border-0 px-4 py-3">Unit</th>
                                    <th class="border-0 px-4 py-3">Konversi</th>
                                    <th class="border-0 px-4 py-3">Harga Beli</th>
                                    <th class="border-0 px-4 py-3">Harga Jual</th>
                                    <th class="border-0 px-4 py-3">Stok</th>
                                    <th class="border-0 px-4 py-3">Min Stok</th>
                                    <th class="border-0 px-4 py-3">Status</th>
                                    <th class="border-0 px-4 py-3">Aksi</th>
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
                                            <span
                                                class="badge bg-label-{{ $unit->is_default ? 'primary' : 'secondary' }}">
                                                {{ $unit->is_default ? 'Default' : 'Alternative' }}
                                            </span>
                                        </td>
                                        <td>
{{--                                            <a class="btn btn-sm btn-outline-info" href="javascript:void(0);"--}}
{{--                                               data-bs-toggle="modal"--}}
{{--                                               data-bs-target="#adjustStockModal{{ $unit->id }}">--}}
{{--                                                <i class="bx bx-plus"></i>--}}
{{--                                            </a>--}}
                                            <a class="btn btn-sm btn-warning"
                                               href="{{ route('products.units.edit', [$product, $unit]) }}">
                                                Edit
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
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header border-0">
                                                    <h5 class="modal-title">Adjust Stock - {{ $unit->unit->name }}</h5>
                                                    <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('stock.adjustments.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_unit_id" value="{{ $unit->id }}">
                                                    <input type="hidden" name="redirect_back" value="1">

                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Adjustment Type</label>
                                                            <select name="type" class="form-select form-select-sm"
                                                                    required>
                                                                <option value="in">Addition (+)</option>
                                                                <option value="out">Reduction (-)</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Quantity</label>
                                                            <div class="input-group input-group-sm">
                                                                <input type="number" name="quantity"
                                                                       class="form-control" step="0.01" min="0.01"
                                                                       required>
                                                                <span
                                                                    class="input-group-text">{{ $unit->unit->code }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Notes</label>
                                                            <textarea name="notes" class="form-control form-control-sm"
                                                                      rows="3"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0">
                                                        <button type="button" class="btn btn-sm btn-light"
                                                                data-bs-dismiss="modal">Cancel
                                                        </button>
                                                        <button type="submit" class="btn btn-sm btn-primary px-3">Save
                                                        </button>
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
                </div>
            </div>

            <!-- Tiered Pricing Tab -->
            <div class="tab-pane fade" id="tab-pricing" role="tabpanel">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                        <h5 class="card-title mb-0 text-primary">Harga Bertingkat</h5>
                        <a href="{{ route('products.prices.create', $product) }}"
                           class="btn btn-sm btn-primary rounded-pill">
                            <i class='bx bx-plus'></i> Tambah
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th class="border-0 px-4 py-3">Unit</th>
                                    <th class="border-0 px-4 py-3">Min. Quantity</th>
                                    <th class="border-0 px-4 py-3">Price</th>
                                    <th class="border-0 px-4 py-3">Margin</th>
                                    <th class="border-0 px-4 py-3 text-end">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($product->productUnits->flatMap->prices->sortBy('min_quantity') as $price)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="fw-medium">{{ $price->productUnit->unit->name }}</div>
                                            <div class="text-muted small">{{ $price->productUnit->unit->code }}</div>
                                        </td>
                                        <td class="px-4 py-3">{{ number_format($price->min_quantity) }}</td>
                                        <td class="px-4 py-3">Rp {{ number_format($price->price) }}</td>
                                        <td class="px-4 py-3">
                                            @php
                                                $margin = (($price->price - $price->productUnit->purchase_price) / $price->productUnit->purchase_price) * 100;
                                            @endphp
                                            <span
                                                class="badge rounded-pill bg-{{ $margin >= 20 ? 'success' : 'warning' }} bg-opacity-10 text-{{ $margin >= 20 ? 'success' : 'warning' }} px-2 py-1">
                                    {{ number_format($margin, 1) }}%
                                </span>
                                        </td>
                                        <td class="px-4 py-3 text-end">
                                            <div class="btn-group">
                                                <a href="{{ route('products.prices.edit', [$product, $price]) }}"
                                                   class="btn btn-sm btn-icon" title="Edit">
                                                    <i class="bx bx-edit-alt text-warning"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-icon"
                                                        onclick="event.preventDefault(); document.getElementById('delete-price-{{ $price->id }}').submit();"
                                                        title="Delete">
                                                    <i class="bx bx-trash text-danger"></i>
                                                </button>
                                                <form id="delete-price-{{ $price->id }}"
                                                      action="{{ route('products.prices.destroy', [$product, $price]) }}"
                                                      method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class='bx bx-tag fs-1 text-muted mb-2'></i>
                                            <p class="mb-0">No tiered prices available</p>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Tab -->
            <div class="tab-pane fade" id="tab-transactions" role="tabpanel">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                        <h5 class="card-title mb-0 text-primary">Recent Transactions</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th class="border-0 px-4 py-3">Date</th>
                                    <th class="border-0 px-4 py-3">Invoice</th>
                                    <th class="border-0 px-4 py-3">Unit</th>
                                    <th class="border-0 px-4 py-3">Quantity</th>
                                    <th class="border-0 px-4 py-3">Unit Price</th>
                                    <th class="border-0 px-4 py-3">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($product->transactionItems as $item)
                                    <tr>
                                        <td class="px-4 py-3">{{ $item->transaction->invoice_date->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-3">{{ $item->transaction->invoice_number }}</td>
                                        <td class="px-4 py-3">{{ $item->unit->code }}</td>
                                        <td class="px-4 py-3">{{ number_format($item->quantity) }}</td>
                                        <td class="px-4 py-3">Rp {{ number_format($item->unit_price) }}</td>
                                        <td class="px-4 py-3">Rp {{ number_format($item->subtotal) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
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


            <!-- Images Tab -->
            @if($product->featured)
                <div class="tab-pane fade" id="tab-images" role="tabpanel">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Gambar</h5>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#uploadImagesModal">
                                <i class='bx bx-plus'></i> Tambah
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @forelse($product->images as $image)
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <div class="position-relative">
                                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                                 alt="{{ $image->alt_text }}"
                                                 class="d-block rounded w-100 h-100 object-fit-cover"/>
                                            @if($image->is_primary)
                                                <span
                                                    class="badge bg-primary position-absolute top-0 end-0 m-2">Primary</span>
                                            @endif
                                            <div
                                                class="position-absolute bottom-0 start-0 w-100 p-2 bg-dark bg-opacity-50">
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
                </div>
            @endif

            <!-- Barcode Tab -->
            <div class="tab-pane fade" id="tab-barcode" role="tabpanel">
                <div class="card mb-4">
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
        </div>
    </div>

    <!-- Delete Product Button -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-9">
                    <h5 class="text-danger">Hapus Produk</h5>
                    <p class="mb-0">Tindakan ini akan menghapus produk secara permanen dan tidak dapat dikembalikan.</p>
                </div>
                <div class="col-md-3 d-flex align-items-center justify-content-end">
                    <form action="{{ route('products.destroy', $product) }}" method="POST" id="deleteProductForm">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete()">
                            Hapus Produk
                        </button>
                    </form>
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

    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Konfirmasi Hapus Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="bx bx-error-circle text-danger" style="font-size: 6rem;"></i>
                    </div>
                    <p class="fw-bold text-center fs-5 mb-3">Anda yakin ingin menghapus produk ini?</p>
                    <p class="text-center mb-0">Produk: <strong>{{ $product->name }}</strong></p>
                    <p class="text-center text-secondary">Penghapusan akan menghilangkan semua data terkait produk ini,
                        termasuk riwayat stok, unit, dan gambar.</p>

                    <div class="alert alert-warning mt-3">
                        <div class="d-flex">
                            <i class="bx bx-info-circle me-2 mt-1"></i>
                            <div>
                                <strong>Perhatian:</strong> Pastikan tidak ada transaksi aktif yang terkait dengan
                                produk ini. Penghapusan tidak dapat dibatalkan.
                            </div>
                        </div>
                    </div>

                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="confirmDeleteCheck">
                        <label class="form-check-label" for="confirmDeleteCheck">
                            Saya mengerti dan ingin menghapus produk ini
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton" disabled>Hapus Produk</button>
                </div>
            </div>
        </div>
    </div>

@endsection

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

        function confirmDelete() {
            // Tampilkan modal konfirmasi
            const modal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
            modal.show();

            // Aktifkan/nonaktifkan tombol konfirmasi berdasarkan checkbox
            const checkbox = document.getElementById('confirmDeleteCheck');
            const confirmButton = document.getElementById('confirmDeleteButton');

            checkbox.addEventListener('change', function () {
                confirmButton.disabled = !this.checked;
            });

            // Event listener untuk tombol konfirmasi
            confirmButton.addEventListener('click', function () {
                if (checkbox.checked) {
                    document.getElementById('deleteProductForm').submit();
                }
            });
        }
    </script>
@endpush
