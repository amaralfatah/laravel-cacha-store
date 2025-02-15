@extends('layouts.app')

@section('content')
    <x-section-header
        title="Daftar Transaksi"
    />

    <div class="card mb-4">
        <div class="card-body">
            <div class="filter-row">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status Transaksi</label>
                        <select id="status-filter" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending">Draft</option>
                            <option value="success">Selesai</option>
                            <option value="failed">Gagal</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Periode</label>
                        <select id="period-filter" class="form-select">
                            <option value="">Semua Periode</option>
                            <option value="today">Hari Ini</option>
                            <option value="yesterday">Kemarin</option>
                            <option value="this_week">Minggu Ini</option>
                            <option value="this_month">Bulan Ini</option>
                            <option value="last_month">Bulan Lalu</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button id="reset-filter" class="btn btn-secondary">
                            Reset Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="transactions-table">
                    <thead>
                    <tr>
                        <th>No. Invoice</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Total</th>
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
        document.addEventListener('DOMContentLoaded', function() {
            const table = $('#transactions-table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: {
                    url: "{{ route('transactions.index') }}",
                    data: function(d) {
                        d.status = $('#status-filter').val();
                        d.period = $('#period-filter').val();
                    }
                },
                columns: [
                    {
                        data: 'invoice_number',
                        name: 'invoice_number',
                        width: '120px'
                    },
                    {
                        data: 'invoice_date_formatted',
                        name: 'invoice_date',
                        width: '150px'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer.name',
                        width: '200px'
                    },
                    {
                        data: 'final_amount_formatted',
                        name: 'final_amount',
                        width: '120px',
                        className: 'text-end'
                    },
                    {
                        data: 'status_formatted',
                        name: 'status',
                        width: '100px'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '100px'
                    }
                ],
                order: [[1, 'desc']],
            });

            // Event listener untuk filter
            $('#status-filter, #period-filter').change(function() {
                table.draw();
            });

            // Reset filter
            $('#reset-filter').click(function() {
                $('#status-filter, #period-filter').val('');
                table.draw();
            });

            // Tambahkan tooltip untuk cell yang terpotong
            $('#transactions-table').on('mouseenter', 'td', function() {
                if(this.offsetWidth < this.scrollWidth) {
                    $(this).attr('title', $(this).text());
                }
            });
        });
    </script>
@endpush
