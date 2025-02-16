@extends('layouts.app')

@section('content')
    <x-section-header
        title="Manajemen Satuan"
        :route="route('units.create')"
        buttonText="Tambah Satuan"
        icon="bx-plus"
    />

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Name</th>
                        @if(auth()->user()->role === 'admin')
                            <th>Store</th>
                        @endif
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($units as $unit)
                        <tr>
                            <td>{{ $unit->code }}</td>
                            <td>{{ $unit->name }}</td>
                            @if(auth()->user()->role === 'admin')
                                <td>{{ $unit->store->name }}</td>
                            @endif
                            <td>
                                <a href="{{ route('units.edit', $unit) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <form action="{{ route('units.destroy', $unit) }}" method="POST" class="d-inline">
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
                            <td colspan="{{ auth()->user()->role === 'admin' ? 4 : 3 }}" class="text-center">
                                No units found.
                            </td>
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
@endsection
