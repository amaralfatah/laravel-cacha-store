@extends('layouts.app')

@section('content')
    <x-section-header title="Laporan Penjualan"/>

    <!-- Form Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="filter-form" method="GET" action="{{ route('reports.sales') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div>
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                   value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div>
                            <label for="end_date" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                   value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
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

    <!-- Kartu Ringkasan -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Penjualan</h6>
                    <h4 class="mb-0">Rp {{ number_format($summary['total_sales'], 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Jumlah Transaksi</h6>
                    <h4 class="mb-0">{{ $summary['total_transactions'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Rata-rata Transaksi</h6>
                    <h4 class="mb-0">Rp {{ number_format($summary['average_transaction'], 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Item Terjual</h6>
                    <h4 class="mb-0">{{ $summary['total_items'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Tabel Penjualan -->
            <div class="table-responsive">
                <table class="table table-hover" id="sales-table">
                    <thead>
                    <tr>
                        <th>No. Faktur</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Kasir</th>
                        <th>Jumlah Item</th>
                        <th>Total</th>
                        <th>Diskon</th>
                        <th>Pajak</th>
                        <th>Total Akhir</th>
                        <th>Pembayaran</th>
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
            let table = $('#sales-table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: {
                    url: "{{ route('reports.sales') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [
                    {data: 'invoice_number', name: 'invoice_number'},
                    {data: 'invoice_date', name: 'invoice_date'},
                    {data: 'customer.name', name: 'customer.name'},
                    {data: 'user.name', name: 'user.name'},
                    {data: 'total_items', name: 'total_items'},
                    {data: 'total_amount', name: 'total_amount'},
                    {data: 'discount_amount', name: 'discount_amount'},
                    {data: 'tax_amount', name: 'tax_amount'},
                    {data: 'final_amount', name: 'final_amount'},
                    {data: 'payment_type', name: 'payment_type'}
                ],
                order: [[1, 'desc']],
                pageLength: 25
            });

            // Handle filter form submission
            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                table.draw();

                // Update summary cards via AJAX
                $.get($(this).attr('action'), $(this).serialize(), function(response) {
                    // Update summary cards with new data
                    updateSummaryCards(response.summary);
                });
            });
        });

        function updateSummaryCards(summary) {
            // Update summary cards with new data
            $('.total-sales').text('Rp ' + number_format(summary.total_sales));
            $('.total-transactions').text(summary.total_transactions);
            $('.average-transaction').text('Rp ' + number_format(summary.average_transaction));
            $('.total-items').text(summary.total_items);
        }

        function number_format(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }
    </script>
@endpush
