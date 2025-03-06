@extends('layouts.app')

@section('title', 'Performa Toko')

@section('content')
    <x-section-header title="Performa Toko">
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

    <!-- Top Stores Card -->
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center p-4">
            <div class="card-title mb-0">
                <h5 class="mb-0">Top Toko Bulan Ini</h5>
                <small class="text-muted">Berdasarkan pendapatan bulan {{ \Carbon\Carbon::now()->format('F Y') }}</small>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row">
                @forelse($topStores as $index => $store)
                    <div class="col-md-4 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md me-3 bg-light-primary">
                                <span class="avatar-initial rounded">{{ $index + 1 }}</span>
                            </div>
                            <div class="d-flex flex-column w-100">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-0">{{ $store->name }}</h6>
                                    <small class="text-muted">{{ $store->code }}</small>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span class="text-muted">Pendapatan</span>
                                    <h6 class="mb-0">Rp {{ number_format($store->revenue ?? 0, 0, ',', '.') }}</h6>
                                </div>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $index === 0 ? '100' : ($store->revenue / $topStores->first()->revenue * 100) }}%" role="progressbar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-3 text-muted">
                            <i class='bx bx-store-alt fs-1'></i>
                            <p>Belum ada data performa toko</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Store Performance Comparison -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center p-4">
            <h5 class="card-title mb-0">Perbandingan Performa Toko</h5>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Toko</th>
                        <th>Total Transaksi</th>
                        <th>Pendapatan</th>
                        <th>Rata-rata per Transaksi</th>
                        <th>Jumlah Pelanggan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($stores as $store)
                        <tr>
                            <td><span class="fw-medium">{{ $store->code }}</span></td>
                            <td>{{ $store->name }}</td>
                            <td>{{ $store->sales_count ?? 0 }}</td>
                            <td>Rp {{ number_format($store->revenue ?? 0, 0, ',', '.') }}</td>
                            <td>
                                @if(($store->sales_count ?? 0) > 0)
                                    Rp {{ number_format(($store->revenue ?? 0) / $store->sales_count, 0, ',', '.') }}
                                @else
                                    Rp 0
                                @endif
                            </td>
                            <td>{{ $store->customers_count ?? 0 }}</td>
                            <td>
                                @if($store->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('reports.store.detail', $store->id) }}">
                                            <i class="bx bx-detail me-1"></i> Detail
                                        </a>
                                        <form action="{{ route('stores.toggle-status', $store->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="dropdown-item">
                                                @if($store->is_active)
                                                    <i class="bx bx-power-off me-1 text-danger"></i> Nonaktifkan
                                                @else
                                                    <i class="bx bx-power-off me-1 text-success"></i> Aktifkan
                                                @endif
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-center text-muted">
                                    <i class='bx bx-store-alt fs-1'></i>
                                    <p>Belum ada toko yang terdaftar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Revenue Comparison Chart -->
    <div class="card mt-4">
        <div class="card-header d-flex align-items-center p-4">
            <div class="card-title mb-0">
                <h5 class="mb-0">Perbandingan Pendapatan Toko</h5>
                <small class="text-muted">Visualisasi pendapatan antar toko</small>
            </div>
        </div>
        <div class="card-body p-4">
            <div id="storeRevenueChart" style="width: 100%; height: 400px;"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            // Store revenue chart
            const storeRevenueChart = document.querySelector('#storeRevenueChart');

            if (storeRevenueChart) {
                const storeNames = [@foreach($stores as $store) '{{ $store->name }}', @endforeach];
                const storeRevenues = [@foreach($stores as $store) {{ $store->revenue ?? 0 }}, @endforeach];

                const options = {
                    series: [{
                        name: 'Pendapatan',
                        data: storeRevenues
                    }],
                    chart: {
                        type: 'bar',
                        height: 400,
                        fontFamily: 'Public Sans, sans-serif',
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            borderRadius: 4
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    colors: ['#696cff'],
                    xaxis: {
                        categories: storeNames,
                    },
                    yaxis: {
                        title: {
                            text: 'Pendapatan (Rp)'
                        },
                        labels: {
                            formatter: function (val) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                            }
                        }
                    }
                };

                new ApexCharts(storeRevenueChart, options).render();
            }

            // Handle date filter
            $('#date-filter-form').on('submit', function(e) {
                e.preventDefault();
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();

                window.location.href = `{{ route('reports.store-performance') }}?start_date=${startDate}&end_date=${endDate}`;
            });
        });
    </script>
@endpush
