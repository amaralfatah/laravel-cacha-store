@extends('layouts.app')

@section('content')

    <x-section-header
        title="Stock Opname"
        :route="route('stock-takes.create')"
        buttonText="Tambah Stock Opname"
        icon="bx-plus"
    />

    <div class="card">
        <div class="card-body">
            @if($stockTakes->isEmpty())
                <div class="text-center py-4">
                    <p class="text-muted mb-0">No stock takes found</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jml Barang</th>
                            <th>Status</th>
                            <th>Dibuat Oleh</th>
{{--                            <th>Created At</th>--}}
                            <th class="text-end">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($stockTakes as $stockTake)
                            <tr>
                                <td>{{ $stockTake->date }}</td>
                                <td>{{ $stockTake->items_count ?? $stockTake->items->count() }}</td>
                                <td>
                                        <span
                                            class="badge bg-{{ $stockTake->status === 'completed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($stockTake->status) }}
                                        </span>
                                </td>
                                <td>{{ $stockTake->creator->name }}</td>
{{--                                <td>{{ $stockTake->created_at->format('Y-m-d H:i') }}</td>--}}
                                <td class="text-end">
                                    <a href="{{ route('stock-takes.show', $stockTake) }}"
                                       class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $stockTakes->links() }}
                </div>
            @endif
        </div>
    </div>

@endsection
