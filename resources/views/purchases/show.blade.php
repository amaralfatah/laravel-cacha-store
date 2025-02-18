{{-- resources/views/purchases/show.blade.php --}}
@extends('layouts.app')

@section('content')

    <x-section-header title="Purchase Order Detail">
        <x-slot:actions>
            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
            <button onclick="printPO()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Print
            </button>
            @if($purchase->status === 'pending')
                <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            @endif
        </x-slot:actions>
    </x-section-header>


    <div class="card">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="card-title">Purchase Information</h5>
                    <table class="table table-sm">
                        <tr>
                            <td width="40%">Invoice Number</td>
                            <td>: {{ $purchase->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td>Purchase Date</td>
                            <td>: {{ $purchase->purchase_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>:
                                @if($purchase->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($purchase->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @else
                                    <span class="badge bg-danger">Cancelled</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Payment Type</td>
                            <td>: {{ ucfirst($purchase->payment_type) }}</td>
                        </tr>
                        @if($purchase->reference_number)
                            <tr>
                                <td>Reference Number</td>
                                <td>: {{ $purchase->reference_number }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
                <div class="col-md-6">
                    <h5 class="card-title">Supplier Information</h5>
                    <table class="table table-sm">
                        <tr>
                            <td width="40%">Name</td>
                            <td>: {{ $purchase->supplier->name }}</td>
                        </tr>
                        <tr>
                            <td>Phone</td>
                            <td>: {{ $purchase->supplier->phone }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <h5 class="card-title">Items</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Product</th>
                        <th>Unit</th>
                        <th class="text-end">Quantity</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($purchase->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->unit->name }}</td>
                            <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                            <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-end">{{ number_format($item->discount, 2) }}</td>
                            <td class="text-end">{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="5" class="text-end fw-bold">Total</td>
                        <td class="text-end">{{ number_format($purchase->total_amount, 2) }}</td>
                    </tr>
                    @if($purchase->tax_amount > 0)
                        <tr>
                            <td colspan="5" class="text-end">Tax</td>
                            <td class="text-end">{{ number_format($purchase->tax_amount, 2) }}</td>
                        </tr>
                    @endif
                    @if($purchase->discount_amount > 0)
                        <tr>
                            <td colspan="5" class="text-end">Discount</td>
                            <td class="text-end">{{ number_format($purchase->discount_amount, 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="5" class="text-end fw-bold">Final Amount</td>
                        <td class="text-end fw-bold">{{ number_format($purchase->final_amount, 2) }}</td>
                    </tr>
                    </tfoot>
                </table>
            </div>

            @if($purchase->notes)
                <div class="mt-4">
                    <h5 class="card-title">Notes</h5>
                    <p class="text-muted">{{ $purchase->notes }}</p>
                </div>
            @endif

            @if($purchase->status === 'pending')
                <div class="mt-4 text-end">
                    <form action="{{ route('purchases.update', $purchase) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="btn btn-success"
                                onclick="return confirm('Are you sure want to complete this purchase?')">
                            <i class="bi bi-check-circle"></i> Complete Purchase
                        </button>
                    </form>
                    <form action="{{ route('purchases.update', $purchase) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Are you sure want to cancel this purchase?')">
                            <i class="bi bi-x-circle"></i> Cancel Purchase
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function printPO() {
                window.print();
            }
        </script>
    @endpush
@endsection
