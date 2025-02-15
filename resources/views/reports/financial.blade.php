@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
    <x-section-header title="Laporan Keuangan"/>

    <!-- Form Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <form id="filter-form" method="GET" action="{{ route('reports.financial') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label small mb-1">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                   value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label small mb-1">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                   value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('reports.financial') }}" class="btn btn-secondary">
                                <i class="fas fa-sync-alt me-1"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Total Pemasukan</div>
                            <div class="text-lg fw-bold">
                                Rp {{ number_format($summary['total_in'], 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <i class="fas fa-money-bill-wave fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Total Pengeluaran</div>
                            <div class="text-lg fw-bold">
                                Rp {{ number_format($summary['total_out'], 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <i class="fas fa-hand-holding-usd fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Total Bersih</div>
                            <div class="text-lg fw-bold">
                                Rp {{ number_format($summary['net_amount'], 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <i class="fas fa-chart-line fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Saldo Saat Ini</div>
                            <div class="text-lg fw-bold">
                                Rp {{ number_format($summary['current_balance'], 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <i class="fas fa-wallet fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <!-- Tabel Transaksi -->
            <div class="table-responsive">
                <table class="table table-hover" id="financialTable">
                    <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Jumlah</th>
                        <th>Sumber</th>
                        <th>Saldo Sebelumnya</th>
                        <th>Saldo Saat Ini</th>
                        <th>Dibuat Oleh</th>
                        <th class="w-100">Catatan</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            let table = $('#financialTable').DataTable({
                ajax: {
                    url: "{{ route('reports.financial') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [
                    {data: 'created_at', name: 'created_at'},
                    {data: 'type', name: 'type'},
                    {data: 'amount', name: 'amount'},
                    {data: 'source_type', name: 'source_type'},
                    {data: 'previous_balance', name: 'previous_balance'},
                    {data: 'current_balance', name: 'current_balance'},
                    {data: 'createdBy.name', name: 'createdBy.name'},
                    {data: 'notes', name: 'notes'}
                ],
                order: [[0, 'desc']]
            });

            // Handle filter form submission
            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                table.draw();

                $.get($(this).attr('action'), $(this).serialize(), function(response) {
                    updateSummaryCards(response.summary);
                });
            });
        });

        function updateSummaryCards(summary) {
            $('.total-in').text('Rp ' + number_format(summary.total_in));
            $('.total-out').text('Rp ' + number_format(summary.total_out));
            $('.net-amount').text('Rp ' + number_format(summary.net_amount));
            $('.current-balance').text('Rp ' + number_format(summary.current_balance));
        }

        function number_format(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }
    </script>
@endpush
