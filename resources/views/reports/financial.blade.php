@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
    <x-section-header title="Laporan Keuangan">
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
    <div class="row mb-4">
        <x-card-status
            title="Total Pemasukan"
            subtitle="Seluruh aliran kas masuk"
            :value="$totalIncome"
            icon="bx-trending-up"
            iconColor="success"
            columnSize="col-md-3" />

        <x-card-status
            title="Total Pengeluaran"
            subtitle="Seluruh aliran kas keluar"
            :value="$totalExpense"
            icon="bx-trending-down"
            iconColor="danger"
            columnSize="col-md-3" />

        <x-card-status
            title="Arus Kas Bersih"
            subtitle="Pemasukan - Pengeluaran"
            :value="abs($netCashflow)"
            icon="bx-wallet"
            iconColor="primary"
            columnSize="col-md-3" />

        <x-card-status
            title="Saldo Toko"
            subtitle="Saldo saat ini"
            :value="$totalBalance"
            icon="bx-money"
            iconColor="info"
            columnSize="col-md-3" />
    </div>

    <!-- Charts -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center p-4">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Aliran Kas</h5>
                        <small class="text-muted">Pemasukan dan pengeluaran dalam periode terpilih</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="cashflowChart" style="width: 100%; height: 300px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center p-4">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Metode Pembayaran</h5>
                        <small class="text-muted">Distribusi transaksi</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="paymentMethodChart" style="width: 100%; height: 250px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center p-4">
            <h5 class="card-title mb-0">Riwayat Transaksi Keuangan</h5>
            <div class="d-flex gap-2">
                <input type="hidden" id="type" name="type">
                <input type="hidden" id="payment_method" name="payment_method">

                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="typeFilterDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        Filter Tipe
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="typeFilterDropdown">
                        <li><a class="dropdown-item type-filter" href="#" data-type="">Semua Transaksi</a></li>
                        <li><a class="dropdown-item type-filter" href="#" data-type="in">Pemasukan</a></li>
                        <li><a class="dropdown-item type-filter" href="#" data-type="out">Pengeluaran</a></li>
                    </ul>
                </div>

                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="methodFilterDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        Filter Metode
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="methodFilterDropdown">
                        <li><a class="dropdown-item method-filter" href="#" data-method="">Semua Metode</a></li>
                        <li><a class="dropdown-item method-filter" href="#" data-method="cash">Tunai</a></li>
                        <li><a class="dropdown-item method-filter" href="#" data-method="transfer">Transfer</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="financial-table" class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Tipe</th>
                        <th>Metode</th>
                        <th>Jumlah</th>
                        <th>Saldo</th>
                        <th>Dibuat Oleh</th>
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
                type: '',
                payment_method: ''
            };

            const financialTable = $('#financial-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('reports.financial') }}",
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.type = $('#type').val();
                        d.payment_method = $('#payment_method').val();
                    }
                },
                columns: [
                    {data: 'formatted_date', name: 'created_at'},
                    {data: 'notes', name: 'notes'},
                    {
                        data: 'type',
                        name: 'type',
                        render: function (data) {
                            if (data === 'in') {
                                return '<span class="badge bg-success">Pemasukan</span>';
                            } else {
                                return '<span class="badge bg-danger">Pengeluaran</span>';
                            }
                        }
                    },
                    {
                        data: 'payment_method',
                        name: 'payment_method',
                        render: function (data) {
                            if (data === 'cash') {
                                return '<span class="badge bg-info">Tunai</span>';
                            } else {
                                return '<span class="badge bg-primary">Transfer</span>';
                            }
                        }
                    },
                    {data: 'formatted_amount', name: 'amount'},
                    {data: 'formatted_balance', name: 'current_balance'},
                    {data: 'created_by', name: 'created_by'}
                ],
                order: [[0, 'desc']]
            });

            // Chart management
            let charts = {
                cashflow: null,
                paymentMethod: null
            };

            // Event handlers
            $('.type-filter').on('click', function (e) {
                e.preventDefault();
                filters.type = $(this).data('type');
                $('#typeFilterDropdown').text($(this).text());
                financialTable.ajax.reload();
            });

            $('.method-filter').on('click', function (e) {
                e.preventDefault();
                filters.payment_method = $(this).data('method');
                $('#methodFilterDropdown').text($(this).text());
                financialTable.ajax.reload();
            });

            $('#date-filter-form').on('submit', function (e) {
                e.preventDefault();
                filters.start_date = $('#start_date').val();
                filters.end_date = $('#end_date').val();
                financialTable.ajax.reload();
                loadChartData();
            });

            // Load chart data
            function loadChartData() {
                $.ajax({
                    url: "{{ route('reports.financial.chart') }}",
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
                        showChartError('#cashflowChart');
                        showChartError('#paymentMethodChart');
                    }
                });
            }

            function showChartError(selector) {
                $(selector).html('<div class="text-center py-5 text-muted">Gagal memuat data</div>');
            }

            function renderCharts(data) {
                renderCashflowChart(data.cashflow);
                renderPaymentMethodChart(data.payment_methods);
            }

            function renderCashflowChart(data) {
                if (charts.cashflow) {
                    charts.cashflow.destroy();
                }

                $("#cashflowChart").empty();

                if (!data || data.length === 0) {
                    $("#cashflowChart").html('<div class="text-center py-5 text-muted">Tidak ada data untuk periode ini</div>');
                    return;
                }

                const options = {
                    series: [
                        {
                            name: "Pemasukan",
                            data: data.map(item => parseFloat(item.income))
                        },
                        {
                            name: "Pengeluaran",
                            data: data.map(item => parseFloat(item.expense))
                        }
                    ],
                    chart: {
                        type: "bar",
                        height: 300,
                        fontFamily: 'Public Sans, sans-serif',
                        toolbar: {
                            show: false
                        }
                    },
                    colors: ['#03c3ec', '#ff3e1d'],
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            columnWidth: '55%'
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
                    legend: {
                        position: 'top'
                    }
                };

                try {
                    charts.cashflow = new ApexCharts(document.querySelector("#cashflowChart"), options);
                    charts.cashflow.render();
                } catch (e) {
                    console.error("Error rendering cashflow chart:", e);
                    showChartError('#cashflowChart');
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
                        animations: {
                            enabled: false
                        }
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
