@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Stock Adjustments</h2>
        <div>
            <a href="{{ route('stock-takes.index') }}" class="btn btn-info me-2">Stock Opname</a>
            <a href="{{ route('stock.adjustments.create') }}" class="btn btn-primary">New Adjustment</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Unit</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Notes</th>
                        <th>Created By</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($adjustments as $adjustment)
                        <tr>
                            <td>{{ $adjustment->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $adjustment->productUnit->product->name }}</td>
                            <td>{{ $adjustment->productUnit->unit->name }}</td>
                            <td>
                                    <span class="badge bg-{{ $adjustment->type === 'in' ? 'success' : 'danger' }}">
                                        {{ ucfirst($adjustment->type) }}
                                    </span>
                            </td>
                            <td>{{ number_format($adjustment->quantity, 2) }}</td>
                            <td>{{ $adjustment->notes }}</td>
                            <td>{{ $adjustment->creator->name }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $adjustments->links() }}
        </div>
    </div>

@endsection
