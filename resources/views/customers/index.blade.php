@extends('layouts.app')

@section('content')

    <x-section-header
        title="Manajemen Pelanggan"
        :route="route('customers.create')"
        buttonText="Tambah Pelanggan"
        icon="bx-plus"
    />

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Telpon</th>
{{--                        <th>Created At</th>--}}
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer)
                        <tr>
                            <td>{{ $customer->id }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->phone }}</td>
{{--                            <td>{{ $customer->created_at->format('Y-m-d H:i') }}</td>--}}
                            <td>
                                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-info">Edit</a>
                                <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline">
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
            {{ $customers->links() }}
        </div>
    </div>
@endsection
