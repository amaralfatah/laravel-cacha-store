@extends('layouts.app')

@section('content')
    <!-- Bagian Header Produk -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">{{ $product->name }}</h2>
            <p class="text-muted mb-0">
                <i class="bi bi-folder me-2"></i>{{ $product->category->name }}
                <span class="ms-3">
                    <i class="bi bi-upc me-2"></i>{{ $product->barcode }}
                </span>
                <span class="ms-3">
                    <i class="bi bi-upc me-2"></i>{{ $product->code }}
                </span>
                <span class="ms-3">
                    <i class="bi bi-upc me-2"></i>{{ $product->store->name }}
                </span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil-square me-1"></i>Edit
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Kartu Info Produk -->
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informasi Produk</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Detail Produk -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="small text-muted d-block">Harga Unit Default</label>
                                @php
                                    $defaultUnit = $product->productUnits->where('is_default', true)->first();
                                @endphp
                                <h3 class="text-primary mb-0">
                                    Rp {{ number_format($defaultUnit?->selling_price ?? 0, 2) }}
                                </h3>
                                <small class="text-muted">
                                    Modal: Rp {{ number_format($defaultUnit?->purchase_price ?? 0, 2) }}
                                </small>
                            </div>

                            <div class="mb-3">
                                <label class="small text-muted d-block">Jumlah Stok</label>
                                <h4 class="mb-0 {{ ($defaultUnit?->stock ?? 0) < 10 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($defaultUnit?->stock ?? 0) }}
                                    <small class="text-muted">{{ $defaultUnit?->unit->code }}</small>
                                </h4>
                            </div>

                            @if($product->tax)
                                <div class="mb-3">
                                    <label class="small text-muted d-block">Pajak</label>
                                    <span class="badge bg-info">{{ $product->tax->name }} ({{ $product->tax->rate }}%)</span>
                                </div>
                            @endif

                            @if($product->discount)
                                <div class="mb-3">
                                    <label class="small text-muted d-block">Diskon Aktif</label>
                                    <span class="badge bg-warning text-dark">
                                    {{ $product->discount->name }} ({{ $product->discount->value }}{{ $product->discount->type === 'percentage' ? '%' : ' Rp' }})
                                </span>
                                </div>
                            @endif
                        </div>

                        <!-- Bagian Barcode -->
                        <div class="col-md-6 text-center">
                            @if ($product->barcode_image && Storage::disk('public')->exists($product->barcode_image))
                                <img src="{{ Storage::url($product->barcode_image) }}"
                                     alt="Barcode Produk"
                                     class="img-fluid mb-2">
                                <div class="d-grid gap-2">
                                    <a href="{{ Storage::url($product->barcode_image) }}"
                                       download="{{ $product->barcode }}.png"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-download me-1"></i>Unduh Barcode
                                    </a>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            onclick="window.print()">
                                        <i class="bi bi-printer me-1"></i>Cetak Barcode
                                    </button>
                                </div>
                            @else
                                <div class="text-muted">
                                    <i class="bi bi-upc fs-1 d-block mb-2"></i>
                                    Gambar barcode tidak tersedia
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Harga Unit -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Harga Per Unit</h5>
                    <a href="{{ route('products.units.create', $product) }}" class="btn btn-primary btn-sm">Tambah Unit</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                            <tr>
                                <th>Unit</th>
                                <th>Konversi</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Stok</th>
                                <th>Default</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($product->productUnits as $unit)
                                <tr>
                                    <td>
                                        <strong>{{ $unit->unit->name }}</strong>
                                        <small class="text-muted d-block">{{ $unit->unit->code }}</small>
                                    </td>
                                    <td>1 = {{ $unit->conversion_factor }}</td>
                                    <td>Rp {{ number_format($unit->purchase_price, 2) }}</td>
                                    <td>Rp {{ number_format($unit->selling_price, 2) }}</td>
                                    <td>{{ number_format($unit->stock) }}</td>
                                    <td>
                                        <span class="badge {{ $unit->is_default ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $unit->is_default ? 'Ya' : 'Tidak' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('products.units.edit', [$product, $unit]) }}"
                                           class="btn btn-sm btn-primary">Edit</a>

                                        @if (!$unit->is_default)
                                            <form action="{{ route('products.units.destroy', [$product, $unit]) }}"
                                                  method="POST"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Anda yakin?')">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Harga Bertingkat -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Harga Bertingkat</h5>
                    <a href="{{route('products.prices.create', $product)}}" type="button"
                       class="btn btn-primary btn-sm">
                        Tambah Harga
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                            <tr>
                                <th>Unit</th>
                                <th>Jumlah Minimum</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($product->productUnits as $productUnit)
                                @foreach ($productUnit->prices as $price)
                                    <tr>
                                        <td>
                                            <strong>{{ $productUnit->unit->name }}</strong>
                                            <small class="text-muted d-block">{{ $productUnit->unit->code }}</small>
                                        </td>
                                        <td>{{ number_format($price->min_quantity) }}</td>
                                        <td>Rp {{ number_format($price->price, 2) }}</td>
                                        <td>
                                            <a href="{{ route('products.prices.edit', [$product, $price]) }}"
                                               class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route('products.prices.destroy', [$product, $price]) }}"
                                                  method="POST"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Anda yakin?')">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
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
        /* Print styles for barcode */
        @media print {
            body * {
                visibility: hidden;
            }

            .barcode-print-area, .barcode-print-area * {
                visibility: visible;
            }

            .barcode-print-area {
                position: absolute;
                left: 0;
                top: 0;
            }
        }
    </style>
@endpush
