@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Stock Movement Detail</h2>
            <a href="{{ route('stock.histories.index') }}" class="btn btn-secondary">Back to List</a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Product Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Product Name</strong></td>
                                <td>: {{ $history->productUnit->product->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Category</strong></td>
                                <td>: {{ $history->productUnit->product->category->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Unit</strong></td>
                                <td>: {{ $history->productUnit->unit->name }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Movement Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Date</strong></td>
                                <td>: {{ $history->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Type</strong></td>
                                <td>:
                                    <span class="badge bg-{{ $history->type === 'in' ? 'success' : ($history->type === 'out' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($history->type) }}
                                </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Quantity</strong></td>
                                <td>: {{ number_format($history->quantity, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Remaining Stock</strong></td>
                                <td>: {{ number_format($history->remaining_stock, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Source</strong></td>
                                <td>: {{ ucfirst(str_replace('_', ' ', $history->reference_type)) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Notes</strong></td>
                                <td>: {{ $history->notes }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
