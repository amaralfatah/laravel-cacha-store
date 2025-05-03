@extends('layouts.app')

@section('content')
    <x-section-header
        title="Manajemen Pajak"
        :route="route('taxes.create')"
        buttonText="Tambah Pajak"
        icon="bx-plus"
    />

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="taxes-table">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Tarif</th>
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
            $('#taxes-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("taxes.index") }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'rate_formatted', name: 'rate' },
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
