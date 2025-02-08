<!-- resources/views/inventory/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row mb-3">
            <div class="col">
                <h2>Manajemen Inventory</h2>
                <a href="{{ route('inventory.create') }}" class="btn btn-primary">Tambah Stok</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Unit</th>
                            <th>Stok</th>
                            <th>Minimum Stok</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventories as $inventory)
                            <tr>
                                <td>{{ $inventory->product->name }}</td>
                                <td>{{ $inventory->unit->name }}</td>
                                <td>{{ $inventory->quantity }}</td>
                                <td>{{ $inventory->min_stock }}</td>
                                <td>
                                    @if ($inventory->quantity <= $inventory->min_stock)
                                        <span class="badge bg-danger">Stok Minimum</span>
                                    @else
                                        <span class="badge bg-success">Stok Aman</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('inventory.edit', $inventory) }}"
                                        class="btn btn-sm btn-warning">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Notifikasi Stok Modal -->
    <div class="modal fade" id="lowStockModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Notifikasi Stok Minimum</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="lowStockList">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function checkLowStock() {
            fetch('{{ route('inventory.check-low-stock') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        let html = '<ul class="list-group">';
                        data.forEach(item => {
                            html +=
                                `<li class="list-group-item">Produk ${item.product.name} (${item.unit.name}) memiliki stok dibawah minimum</li>`;
                        });
                        html += '</ul>';
                        document.getElementById('lowStockList').innerHTML = html;
                        new bootstrap.Modal(document.getElementById('lowStockModal')).show();
                    }
                });
        }

        // Check low stock setiap 5 menit
        setInterval(checkLowStock, 300000);
        // Check saat halaman dimuat
        document.addEventListener('DOMContentLoaded', checkLowStock);
    </script>
@endpush
