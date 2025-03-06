@extends('layouts.app')

@section('content')
        <x-section-header title="Dashboard"/>

        <div class="row mb-4">
            <x-card-status
                title="Penjualan Hari Ini"
                :value="$todaySales"
                :growth="$salesGrowth"
                subtitle="dibandingkan kemarin"
                icon="bx-calendar"
                iconColor="primary" />

            <x-card-status
                title="Penjualan Bulan Ini"
                :value="$monthlySales"
                :growth="$monthlyGrowth"
                subtitle="dibandingkan bulan lalu"
                icon="bx-dollar"
                iconColor="success" />

            <x-card-status
                title="Transaksi Hari Ini"
                :value="$todayTransactions"
                subtitle="transaksi sukses"
                icon="bx-receipt"
                iconColor="info"
                format="normal" />

            <x-card-status
                title="Produk Aktif"
                :value="$activeProducts"
                subtitle="total produk"
                icon="bx-package"
                iconColor="warning"
                format="normal" />
        </div>

        <div class="row">
            <!-- Chart -->
            <div class="col-12 col-lg-8 order-2 order-md-3 order-lg-1 mb-4">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0">Penjualan Bulanan</h5>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="chartDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="chartDropdown">
                                <a class="dropdown-item" href="javascript:void(0);">Export Data</a>
                                <a class="dropdown-item" href="javascript:void(0);">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlySalesChart" height="320"></canvas>
                    </div>
                </div>
            </div>

            <!-- Right column cards -->
            <div class="col-12 col-lg-4 order-1 order-md-1 order-lg-2">
                @if(Auth::user()->role === 'admin' && count($stores) > 0)
                    <!-- Stores Performance (Admin only) -->
                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0">Performa Toko</h5>
                            <small class="text-muted">Bulan Ini</small>
                        </div>
                        <div class="card-body">
                            @foreach($stores as $store)
                                <div class="d-flex justify-content-between mb-4">
                                    <div class="d-flex flex-column">
                                        <span>{{ $store->name }}</span>
                                        <small class="text-muted">Rp {{ number_format($store->sales_total ?? 0, 0, ',', '.') }}</small>
                                    </div>
                                    <div class="w-50">
                                        @php
                                            $highestSales = $stores->max('sales_total') ?: 1;
                                            $percentage = ($store->sales_total / $highestSales) * 100;
                                        @endphp
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%"
                                                 aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <!-- Top Categories -->
                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0">Kategori Teratas</h5>
                            <small class="text-muted">Bulan Ini</small>
                        </div>
                        <div class="card-body">
                            @foreach($topCategories as $category)
                                <div class="d-flex justify-content-between mb-4">
                                    <div class="d-flex flex-column">
                                        <span>{{ $category->name }}</span>
                                        <small class="text-muted">Rp {{ number_format($category->total_sales ?? 0, 0, ',', '.') }}</small>
                                    </div>
                                    <div class="w-50">
                                        @php
                                            $highestSales = $topCategories->max('total_sales') ?: 1;
                                            $percentage = ($category->total_sales / $highestSales) * 100;
                                        @endphp
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%"
                                                 aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <!-- Top Products -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0">Produk Terlaris</h5>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="productDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="productDropdown">
                                <a class="dropdown-item" href="javascript:void(0);">Lihat Semua</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Unit</th>
                                    <th>Jumlah</th>
                                    <th>Total</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                @forelse($topProducts as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->unit_name }}</td>
                                        <td>{{ number_format($product->total_quantity, 0) }}</td>
                                        <td>Rp {{ number_format($product->total_sales, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada data penjualan</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0">Peringatan Stok Rendah</h5>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="stockDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="stockDropdown">
                                <a class="dropdown-item" href="javascript:void(0);">Lihat Semua</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Unit</th>
                                    <th>Stok</th>
                                    <th>Min</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                @forelse($lowStockProducts as $product)
                                    <tr>
                                        <td>{{ $product->product_name }}</td>
                                        <td>{{ $product->unit_name }}</td>
                                        <td>{{ number_format($product->stock, 0) }}</td>
                                        <td>{{ number_format($product->min_stock, 0) }}</td>
                                        <td>
                                            <span class="badge bg-danger">Stok Rendah</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada produk dengan stok rendah</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Setup data for sales chart
            var salesData = @json($allDates);
            var labels = Object.keys(salesData);
            var data = Object.values(salesData);

            // Default colors if template config is not available
            const chartColors = {
                primary: '#696cff',
                borderColor: '#eceef1',
                bodyColor: '#697a8d'
            };

            // Get chart canvas context
            const ctx = document.getElementById('monthlySalesChart').getContext('2d');

            // Create gradient for the chart background
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(105, 108, 255, 0.5)');
            gradient.addColorStop(1, 'rgba(105, 108, 255, 0.01)');

            // Config
            const config = {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Penjualan Harian',
                        data: data,
                        borderColor: chartColors.primary,
                        backgroundColor: gradient,
                        borderWidth: 2,
                        tension: 0.4,
                        pointStyle: 'circle',
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        pointHoverBorderWidth: 2,
                        pointBackgroundColor: chartColors.primary,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                color: chartColors.borderColor,
                                drawBorder: false,
                                borderColor: chartColors.borderColor
                            },
                            ticks: {
                                color: chartColors.bodyColor,
                                maxTicksLimit: 7
                            }
                        },
                        y: {
                            grid: {
                                drawBorder: false,
                                borderColor: chartColors.borderColor,
                                color: chartColors.borderColor,
                            },
                            ticks: {
                                color: chartColors.bodyColor,
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Penjualan: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                }
                            }
                        },
                        legend: {
                            display: false
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        intersect: false
                    }
                }
            };

            // Render chart
            const salesChart = new Chart(ctx, config);
        });
    </script>
@endpush
