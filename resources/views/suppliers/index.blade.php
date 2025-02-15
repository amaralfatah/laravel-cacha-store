@extends('layouts.app')

@section('content')

    <x-section-header
        title="Manajemen Pemasok"
        :route="route('suppliers.create')"
        buttonText="Tambah Pemasok"
        icon="bx-plus"
    />

    <div class="card">
        <div class="card-body">
            <table class="table table-hover" id="datatable">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Telpon</th>
                    <th>Aksi</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            const table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('suppliers.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'code', name: 'code'},
                    {data: 'name', name: 'name'},
                    {data: 'phone', name: 'phone'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                    }
                ],
                order: [[1, 'asc']],
            });
        })
    </script>
@endpush
