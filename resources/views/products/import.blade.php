{{-- resources/views/products/import.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Import Products</h3>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {!! session('success') !!}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        {!! session('error') !!}
                    </div>
                @endif

                @if($lastImport)
                    <div class="alert alert-info">
                        Status import terakhir ({{ $lastImport->filename }}):
                        <strong>{{ ucfirst($lastImport->status) }}</strong>
                        @if($lastImport->error)
                            <br>Error: {{ $lastImport->error }}
                        @endif
                    </div>
                @endif

                    @if($lastImport && $lastImport->status === 'processing')
                        <div class="progress">
                            <div class="progress-bar" role="progressbar"
                                 style="width: {{ ($lastImport->processed_rows / $lastImport->total_rows) * 100 }}%">
                                {{ $lastImport->processed_rows }} / {{ $lastImport->total_rows }}
                            </div>
                        </div>
                    @endif

                <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Pilih File Excel</label>
                        <input type="file" name="file" class="form-control @error('file') is-invalid @enderror">
                        @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Import</button>
                        <a href="{{ route('products.import.template') }}" class="btn btn-secondary">Download Template</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
