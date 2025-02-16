@extends('layouts.app')

@section('content')

    <x-section-header
        title="Manajemen Kelompok"
        :route="route('groups.create')"
        buttonText="Tambah Kelompok"
        icon="bx-plus"
    />

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="groups-table">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama</th>
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
            $('#groups-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("groups.index") }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'code', name: 'code' },
                    { data: 'name', name: 'name' },
                        @if(auth()->user()->role === 'admin')
                    { data: 'store_name', name: 'store_name' },  // Ubah dari store.name menjadi store_name
                        @endif
                    { data: 'status', name: 'status' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
            });
        });
    </script>
@endpush
