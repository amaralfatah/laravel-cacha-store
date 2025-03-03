{{-- resources/views/purchases/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <x-section-header title="Data Pembelian">
        <x-slot:actions>
            <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Tambah
            </a>
        </x-slot:actions>
    </x-section-header>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->invoice_number }}</td>
                                <td>{{ $purchase->supplier->name }}</td>
                                <td>{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                                <td>{{ number_format($purchase->final_amount, 2) }}</td>
                                <td>
                                    @if($purchase->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($purchase->status == 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-danger">Cancelled</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $purchases->links() }}
        </div>
@endsection
