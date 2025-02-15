@extends('layouts.app')

@section('content')

    <x-section-header
        title="Manajemen Pemasok"
        :route="route('suppliers.create')"
        buttonText="Tambah Pemasok"
        icon="bx-plus"
    />

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Telpon</th>
{{--                        <th>Created At</th>--}}
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->code }}</td>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->phone }}</td>
{{--                            <td>{{ $supplier->created_at->format('Y-m-d H:i') }}</td>--}}
                            <td>
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-info">Edit</a>
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline">
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
            {{ $suppliers->links() }}
        </div>
    </div>
@endsection
