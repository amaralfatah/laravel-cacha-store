@extends('layouts.app')

@section('title', 'Laporan Pembelian')

@section('content')
    <x-section-header title="Laporan Pembelian">
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

    <!-- Summary Cards -->
    <!-- Using the original card-status component without modifications -->

    <div class="row mb-4">
        <x-card-status
            title="Total Pembelian"
            subtitle="Seluruh pembelian yang sudah selesai"
            :value="$totalPurchases"
            icon="bx-shopping-bag"
            iconColor="primary"
            columnSize="col-md-4" />

        <x-card-status
            title="Pembelian Tertunda"
            subtitle="Pembelian yang belum selesai"
            :value="$pendingPurchasesCount"
            icon="bx-time"
            iconColor="warning"
            format="normal"
            columnSize="col-md-4" />

        <x-card-status
            title="Jumlah Supplier"
            subtitle="Total supplier aktif"
            :value="$suppliers->count()"
            icon="bx-building"
            iconColor="info"
            format="normal"
            columnSize="col-md-4" />
    </div>

    <!-- Charts -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center p-4">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Tren Pembelian</h5>
                        <small class="text-muted">Riwayat pembelian per periode</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="purchaseChart" style="width: 100%; height: 300px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center p-4">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Metode Pembayaran</h5>
                        <small class="text-muted">Distribusi metode pembayaran</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="paymentMethodChart" style="width: 100%; height: 250px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchases Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center p-4">
            <h5 class="card-title mb-0">Riwayat Pembelian</h5>
            <div class="d-flex gap-2">
                <input type="hidden" id="supplier_id" name="supplier_id">
                <input type="hidden" id="status" name="status">

                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="supplierFilterDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        Filter Supplier
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="supplierFilterDropdown">
                        <li><a class="dropdown-item supplier-filter" href="#" data-supplier="">Semua Supplier</a></li>
                        @foreach($suppliers as $supplier)
                            <li><a class="dropdown-item supplier-filter" href="#" data-supplier="{{ $supplier->id }}">{{ $supplier->name }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="statusFilterDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        Filter Status
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="statusFilterDropdown">
                        <li><a class="dropdown-item status-filter" href="#" data-status="">Semua Status</a></li>
                        <li><a class="dropdown-item status-filter" href="#" data-status="completed">Selesai</a></li>
                        <li><a class="dropdown-item status-filter" href="#" data-status="pending">Tertunda</a></li>
                        <li><a class="dropdown-item status-filter" href="#" data-status="cancelled">Dibatalkan</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="purchases-table" class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>Nomor Faktur</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Total Pembelian</th>
                        <th>Metode Pembayaran</th>
                        <th>Status</th>
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
            // Initialize filters
            const filters = {
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                supplier_id: '',
                status: ''
            };

            const purchasesTable = $('#purchases-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('reports.purchasing') }}",
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.supplier_id = $('#supplier_id').val();
                        d.status = $('#status').val();
                    }
                },
                columns: [
                    {data: 'invoice_number', name: 'invoice_number'},
                    {data: 'formatted_date', name: 'purchase_date'},
                    {data: 'supplier_name', name: 'supplier_id'},
                    {data: 'formatted_total', name: 'final_amount'},
                    {
                        data: 'payment_type',
                        name: 'payment_type',
                        render: function (data) {
                            if (data === 'cash') {
                                return '<span class="badge bg-info">Tunai</span>';
                            } else {
                                return '<span class="badge bg-primary">Transfer</span>';
                            }
                        }
                    },
                    {data: 'status_label', name: 'status'},
                ],
                order: [[1, 'desc']]
            });

            // Chart management
            let charts = {
                purchase: null,
                paymentMethod: null
            };

            // Event handlers
            $('.supplier-filter').on('click', function (e) {
                e.preventDefault();
                filters.supplier_id = $(this).data('supplier');
                $('#supplier_id').val(filters.supplier_id);
                $('#supplierFilterDropdown').text($(this).text());
                purchasesTable.ajax.reload();
            });

            $('.status-filter').on('click', function (e) {
                e.preventDefault();
                filters.status = $(this).data('status');
                $('#status').val(filters.status);
                $('#statusFilterDropdown').text($(this).text());
                purchasesTable.ajax.reload();
            });

            $('#date-filter-form').on('submit', function (e) {
                e.preventDefault();
                filters.start_date = $('#start_date').val();
                filters.end_date = $('#end_date').val();
                purchasesTable.ajax.reload();
                loadChartData();
            });

            // Load chart data
            function loadChartData() {
                $.ajax({
                    url: "{{ route('reports.purchasing.chart') }}",
                    method: "GET",
                    data: {
                        start_date: filters.start_date,
                        end_date: filters.end_date
                    },
                    success: function (response) {
                        if (response.success) {
                            renderCharts(response);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error loading chart data:", error);
                        showChartError('#purchaseChart');
                        showChartError('#paymentMethodChart');
                    }
                });
            }

            function showChartError(selector) {
                $(selector).html('<div class="text-center py-5 text-muted">Gagal memuat data</div>');
            }

            function renderCharts(data) {
                renderPurchaseChart(data.purchases);
                renderPaymentMethodChart(data.payment_methods);
            }

            function renderPurchaseChart(data) {
                if (charts.purchase) {
                    charts.purchase.destroy();
                }

                $("#purchaseChart").empty();

                if (!data || data.length === 0) {
                    $("#purchaseChart").html('<div class="text-center py-5 text-muted">Tidak ada data untuk periode ini</div>');
                    return;
                }

                const options = {
                    series: [
                        {
                            name: "Total Pembelian",
                            data: data.map(item => parseFloat(item.amount))
                        }
                    ],
                    chart: {
                        type: "line",
                        height: 300,
                        fontFamily: 'Public Sans, sans-serif',
                        toolbar: {
                            show: false
                        }
                    },
                    colors: ['#696cff'],
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    markers: {
                        size: 4,
                        colors: ['#696cff'],
                        strokeColors: '#fff',
                        strokeWidth: 2,
                        hover: {
                            size: 6
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: data.map(item => item.date)
                    },
                    yaxis: {
                        title: {
                            text: 'Jumlah (Rp)'
                        },
                        labels: {
                            formatter: val => new Intl.NumberFormat('id-ID').format(val)
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: val => "Rp " + new Intl.NumberFormat('id-ID').format(val)
                        }
                    },
                    grid: {
                        borderColor: '#f1f1f1',
                    }
                };

                try {
                    charts.purchase = new ApexCharts(document.querySelector("#purchaseChart"), options);
                    charts.purchase.render();
                } catch (e) {
                    console.error("Error rendering purchase chart:", e);
                    showChartError('#purchaseChart');
                }
            }

            function renderPaymentMethodChart(data) {
                if (charts.paymentMethod) {
                    charts.paymentMethod.destroy();
                }

                $("#paymentMethodChart").empty();

                if (!data || data.length === 0) {
                    $("#paymentMethodChart").html('<div class="text-center py-5 text-muted">Tidak ada data untuk periode ini</div>');
                    return;
                }

                const options = {
                    series: data.map(item => parseFloat(item.percentage)),
                    chart: {
                        type: 'donut',
                        height: 250,
                        fontFamily: 'Public Sans, sans-serif',
                    },
                    labels: data.map(item => item.method === 'cash' ? 'Tunai' : 'Transfer'),
                    colors: ['#696cff', '#03c3ec'],
                    legend: {
                        position: 'bottom'
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total'
                                    }
                                }
                            }
                        }
                    }
                };

                try {
                    charts.paymentMethod = new ApexCharts(document.querySelector("#paymentMethodChart"), options);
                    charts.paymentMethod.render();
                } catch (e) {
                    console.error("Error rendering payment chart:", e);
                    showChartError('#paymentMethodChart');
                }
            }

            // Initial load
            loadChartData();
        });
    </script>
@endpush
