@extends('layouts.app')

@section('content')
    <x-section-header
        title="Manajemen Diskon"
        :route="route('discounts.create')"
        buttonText="Tambah Diskon"
        icon="bx-plus"
    />

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="discounts-table">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Tipe</th>
                        <th>Nilai</th>
                        @if(auth()->user()->role === 'admin')
                            <th>Toko</th>
                        @endif
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#discounts-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("discounts.index") }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'type_formatted', name: 'type' },
                    { data: 'value_formatted', name: 'value' },
                    @if(auth()->user()->role === 'admin')
                    { data: 'store_name', name: 'store_name' },
                    @endif
                    { data: 'status', name: 'status' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[1, 'asc']], 
            });
        });
    </script>
@endpush
