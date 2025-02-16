@extends('layouts.app')

@section('content')
    <x-section-header
        title="Penyesuaian Stok"
        :route="route('stock.adjustments.create')"
        buttonText="Tambah Penyesuaian Stok"
        icon="bx-plus"
    />

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="adjustments-table">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Date</th>
                        @if(auth()->user()->role === 'admin')
                            <th>Store</th>
                        @endif
                        <th>Product</th>
                        <th>Unit</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Notes</th>
                        <th>Created By</th>
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
            let columns = [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'created_at', name: 'created_at'},
                    @if(auth()->user()->role === 'admin')
                {data: 'store_name', name: 'store_name'},
                    @endif
                {data: 'product_name', name: 'product_name'},
                {data: 'unit_name', name: 'unit_name'},
                {data: 'type', name: 'type'},
                {data: 'quantity', name: 'quantity'},
                {data: 'notes', name: 'notes'},
                {data: 'creator_name', name: 'creator_name'}
            ];

            $('#adjustments-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('stock.adjustments.data') }}",
                columns: columns,
                order: [[1, 'desc']],
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>'
                }
            });
        });
    </script>
@endpush
