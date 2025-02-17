{{-- resources/views/products/import.blade.php --}}
@extends('layouts.app')

@section('content')
        <div class="card">
            <div class="card-header">
                <h3>Import Products</h3>
            </div>
            <div class="card-body">
                {{-- Success Message --}}
                @if(session('success'))
                    <div class="alert alert-success">
                        {!! session('success') !!}
                    </div>
                @endif

                {{-- Error Message --}}
                @if(session('error'))
                    <div class="alert alert-danger">
                        {!! session('error') !!}
                    </div>
                @endif

                {{-- Last Import Status --}}
                @if($lastImport)
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Status import terakhir:</strong> {{ $lastImport->filename }}
                                <br>
                                Status: <strong>{{ ucfirst($lastImport->status) }}</strong>
                                @if($lastImport->error)
                                    <br>Error: {{ $lastImport->error }}
                                @endif
                            </div>
                            @if($lastImport->status === 'failed')
                                <form action="{{ route('products.import') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="retry_import" value="{{ $lastImport->id }}">
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        Retry Import
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Progress Bar --}}
                @if($lastImport && $lastImport->status === 'processing')
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Progress Import</h5>
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated"
                                     role="progressbar"
                                     style="width: {{ ($lastImport->processed_rows / max($lastImport->total_rows, 1)) * 100 }}%">
                                    {{ $lastImport->processed_rows }} / {{ $lastImport->total_rows }} rows
                                </div>
                            </div>
                            <div class="text-muted mt-2 small">
                                Sedang memproses baris ke {{ $lastImport->processed_rows }} dari {{ $lastImport->total_rows }}
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Import Instructions --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Petunjuk Import</h5>
                        <ol class="ps-3">
                            <li>Download template Excel menggunakan tombol "Download Template"</li>
                            <li>Isi data sesuai format yang ada di template</li>
                            <li>Upload file yang sudah diisi</li>
                            <li>Klik tombol Import untuk memulai proses import</li>
                        </ol>
                    </div>
                </div>

                {{-- Import Form --}}
                <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="form-label">Pilih File Excel</label>
                        <input type="file"
                               name="file"
                               class="form-control @error('file') is-invalid @enderror"
                               accept=".xlsx,.xls">
                        @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Format yang didukung: .xlsx, .xls (max 5MB)
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload me-1"></i> Import
                        </button>
                        <a href="{{ route('products.import.template') }}" class="btn btn-secondary">
                            <i class="bi bi-download me-1"></i> Download Template
                        </a>
                    </div>
                </form>
            </div>
        </div>
@endsection
@push('scripts')
    <script>
        // Refresh progress every 5 seconds if import is processing
        @if($lastImport && $lastImport->status === 'processing')
        setTimeout(function() {
            window.location.reload();
        }, 5000);
        @endif
    </script>
@endpush
