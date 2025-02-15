@extends('layouts.app')


@section('content')
    <x-section-header
        title="Stock Opname"
        :route="route('stock-takes.create')"
        buttonText="Tambah Stock Opname"
        icon="bx-plus"
    />

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="stock-takes-table">
                    <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jml Barang</th>
                        <th>Status</th>
                        <th>Dibuat Oleh</th>
                        <th class="text-end">Aksi</th>
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
            $('#stock-takes-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('stock-takes.data') }}",
                    error: function (xhr, error, thrown) {
                        console.error('DataTables error:', error);
                        // Tampilkan pesan error yang user-friendly
                        $('#stock-takes-table_processing').html('Terjadi kesalahan saat memuat data');
                    }
                },
                columns: [
                    {data: 'date', name: 'date'},
                    {data: 'items_count', name: 'items_count'},
                    {data: 'status', name: 'status'},
                    {data: 'creator_name', name: 'creator_name'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-end'
                    }
                ],
            });
        });
    </script>
@endpush
