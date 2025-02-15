{{-- resources/views/stores/index.blade.php --}}
@extends('layouts.app')

@section('page-action')

@endsection

@section('content')

    <x-section-header
        title="Manajemen Toko"
        :route="route('stores.create')"
        buttonText="Tambah Toko"
        icon="bx-plus"
    />

    {{-- Search and Filter --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('stores.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control" placeholder="Search by name, code or email">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Stores Table --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>Logo</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($stores as $store)
                    <tr>
                        <td>
                            @if($store->logo)
                                <img src="{{ $store->logo }}" alt="{{ $store->name }}"
                                     class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <div
                                    class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white"
                                    style="width: 40px; height: 40px;">
                                    N/A
                                </div>
                            @endif
                        </td>
                        <td>{{ $store->code }}</td>
                        <td>{{ $store->name }}</td>
                        <td>
                            <div>{{ $store->phone }}</div>
                            <small class="text-muted">{{ $store->email }}</small>
                        </td>
                        <td>
                            <form action="{{ route('stores.toggle-status', $store) }}" method="POST"
                                  class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="btn btn-sm {{ $store->is_active ? 'btn-success' : 'btn-danger' }}">
                                    {{ $store->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <a href="{{ route('stores.edit', $store) }}"
                               class="btn btn-sm btn-warning">
                                Edit
                            </a>
                            <form action="{{ route('stores.destroy', $store) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this store?')">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No stores found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $stores->links() }}
        </div>
    </div>
@endsection
