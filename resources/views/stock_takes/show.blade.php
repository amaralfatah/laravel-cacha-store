<!-- resources/views/stock_takes/show.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Stock Take Details</h2>
            @if($stockTake->status === 'draft')
                <form action="{{ route('stock-takes.complete', $stockTake) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">Complete Stock Take</button>
                </form>
            @endif
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <p class="mb-1"><strong>Date:</strong></p>
                        <p>{{ $stockTake->date }}</p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1"><strong>Status:</strong></p>
                        <p><span class="badge bg-{{ $stockTake->status === 'completed' ? 'success' : 'warning' }}">
                        {{ ucfirst($stockTake->status) }}
                    </span></p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1"><strong>Created By:</strong></p>
                        <p>{{ $stockTake->creator->name }}</p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1"><strong>Created At:</strong></p>
                        <p>{{ $stockTake->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
                @if($stockTake->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <p class="mb-1"><strong>Notes:</strong></p>
                            <p>{{ $stockTake->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Product</th>
                        <th>Unit</th>
                        <th>System Stock</th>
                        <th>Actual Stock</th>
                        <th>Difference</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($stockTake->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->unit->name }}</td>
                            <td>{{ number_format($item->system_qty, 2) }}</td>
                            <td>{{ number_format($item->actual_qty, 2) }}</td>
                            <td>
                <span class="text-{{ $item->actual_qty - $item->system_qty == 0 ? 'success' : ($item->actual_qty - $item->system_qty > 0 ? 'info' : 'danger') }}">
                    {{ number_format($item->actual_qty - $item->system_qty, 2) }}
                </span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('stock-takes.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
@endsection
