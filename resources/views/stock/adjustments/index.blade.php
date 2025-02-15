@extends('layouts.app')

@section('content')

    <x-section-header
        title="Penyesuaian Stok"
        :route="route('stock.adjustments.create')"
        buttonText="Tambah Penyesuaian Stok"
        icon="bx-plus"
    />

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
