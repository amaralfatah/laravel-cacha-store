@extends('layouts.app')

@section('content')
    <!-- Product Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">{{ $product->name }}</h2>
            <div class="text-muted small">
                <span><i class="bi bi-building me-1"></i>{{ $product->store->name }}</span>
                <span class="ms-3"><i class="bi bi-tag me-1"></i>{{ $product->code }}</span>
                <span class="ms-3"><i class="bi bi-upc me-1"></i>{{ $product->barcode }}</span>
                <span class="ms-3"><i class="bi bi-folder me-1"></i>{{ $product->category->group->name }} / {{ $product->category->name }}</span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-info" data-bs-toggle="modal"
                    data-bs-target="#stockHistoryModal">
                <i class="bi bi-clock-history me-1"></i>Riwayat Stok
            </button>
            <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil-square me-1"></i>Edit
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Quick Overview Card -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Ringkasan Produk</h5>
                </div>
                <div class="card-body">
                    @php
                        $defaultUnit = $product->productUnits->where('is_default', true)->first();
                    @endphp
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <span class="text-muted small d-block">Harga Jual Default</span>
                                <h3 class="text-primary mb-0">
                                    Rp {{ number_format($defaultUnit?->selling_price ?? 0, 0, ',', '.') }}
                                </h3>
                                <small class="text-muted">
                                    Per {{ $defaultUnit?->unit->name ?? '-' }}
                                </small>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted small d-block">Modal</span>
                                <h4 class="mb-0">
                                    Rp {{ number_format($defaultUnit?->purchase_price ?? 0, 0, ',', '.') }}
                                </h4>
                                <small class="text-success">
                                    Margin: {{ $defaultUnit ? number_format((($defaultUnit->selling_price - $defaultUnit->purchase_price) / $defaultUnit->purchase_price) * 100, 1) : 0 }}
                                    %
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <span class="text-muted small d-block">Stok Saat Ini</span>
                                <h3 class="mb-0 {{ ($defaultUnit?->stock ?? 0) <= ($defaultUnit?->min_stock ?? 0) ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($defaultUnit?->stock ?? 0) }}
                                    <small>{{ $defaultUnit?->unit->code }}</small>
                                </h3>
                                <small class="text-muted">
                                    Min. Stok: {{ number_format($defaultUnit?->min_stock ?? 0) }}
                                </small>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted small d-block">Status Produk</span>
                                <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }} me-2">
                                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                                @if($defaultUnit?->stock <= $defaultUnit?->min_stock)
                                    <span class="badge bg-warning text-dark">Stok Rendah</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supplier & Tax Info -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informasi Tambahan</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <span class="text-muted small d-block">Supplier</span>
                                @if($product->supplier)
                                    <h6 class="mb-0">{{ $product->supplier->name }}</h6>
                                    <small class="text-muted">
                                        Kode: {{ $product->supplier->code }}<br>
                                        Telp: {{ $product->supplier->phone }}
                                    </small>
                                @else
                                    <p class="text-muted mb-0">Tidak ada supplier</p>
                                @endif
                            </div>

                            @if($product->category)
                                <div class="mb-3">
                                    <span class="text-muted small d-block">Kategori & Grup</span>
                                    <h6 class="mb-0">{{ $product->category->name }}</h6>
                                    <small class="text-muted">
                                        Grup: {{ $product->category->group->name }}<br>
                                        Kode: {{ $product->category->code }}
                                    </small>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if($product->tax)
                                <div class="mb-3">
                                    <span class="text-muted small d-block">Pajak</span>
                                    <h6 class="mb-0">{{ $product->tax->name }}</h6>
                                    <small class="text-muted">Rate: {{ $product->tax->rate }}%</small>
                                </div>
                            @endif

                            @if($product->discount)
                                <div class="mb-3">
                                    <span class="text-muted small d-block">Diskon Aktif</span>
                                    <h6 class="mb-0">{{ $product->discount->name }}</h6>
                                    <small class="text-muted">
                                        {{ $product->discount->value }}{{ $product->discount->type === 'percentage' ? '%' : ' Rp' }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barcode Section -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Barcode</h5>
                </div>
                <div class="card-body text-center">
                    @if ($product->barcode_image && Storage::disk('public')->exists($product->barcode_image))
                        <div class="barcode-print-area mb-3">
                            <img src="{{ Storage::url($product->barcode_image) }}"
                                 alt="Barcode"
                                 class="img-fluid">
                            <div class="mt-2">
                                <strong>{{ $product->name }}</strong><br>
                                <small>{{ $product->barcode }}</small>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="{{ Storage::url($product->barcode_image) }}"
                               download="{{ $product->barcode }}.png"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download me-1"></i>Unduh Barcode
                            </a>
                            <button onclick="window.print()"
                                    class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-printer me-1"></i>Cetak Barcode
                            </button>
                        </div>
                    @else
                        <div class="text-muted py-4">
                            <i class="bi bi-upc fs-1 d-block mb-2"></i>
                            Barcode tidak tersedia
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Transaksi Terakhir</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Invoice</th>
                                <th>Unit</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($product->transactionItems()->latest()->take(5)->get() as $item)
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
                                    <td colspan="6" class="text-center text-muted">
                                        Belum ada transaksi
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Units & Stock -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Unit & Stok</h5>
                    <a href="{{ route('products.units.create', $product) }}"
                       class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Unit
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                            <tr>
                                <th>Unit</th>
                                <th>Konversi</th>
                                <th>Harga Modal</th>
                                <th>Harga Jual</th>
                                <th>Stok</th>
                                <th>Min. Stok</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($product->productUnits as $unit)
                                <tr>
                                    <td>
                                        <strong>{{ $unit->unit->name }}</strong>
                                        <small class="text-muted d-block">{{ $unit->unit->code }}</small>
                                    </td>
                                    <td>1 = {{ $unit->conversion_factor }}</td>
                                    <td>Rp {{ number_format($unit->purchase_price, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($unit->selling_price, 0, ',', '.') }}</td>
                                    <td>
                                            <span
                                                class="badge {{ $unit->stock <= $unit->min_stock ? 'bg-danger' : 'bg-success' }} px-3 py-2">
                                                {{ number_format($unit->stock) }}
                                            </span>
                                    </td>
                                    <td>{{ number_format($unit->min_stock) }}</td>
                                    <td>
                                            <span class="badge {{ $unit->is_default ? 'bg-primary' : 'bg-secondary' }}">
                                                {{ $unit->is_default ? 'Default' : 'Alternative' }}
                                            </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#adjustStockModal{{ $unit->id }}">
                                                Lihat
                                            </button>
                                            <a href="{{ route('products.units.edit', [$product, $unit]) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                Edit
                                            </a>
                                            @if(!$unit->is_default)
                                                <form action="{{ route('products.units.destroy', [$product, $unit]) }}"
                                                      method="POST"
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Hapus unit ini?')">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                        <!-- Modal Stock Adjustment di show.blade.php -->
                                        <div class="modal fade" id="adjustStockModal{{ $unit->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Penyesuaian Stok - {{ $unit->unit->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('stock.adjustments.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="product_unit_id" value="{{ $unit->id }}">
                                                        @if(auth()->user()->role === 'admin')
                                                            <input type="hidden" name="store_id" value="{{ $product->store_id }}">
                                                        @endif
                                                        <!-- Tambahkan input hidden untuk redirect back -->
                                                        <input type="hidden" name="redirect_back" value="1">

                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Tipe Penyesuaian</label>
                                                                <select name="type" class="form-select" required>
                                                                    <option value="in">Penambahan (+)</option>
                                                                    <option value="out">Pengurangan (-)</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Jumlah</label>
                                                                <div class="input-group">
                                                                    <input type="number"
                                                                           name="quantity"
                                                                           class="form-control"
                                                                           step="0.01"
                                                                           min="0.01"
                                                                           required>
                                                                    <span class="input-group-text">{{ $unit->unit->code }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Catatan (Opsional)</label>
                                                                <textarea name="notes"
                                                                          class="form-control"
                                                                          rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button"
                                                                    class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit"
                                                                    class="btn btn-primary">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tiered Pricing -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Harga Bertingkat</h5>
                    <a href="{{ route('products.prices.create', $product) }}"
                       class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Harga
                    </a>
                </div>
                <div class="card-body">
                    @if($product->productUnits->flatMap->prices->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                <tr>
                                    <th>Unit</th>
                                    <th>Min. Quantity</th>
                                    <th>Harga</th>
                                    <th>Margin</th>
                                    <th>Aksi</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($product->productUnits as $productUnit)
                                    @foreach($productUnit->prices->sortBy('min_quantity') as $price)
                                        <tr>
                                            <td>
                                                <strong>{{ $productUnit->unit->name }}</strong>
                                                <small class="text-muted d-block">{{ $productUnit->unit->code }}</small>
                                            </td>
                                            <td>{{ number_format($price->min_quantity) }}</td>
                                            <td>Rp {{ number_format($price->price, 0, ',', '.') }}</td>
                                            <td>
                                                @php
                                                    $margin = (($price->price - $productUnit->purchase_price) / $productUnit->purchase_price) * 100;
                                                @endphp
                                                <span
                                                    class="badge {{ $margin >= 20 ? 'bg-success' : 'bg-warning text-dark' }}">
                                                        {{ number_format($margin, 1) }}%
                                                    </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('products.prices.edit', [$product, $price]) }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form
                                                        action="{{ route('products.prices.destroy', [$product, $price]) }}"
                                                        method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('Hapus harga ini?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-tag fs-1 d-block mb-2"></i>
                            Belum ada harga bertingkat
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Stock History Modal -->
    <div class="modal fade" id="stockHistoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Riwayat Stok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Unit</th>
                                <th>Tipe</th>
                                <th>Jumlah</th>
                                <th>Sisa Stok</th>
                                <th>Catatan</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($product->stockHistories()->latest()->take(20)->get() as $history)
                                <tr>
                                    <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $history->productUnit->unit->code }}</td>
                                    <td>
                                            <span
                                                class="badge bg-{{ $history->type === 'in' ? 'success' : ($history->type === 'out' ? 'danger' : 'warning text-dark') }}">
                                                {{ $history->type === 'in' ? 'Masuk' : ($history->type === 'out' ? 'Keluar' : 'Penyesuaian') }}
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
