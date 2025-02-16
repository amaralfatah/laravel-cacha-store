@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit User</h5>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('users.update', $user) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">

                                    {{-- Pada create.blade.php dan edit.blade.php --}}
                                    <div class="mb-3">
                                        <label for="store_id" class="form-label">Toko</label>
                                        <select class="form-select @error('store_id') is-invalid @enderror"
                                                id="store_id" name="store_id" required>
                                            <option value="">Pilih Toko</option>
                                            @foreach($stores as $store)
                                                <option value="{{ $store->id }}"
                                                    {{ old('store_id', $user->store_id ?? '') == $store->id ? 'selected' : '' }}>
                                                    {{ $store->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('store_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nama</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="role" class="form-label">Role</label>
                                        <select class="form-select @error('role') is-invalid @enderror"
                                                id="role" name="role" required>
                                            <option value="">Pilih Role</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role }}"
                                                    {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                                                    {{ ucfirst($role) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    @if(auth()->user()->role === 'super_admin')
                                        <div class="mb-3">
                                            <label for="store_id" class="form-label">Toko</label>
                                            <select class="form-select @error('store_id') is-invalid @enderror"
                                                    id="store_id" name="store_id" required>
                                                <option value="">Pilih Toko</option>
                                                @foreach($stores as $store)
                                                    <option value="{{ $store->id }}"
                                                        {{ old('store_id', $user->store_id) == $store->id ? 'selected' : '' }}>
                                                        {{ $store->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('store_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password (Kosongkan jika tidak diubah)</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                               id="password" name="password">
                                        @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                        <input type="password" class="form-control"
                                               id="password_confirmation" name="password_confirmation">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
