{{-- // resources/views/discounts/index.blade.php --}}
@extends('layouts.app')

@section('content')

    <x-section-header
        title="Manajemen Diskon"
        :route="route('discounts.create')"
        buttonText="Tambah Diskon"
        icon="bx-plus"
    />

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th>Nama</th>
                    <th>Tipe</th>
                    <th>Nilai</th>
                    @if(auth()->user()->role === 'admin')
                        <th>Toko</th>
                    @endif
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($discounts as $discount)
                    <tr>
                        <td>{{ $discount->name }}</td>
                        <td>{{ ucfirst($discount->type) }}</td>
                        <td>
                            @if ($discount->type === 'percentage')
                                {{ number_format($discount->value, 2) }}%
                            @else
                                Rp {{ number_format($discount->value, 0) }}
                            @endif
                        </td>
                        @if(auth()->user()->role === 'admin')
                            <td>{{ $discount->store->name }}</td>
                        @endif
                        <td>
                            <span class="badge {{ $discount->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $discount->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('discounts.edit', $discount) }}" class="btn btn-sm btn-info">Edit</a>
                            <form action="{{ route('discounts.destroy', $discount) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure?')">Delete
                                </button>
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
