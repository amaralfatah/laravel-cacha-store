@extends('layouts.app')

@section('content')

    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="row">
                <!-- Sales Report Card -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Sales Report</h5>
                            <p class="card-text">View detailed sales transactions, daily/monthly summaries, and revenue
                                analysis.</p>
                            <a href="{{ route('reports.sales') }}" class="btn btn-primary">View Sales Report</a>
                        </div>
                    </div>
                </div>

                <!-- Inventory Report Card -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Inventory Report</h5>
                            <p class="card-text">Check current stock levels, low stock alerts, and product inventory
                                status.</p>
                            <a href="{{ route('reports.inventory') }}" class="btn btn-primary">View Inventory Report</a>
                        </div>
                    </div>
                </div>

                <!-- Stock Movement Report Card -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Stock Movement Report</h5>
                            <p class="card-text">Track all stock movements including sales, adjustments, and stock
                                takes.</p>
                            <a href="{{ route('reports.stock-movement') }}" class="btn btn-primary">View Stock
                                Movements</a>
                        </div>
                    </div>
                </div>

                <!-- Financial Report Card -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Financial Report</h5>
                            <p class="card-text">View financial summaries, cash flow, and balance history.</p>
                            <a href="{{ route('reports.financial') }}" class="btn btn-primary">View Financial Report</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
