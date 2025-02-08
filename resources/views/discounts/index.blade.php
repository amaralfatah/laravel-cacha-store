{{-- // resources/views/discounts/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Discounts</h5>
            <a href="{{ route('discounts.create') }}" class="btn btn-primary">Create New</a>
        </div>
        <div class="card-body">

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($discounts as $discount)
                        <tr>
                            <td>{{ $discount->id }}</td>
                            <td>{{ $discount->name }}</td>
                            <td>{{ ucfirst($discount->type) }}</td>
                            <td>
                                @if ($discount->type === 'percentage')
                                    {{ number_format($discount->value, 2) }}%
                                @else
                                    Rp {{ number_format($discount->value, 0) }}
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $discount->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $discount->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ $discount->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('discounts.edit', $discount) }}" class="btn btn-sm btn-info">Edit</a>
                                <form action="{{ route('discounts.destroy', $discount) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $discounts->links() }}
        </div>
    </div>
@endsection
