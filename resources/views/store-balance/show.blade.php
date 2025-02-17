{{-- resources/views/admin/store-balance/balance-overview.blade.php --}}
@extends('layouts.app')

@section('content')

    <x-section-header title="Store Balance - {{ $store->name }} ({{ $store->code }})">
        <x-slot:actions>
            <a href="{{ route('stores.balance.history', $store) }}" class="btn btn-secondary me-2">
                <i class='bx bx-history me-1'></i> History
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adjustmentModal">
                <i class='bx bx-plus me-1'></i> Adjustment
            </button>
        </x-slot:actions>
    </x-section-header>

            <!-- Balance Summary Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Total Balance</h6>
                                    <h4 class="text-primary mt-2">{{ number_format($summary['total_balance'], 2) }}</h4>
                                </div>
                                <div class="rounded-circle bg-label-primary p-3">
                                    <i class='bx bx-wallet fs-4 text-primary'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Cash Balance</h6>
                                    <h4 class="text-success mt-2">{{ number_format($summary['cash_balance'], 2) }}</h4>
                                </div>
                                <div class="rounded-circle bg-label-success p-3">
                                    <i class='bx bx-money fs-4 text-success'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Non-Cash Balance</h6>
                                    <h4 class="text-info mt-2">{{ number_format($summary['non_cash_balance'], 2) }}</h4>
                                </div>
                                <div class="rounded-circle bg-label-info p-3">
                                    <i class='bx bx-credit-card fs-4 text-info'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Summary -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Today's Cash Summary</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <span>Cash In:</span>
                                <span class="text-success">+ {{ number_format($summary['today_cash_in'], 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Cash Out:</span>
                                <span class="text-danger">- {{ number_format($summary['today_cash_out'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Today's Transfer Summary</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <span>Transfer In:</span>
                                <span class="text-success">+ {{ number_format($summary['today_transfer_in'], 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Transfer Out:</span>
                                <span class="text-danger">- {{ number_format($summary['today_transfer_out'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    @include('store-balance.adjustment-modal')
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('#adjustmentModal form');
            const amountInput = document.querySelector('#amount');

            amountInput.addEventListener('input', function () {
                if (this.value < 0) {
                    this.value = 0;
                }
            });

            form.addEventListener('submit', function (e) {
                const amount = parseFloat(amountInput.value);
                if (amount <= 0) {
                    e.preventDefault();
                    alert('Amount must be greater than 0');
                    return false;
                }
            });
        });
    </script>
@endpush
