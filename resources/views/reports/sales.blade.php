<!-- resources/views/reports/sales.blade.php -->
@extends('reports.layout')

@section('report-title', 'Sales Report')

@section('report-filters')
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <select name="type" class="form-select" onchange="this.form.submit()">
                    <option value="daily" {{ $type === 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="monthly" {{ $type === 'monthly' ? 'selected' : '' }}>Monthly</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="date" class="form-control" value="{{ $date }}"
                    onchange="this.form.submit()">
            </div>
        </div>
    </form>
@endsection

@section('report-content')
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Payment</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sales as $sale)
                    <tr>
                        <td>{{ $sale->invoice_number }}</td>
                        <td>{{ $sale->invoice_date->format('Y-m-d H:i') }}</td>
                        <td>{{ $sale->customer->name }}</td>
                        <td>
                            @foreach ($sale->items as $item)
                                <div>
                                    {{ $item->product->name }} -
                                    {{ $item->quantity }} {{ $item->unit->name }} x
                                    {{ number_format($item->unit_price) }}
                                </div>
                            @endforeach
                        </td>
                        <td>{{ number_format($sale->final_amount) }}</td>
                        <td>{{ ucfirst($sale->payment_type) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end"><strong>Total</strong></td>
                    <td colspan="2"><strong>{{ number_format($sales->sum('final_amount')) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection
