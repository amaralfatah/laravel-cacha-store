@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Units</h5>
                        <a href="{{ route('units.create') }}" class="btn btn-primary">Create New</a>
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Base Unit</th>
                                        <th>Created At</th>
                                        <th width="180px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($units as $unit)
                                        <tr>
                                            <td>{{ $unit->id }}</td>
                                            <td>{{ $unit->name }}</td>
                                            <td>{{ $unit->code }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $unit->is_base_unit ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $unit->is_base_unit ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                            <td>{{ $unit->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <a href="{{ route('units.edit', $unit) }}" class="btn btn-sm btn-info">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </a>
                                                <form action="{{ route('units.destroy', $unit) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure you want to delete this unit?')">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No units found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $units->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
