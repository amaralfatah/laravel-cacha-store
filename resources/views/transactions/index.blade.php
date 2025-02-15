<!-- resources/views/transactions/index.blade.php -->
@extends('layouts.app')

@section('content')

    <x-section-header
        title="Daftar Transaksi"
    />

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>No. Invoice</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->invoice_number }}</td>
                            <td>{{ $transaction->invoice_date->format('d/m/Y H:i') }}</td>
                            <td>{{ $transaction->customer->name }}</td>
                            <td>{{ number_format($transaction->final_amount, 0, ',', '.') }}</td>
                            <td>
                                @if ($transaction->status == 'pending')
                                    <span class="badge bg-warning">Draft</span>
                                @elseif($transaction->status == 'success')
                                    <span class="badge bg-success">Selesai</span>
                                @else
                                    <span class="badge bg-danger">Gagal</span>
                                @endif
                            </td>
                            <td>
                                @if ($transaction->status == 'pending')
                                    <a href="{{ route('transactions.continue', $transaction->id) }}"
                                       class="btn btn-primary btn-sm">Lanjutkan</a>
                                @else
                                    <a href="{{ route('pos.print-invoice', $transaction->id) }}"
                                       class="btn btn-info btn-sm" target="_blank">Detail</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                {{ $transactions->links() }}
            </div>
        </div>
    </div>

@endsection
