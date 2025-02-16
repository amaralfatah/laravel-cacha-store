@extends('layouts.app')

@section('content')

    <x-section-header
        title="Manajemen Kategori"
        :route="route('categories.create')"
        buttonText="Tambah Kategori"
        icon="bx-plus"
    />

    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        @if(auth()->user()->role === 'admin')
                            <th>Toko</th>
                        @endif
                        <th>Kelompok</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>{{ $category->code }}</td>
                            <td>{{ $category->name }}</td>
                            @if(auth()->user()->role === 'admin')
                                <td>{{ $category->store->name }}</td>
                            @endif
                            <td>{{ $category->group->name ?? '-' }}</td>
                            <td>
                                    <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                            </td>
                            {{--                            <td>{{ $category->created_at->format('Y-m-d H:i') }}</td>--}}
                            <td>
                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-info">Edit</a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
@endsection
