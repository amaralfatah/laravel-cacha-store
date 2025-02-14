@extends('layouts.app')

@section('content')

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Sales Report</h5>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('reports.sales') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                           value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                           value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                                        {{--                                            <a href="{{ route('reports.export-sales') }}?start_date={{ request('start_date') }}&end_date={{ request('end_date') }}"--}}
                                        {{--                                               class="btn btn-secondary">Export</a>--}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Sales</h6>
                                    <h4 class="mb-0">{{ number_format($summary['total_sales'], 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Transactions</h6>
                                    <h4 class="mb-0">{{ $summary['total_transactions'] }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Average Transaction</h6>
                                    <h4 class="mb-0">{{ number_format($summary['average_transaction'], 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Items Sold</h6>
                                    <h4 class="mb-0">{{ $summary['total_items'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sales Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Cashier</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Discount</th>
                                <th>Tax</th>
                                <th>Final Amount</th>
                                <th>Payment</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($sales as $sale)
                                <tr>
                                    <td>{{ $sale->invoice_number }}</td>
                                    <td>{{ $sale->invoice_date->format('Y-m-d H:i') }}</td>
                                    <td>{{ $sale->customer->name }}</td>
                                    <td>{{ $sale->cashier->name }}</td>
                                    <td>{{ $sale->items->sum('quantity') }}</td>
                                    <td>{{ number_format($sale->total_amount, 2) }}</td>
                                    <td>{{ number_format($sale->discount_amount, 2) }}</td>
                                    <td>{{ number_format($sale->tax_amount, 2) }}</td>
                                    <td>{{ number_format($sale->final_amount, 2) }}</td>
                                    <td>{{ ucfirst($sale->payment_type) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No sales data found</td>
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
