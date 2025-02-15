@extends('layouts.app')

@section('content')
    <x-section-header
        title="Laporan Produk"
    />

    <!-- Kartu Ringkasan -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Produk</h6>
                    <h4 class="mb-0">{{ $summary['total_products'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">Stok Menipis</h6>
                    <h4 class="mb-0">{{ $summary['low_stock'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Tersedia</h6>
                    <h4 class="mb-0">{{ $summary['available'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="card-title">Stok Habis</h6>
                    <h4 class="mb-0">{{ $summary['out_of_stock'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Tabel Inventaris -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="inventory-table">
                    <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Total Stok</th>
                        <th>Satuan</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let table = $('#inventory-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('reports.inventory') }}",
                columns: [
                    {data: 'code', name: 'code'},
                    {data: 'name', name: 'name'},
                    {data: 'category.name', name: 'category.name'},
                    {data: 'total_stock', name: 'total_stock'},
                    {data: 'units', name: 'units', orderable: false, searchable: false},
                    {data: 'status', name: 'status'}
                ],
                order: [[1, 'asc']],
                pageLength: 25
            });
        });
    </script>
@endpush
