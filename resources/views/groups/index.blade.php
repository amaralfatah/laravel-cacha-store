@extends('layouts.app')

@section('content')
    <x-section-header
        title="Manajemen Kelompok"
        :route="route('groups.create')"
        buttonText="Tambah Kelompok"
        icon="bx-plus"
    />

    <div class="card">
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($groups as $group)
                        <tr>
                            <td>{{ $group->code }}</td>
                            <td>{{ $group->name }}</td>
                            <td>
                                            <span class="badge {{ $group->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $group->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                            </td>
                            <td>
                                <form action="{{ route('groups.destroy', $group) }}" method="POST"
                                      class="d-inline">
                                    <a href="{{ route('groups.edit', $group) }}" class="btn btn-sm btn-primary">Edit</a>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure?')">Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No groups found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $groups->links() }}
            </div>
        </div>
    </div>
@endsection
