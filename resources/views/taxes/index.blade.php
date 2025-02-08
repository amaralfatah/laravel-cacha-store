@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Taxes</h5>
                        <a href="{{ route('taxes.create') }}" class="btn btn-primary">Create New</a>
                    </div>
                    <div class="card-body">

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Rate (%)</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($taxes as $tax)
                                    <tr>
                                        <td>{{ $tax->id }}</td>
                                        <td>{{ $tax->name }}</td>
                                        <td>{{ number_format($tax->rate, 2) }}%</td>
                                        <td>
                                            <span class="badge {{ $tax->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $tax->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $tax->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('taxes.edit', $tax) }}" class="btn btn-sm btn-info">Edit</a>
                                            <form action="{{ route('taxes.destroy', $tax) }}" method="POST"
                                                class="d-inline">
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
            </div>
        </div>
    </div>
@endsection
