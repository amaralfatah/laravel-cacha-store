@extends('layouts.app')

@section('content')
    <x-section-header title="Manajemen Kategori" :route="route('categories.create')" buttonText="Tambah Kategori" icon="bx-plus" />

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="categories-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            @if (auth()->user()->role === 'admin')
                                <th>Toko</th>
                            @endif
                            <th>Kelompok</th>
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
            $('#categories-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('categories.index') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    @if (auth()->user()->role === 'admin')
                        {
                            data: 'store_name',
                            name: 'store_name'
                        },
                    @endif {
                        data: 'group_name',
                        name: 'group_name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'asc']
                ], 
            });
        });
    </script>
@endpush
