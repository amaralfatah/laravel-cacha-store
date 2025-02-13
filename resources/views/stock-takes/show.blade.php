@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Stock Take Details</h2>
        @if($stockTake->status === 'draft')
            <form action="{{ route('stock-takes.complete', $stockTake) }}" method="POST" class="d-flex gap-2">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-success"
                        onclick="return confirm('Are you sure? This will update your actual stock quantities.')">
                    Complete Stock Take
                </button>
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
                    <p>
                            <span class="badge bg-{{ $stockTake->status === 'completed' ? 'success' : 'warning' }}">
                                {{ ucfirst($stockTake->status) }}
                            </span>
                    </p>
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
            <!-- Summary section -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="card-title">Total Items</h6>
                            <h4>{{ $stockTake->items->count() }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="card-title">Items with Difference</h6>
                            <h4>{{ $stockTake->items->filter(fn($item) => $item->actual_qty != $item->system_qty)->count() }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="card-title">Items Over</h6>
                            <h4>{{ $stockTake->items->filter(fn($item) => $item->actual_qty > $item->system_qty)->count() }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="card-title">Items Under</h6>
                            <h4>{{ $stockTake->items->filter(fn($item) => $item->actual_qty < $item->system_qty)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items table -->
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Unit</th>
                        <th class="text-end">System Stock</th>
                        <th class="text-end">Actual Stock</th>
                        <th class="text-end">Difference</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($stockTake->items as $item)
                        <tr>
                            <td>
                                {{ $item->product->name }}
                                @if($item->product->barcode)
                                    <br>
                                    <small class="text-muted">{{ $item->product->barcode }}</small>
                                @endif
                            </td>
                            <td>{{ $item->product->category->name }}</td>
                            <td>{{ $item->unit->name }}</td>
                            <td class="text-end">{{ number_format($item->system_qty, 2) }}</td>
                            <td class="text-end">{{ number_format($item->actual_qty, 2) }}</td>
                            <td class="text-end">
                                @php
                                    $difference = $item->actual_qty - $item->system_qty;
                                    $textClass = $difference == 0 ? 'success' : ($difference > 0 ? 'info' : 'danger');
                                @endphp
                                <span class="text-{{ $textClass }}">
                                        {{ $difference > 0 ? '+' : '' }}{{ number_format($difference, 2) }}
                                    </span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('stock-takes.index') }}" class="btn btn-secondary">Back to List</a>

        @if($stockTake->status === 'completed')
            <button type="button" class="btn btn-primary" onclick="window.print()">
                Print Report
            </button>
        @endif
    </div>


    @push('styles')
        <style>
            @media print {
                .btn {
                    display: none !important;
                }

                .card {
                    border: none !important;
                }

                .table {
                    border-collapse: collapse !important;
                }

                .table td, .table th {
                    background-color: #fff !important;
                    border: 1px solid #ddd !important;
                }
            }
        </style>
    @endpush
@endsection
