@extends('layouts.app')

@section('content')

    <x-section-header
        title="Laporan Pergerakan Stok"
    />

    <div class="card mb-4">
        <div class="card-body">
            <!-- Form Filter -->
            <form method="GET" action="{{ route('reports.stock-movement') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <div>
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                   value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div>
                            <label for="end_date" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                   value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div>
                            <label for="product_id" class="form-label">Produk</label>
                            <select class="form-select" id="product_id" name="product_id">
                                <option value="">Semua Produk</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                        {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div>
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Tabel Pergerakan Stok -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Satuan</th>
                        <th>Tipe</th>
                        <th>Jumlah</th>
                        <th>Sisa Stok</th>
                        <th>Referensi</th>
                        <th>Catatan</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($movements as $movement)
                        <tr>
                            <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $movement->productUnit->product->name }}</td>
                            <td>{{ $movement->productUnit->unit->name }}</td>
                            <td>
                                <span
                                    class="badge bg-{{ $movement->type === 'in' ? 'success' : ($movement->type === 'out' ? 'danger' : 'warning') }}">
                                    {{ $movement->type === 'in' ? 'Masuk' : ($movement->type === 'out' ? 'Keluar' : 'Penyesuaian') }}
                                </span>
                            </td>
                            <td>{{ $movement->quantity }}</td>
                            <td>{{ $movement->remaining_stock }}</td>
                            <td>
                                @php
                                    $refTypes = [
                                        'stock_adjustments' => 'Penyesuaian Stok',
                                        'transactions' => 'Transaksi',
                                        'stock_takes' => 'Stok Opname'
                                    ];
                                @endphp
                                {{ $refTypes[$movement->reference_type] ?? $movement->reference_type }}
                            </td>
                            <td>{{ $movement->notes }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada pergerakan stok ditemukan</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
