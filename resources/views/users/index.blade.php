@extends('layouts.app')

@section('content')

    <x-section-header
        title="Manajemen Pengguna"
        :route="route('users.create')"
        buttonText="Tambah User"
        icon="bx-plus"
    />

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="users-table">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        @if(auth()->user()->role === 'admin')
                            <th>Toko</th>
                        @endif
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
        $(function () {
            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("users.index") }}',
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'role', name: 'role'},
                        @if(auth()->user()->role === 'admin')
                    {
                        data: 'store_name', name: 'store.name'
                    },
                        @endif
                    {
                        data: 'actions', name: 'actions', orderable: false, searchable: false
                    }
                ],
            });
        });
    </script>
@endpush
