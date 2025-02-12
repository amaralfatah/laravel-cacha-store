<!-- resources/views/stock_takes/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Stock Takes</h2>
        <a href="{{ route('stock-takes.create') }}" class="btn btn-primary">New Stock Take</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($stockTakes as $stockTake)
                    <tr>
                        <td>{{ $stockTake->date }}</td>
                        <td>
                            <span class="badge bg-{{ $stockTake->status === 'completed' ? 'success' : 'warning' }}">
                                {{ ucfirst($stockTake->status) }}
                            </span>
                        </td>
                        <td>{{ $stockTake->creator->name }}</td>
                        <td>{{ $stockTake->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('stock-takes.show', $stockTake) }}"
                               class="btn btn-sm btn-info">View</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $stockTakes->links() }}
        </div>
    </div>
@endsection



