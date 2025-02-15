@extends('layouts.app')

@section('content')
    <x-section-header
        title="Laporan Pergerakan Stok"
    />

    <div class="card mb-4">
        <div class="card-body">
            <!-- Form Filter -->
            <form id="filter-form" method="GET" action="{{ route('reports.stock-movement') }}" class="mb-4">
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
                <table class="table table-bordered table-striped" id="stock-movement-table">
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
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let table = $('#stock-movement-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('reports.stock-movement') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.product_id = $('#product_id').val();
                    }
                },
                columns: [
                    {data: 'created_at', name: 'created_at'},
                    {data: 'productUnit.product.name', name: 'productUnit.product.name'},
                    {data: 'productUnit.unit.name', name: 'productUnit.unit.name'},
                    {data: 'type', name: 'type'},
                    {data: 'quantity', name: 'quantity'},
                    {data: 'remaining_stock', name: 'remaining_stock'},
                    {data: 'reference_type', name: 'reference_type'},
                    {data: 'notes', name: 'notes'}
                ],
                order: [[0, 'desc']],
                pageLength: 25
            });

            // Handle filter form submission
            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                table.draw();
            });
        });
    </script>
@endpush
