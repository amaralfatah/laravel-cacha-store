@extends('layouts.app')

@section('content')
    <x-section-header title="Store Details"/>

    <!-- Store Logo & Basic Info -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-column flex-sm-row align-items-center gap-4">
                <div class="text-center">
                    @if($store->logo)
                        <img src="{{ asset($store->logo) }}" alt="store-logo"
                             class="d-block rounded" style="width: 100px; height: 100px; object-fit: cover;"/>
                    @else
                        <div class="d-block rounded bg-light d-flex align-items-center justify-content-center"
                             style="width: 100px; height: 100px;">
                            <i class='bx bx-store text-secondary' style="font-size: 2rem;"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-grow-1 text-center text-sm-start">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $store->name }}</h4>
                            <span class="text-muted d-block mb-2">Store Code: {{ $store->code }}</span>
                            <span class="badge px-3 bg-{{ $store->is_active ? 'success' : 'danger' }} rounded-pill">
                                        <i class='bx {{ $store->is_active ? 'bx-check' : 'bx-x' }}'></i>
                                        {{ $store->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                        </div>
                        <div class="mt-3 mt-sm-0">
                            <a href="{{ route('user.store.edit') }}" class="btn btn-primary me-2">
                                <i class='bx bx-edit me-1 d-none d-sm-inline-block'></i> Edit Store
                            </a>
                            <a href="{{ route('user.store.balance.show') }}" class="btn btn-info">
                                <i class='bx bx-money me-1 d-none d-sm-inline-block'></i> Store Balance
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-3 mb-4">
        <!-- After the existing cards in the Quick Stats Cards row -->
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card border h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-label-success p-3 me-3">
                            <i class='bx bx-money fs-4 text-success'></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ number_format($store->storeBalance->cash_amount ?? 0, 2) }}</h6>
                            <small>Cash Balance</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <div class="card border h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-label-primary p-3 me-3">
                            <i class='bx bx-credit-card fs-4 text-primary'></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ number_format($store->storeBalance->non_cash_amount ?? 0, 2) }}</h6>
                            <small>Non-Cash Balance</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card border h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-label-info p-3 me-3">
                            <i class='bx bx-package fs-4 text-info'></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $store->products_count ?? 0 }}</h6>
                            <small>Total Products</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <div class="card border h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-label-success p-3 me-3">
                            <i class='bx bx-user fs-4 text-success'></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $store->customers_count ?? 0 }}</h6>
                            <small>Total Customers</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <div class="card border h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-label-warning p-3 me-3">
                            <i class='bx bx-shopping-bag fs-4 text-warning'></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $store->transactions_count ?? 0 }}</h6>
                            <small>Total Transactions</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="card border">
        <div class="card-header bg-transparent">
            <h6 class="card-title mb-0">
                <i class='bx bx-info-circle me-2 text-primary'></i>
                Contact Information
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-label-primary p-2 me-3">
                            <i class='bx bx-envelope text-primary'></i>
                        </div>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">Email</small>
                            <span class="text-break">{{ $store->email }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-label-primary p-2 me-3">
                            <i class='bx bx-phone text-primary'></i>
                        </div>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">Phone</small>
                            <span>{{ $store->phone }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="d-flex align-items-start">
                        <div class="rounded-circle bg-label-primary p-2 me-3">
                            <i class='bx bx-map text-primary'></i>
                        </div>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">Address</small>
                            <span class="text-break">{{ $store->address }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
