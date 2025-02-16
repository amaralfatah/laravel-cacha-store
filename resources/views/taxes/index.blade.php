@extends('layouts.app')

@section('content')

    <x-section-header
        title="Manajemen Pajak"
        :route="route('taxes.create')"
        buttonText="Tambah Pajak"
        icon="bx-plus"
    />

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Rate</th>
                    @if(auth()->user()->role === 'admin')
                        <th>Store</th>
                    @endif
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($taxes as $tax)
                    <tr>
                        <td>{{ $tax->name }}</td>
                        <td>{{ $tax->rate }}%</td>
                        @if(auth()->user()->role === 'admin')
                            <td>{{ $tax->store->name }}</td>
                        @endif
                        <td>{{ $tax->is_active ? 'Active' : 'Inactive' }}</td>
                        <td>
                            <a href="{{ route('taxes.edit', $tax) }}" class="btn btn-sm btn-info">Edit</a>
                            <form action="{{ route('taxes.destroy', $tax) }}" method="POST" class="d-inline">
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
            {{ $taxes->links() }}
        </div>
    </div>
@endsection
