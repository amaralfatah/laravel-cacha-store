@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')

    <x-section-header title="Laporan Penjualan">
        <x-slot:actions>
            <form id="date-filter-form" class="d-flex gap-3">
                <div class="input-group">
                    <span class="input-group-text">Dari</span>
                    <input type="date" id="start_date" name="start_date" class="form-control"
                           value="{{ request('start_date', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d')) }}">
                </div>
                <div class="input-group">
                    <span class="input-group-text">Sampai</span>
                    <input type="date" id="end_date" name="end_date" class="form-control"
                           value="{{ request('end_date', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                </div>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class='bx bx-filter-alt'></i>
                    <span>Filter</span>
                </button>
            </form>
        </x-slot:actions>
    </x-section-header>

    <div class="row mb-5">
        <x-card-status
            title="Total Penjualan"
            subtitle="Pendapatan dari semua transaksi"
            :value="$totalSales"
            icon="bx-money"
            iconColor="primary"
            columnSize="col-md-4" />

        <x-card-status
            title="Total Transaksi"
            subtitle="Jumlah penjualan berhasil"
            :value="$salesCount"
            icon="bx-cart"
            iconColor="success"
            format="normal"
            columnSize="col-md-4" />

        <x-card-status
            title="Rata-rata Penjualan"
            subtitle="Nilai transaksi rata-rata"
            :value="$salesCount > 0 ? $totalSales / $salesCount : 0"
            icon="bx-trending-up"
            iconColor="info"
            columnSize="col-md-4" />
    </div>

    <!-- Laporan Data -->
    <div class="row mb-5">
        <!-- Kartu Produk Terlaris -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center pb-0 pt-4 px-4">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Produk Terlaris</h5>
                        <small class="text-muted">Produk dengan performa terbaik</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-end">Kuantitas</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($topProducts as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td class="text-end">{{ number_format($product->total_qty, 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        Rp {{ number_format($product->total_amount, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada data tersedia</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center pb-0 pt-4 px-4">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Penjualan Berdasarkan Metode Pembayaran</h5>
                        <small class="text-muted">Distribusi metode pembayaran</small>
                    </div>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center p-4">
                    <div id="paymentTypeChart" style="width: 100%; height: 250px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu Tabel Transaksi -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center p-4">
            <h5 class="card-title mb-0">Daftar Transaksi</h5>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="statusFilterDropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Filter Status
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="statusFilterDropdown">
                    <a class="dropdown-item status-filter" href="#" data-status="">Semua Transaksi</a>
                    <a class="dropdown-item status-filter" href="#" data-status="success">Berhasil</a>
                    <a class="dropdown-item status-filter" href="#" data-status="pending">Tertunda</a>
                    <a class="dropdown-item status-filter" href="#" data-status="failed">Gagal</a>
                    <a class="dropdown-item status-filter" href="#" data-status="returned">Dikembalikan</a>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive text-nowrap">
                <table id="sales-table" class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>Faktur</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Kasir</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    <!-- Data akan diisi oleh DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            // Initialize DataTable
            const salesTable = $('#sales-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('reports.sales') }}",
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.status = $('#status').val();
                    }
                },
                columns: [
                    {data: 'invoice_number', name: 'invoice_number'},
                    {data: 'formatted_date', name: 'invoice_date'},
                    {data: 'customer_name', name: 'customer_id'},
                    {data: 'cashier_name', name: 'cashier_id'},
                    {data: 'formatted_total', name: 'final_amount'},
                    {
                        data: 'status',
                        name: 'status',
                        render: function (data) {
                            let badgeClass = '';

                            switch (data) {
                                case 'success':
                                    badgeClass = 'bg-success';
                                    break;
                                case 'pending':
                                    badgeClass = 'bg-warning';
                                    break;
                                case 'failed':
                                    badgeClass = 'bg-danger';
                                    break;
                                case 'returned':
                                    badgeClass = 'bg-secondary';
                                    break;
                                default:
                                    badgeClass = 'bg-primary';
                            }

                            return `<span class="badge ${badgeClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: function (data) {
                            return `
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="/transactions/${data}">
                                        <i class="bx bx-show me-1"></i> Lihat
                                    </a>
                                    <a class="dropdown-item" href="/transactions/${data}/print">
                                        <i class="bx bx-printer me-1"></i> Cetak
                                    </a>
                                </div>
                            </div>
                            `;
                        }
                    },
                ],
                order: [[1, 'desc']]
            });

            // Status filter dengan input tersembunyi
            let statusInput = $('<input>').attr({
                type: 'hidden',
                id: 'status',
                name: 'status'
            }).appendTo('#date-filter-form');

            // Handler klik filter status
            $('.status-filter').on('click', function (e) {
                e.preventDefault();
                const status = $(this).data('status');
                $('#status').val(status);
                $('#statusFilterDropdown').text($(this).text());
                salesTable.ajax.reload();
            });

            // Form filter tanggal
            $('#date-filter-form').on('submit', function (e) {
                e.preventDefault();
                salesTable.ajax.reload();
            });

            // Grafik Tipe Pembayaran
            $.ajax({
                url: "{{ route('reports.sales.payment-chart') }}",
                method: "GET",
                data: {
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val()
                },
                success: function (response) {
                    if (response.success && response.data) {
                        const chartData = response.data;

                        // Jika tidak ada data, tampilkan pesan kosong
                        if (chartData.length === 0) {
                            $("#paymentTypeChart").html('<div class="text-center py-5 text-muted">Tidak ada data pembayaran tersedia untuk periode yang dipilih</div>');
                            return;
                        }

                        const options = {
                            series: chartData.map(item => item.amount),
                            chart: {
                                type: 'donut',
                                height: 250,
                                fontFamily: 'Public Sans, sans-serif',
                                toolbar: {
                                    show: false,
                                }
                            },
                            labels: chartData.map(item => {
                                const label = item.payment_type === 'cash' ? 'Tunai' : 'Transfer';
                                return `${label} (Rp ${new Intl.NumberFormat('id-ID').format(item.raw_amount)})`;
                            }),
                            colors: ['#696cff', '#03c3ec'],
                            legend: {
                                position: 'bottom',
                                fontSize: '13px',
                                fontWeight: 400,
                                markers: {
                                    width: 12,
                                    height: 12,
                                    radius: 12
                                }
                            },
                            plotOptions: {
                                pie: {
                                    donut: {
                                        size: '65%',
                                        labels: {
                                            show: true,
                                            name: {
                                                show: true,
                                                fontSize: '13px',
                                                fontWeight: 'semibold'
                                            },
                                            value: {
                                                show: true,
                                                fontSize: '22px',
                                                fontWeight: 'semibold',
                                                formatter: function (val) {
                                                    return val + '%';
                                                }
                                            },
                                            total: {
                                                show: true,
                                                label: 'Total',
                                                formatter: function () {
                                                    return '100%';
                                                }
                                            }
                                        }
                                    }
                                }
                            },
                            responsive: [
                                {
                                    breakpoint: 480,
                                    options: {
                                        chart: {
                                            height: 200
                                        },
                                        legend: {
                                            position: 'bottom'
                                        }
                                    }
                                }
                            ]
                        };

                        const chart = new ApexCharts(document.querySelector("#paymentTypeChart"), options);
                        chart.render();
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Gagal memuat data grafik pembayaran:", error);
                    $("#paymentTypeChart").html('<div class="text-center py-5 text-muted">Error memuat data pembayaran</div>');
                }
            });
        });
    </script>
@endpush

