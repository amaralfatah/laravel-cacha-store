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
{{--                        <th>ID</th>--}}
                        <th>Name</th>
                        <th>Rate (%)</th>
                        <th>Status</th>
{{--                        <th>Created At</th>--}}
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($taxes as $tax)
                        <tr>
{{--                            <td>{{ $tax->id }}</td>--}}
                            <td>{{ $tax->name }}</td>
                            <td>{{ number_format($tax->rate, 2) }}%</td>
                            <td>
                                <span class="badge {{ $tax->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $tax->is_active ? 'Aktif' : 'Non Aktif' }}
                                </span>
                            </td>
{{--                            <td>{{ $tax->created_at->format('Y-m-d H:i') }}</td>--}}
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
