<!-- resources/views/reports/layout.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>@yield('report-title')</h4>
                        <div class="btn-group">
                            <a href="{{ route('reports.export.pdf', request()->segment(2)) }}" class="btn btn-danger">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a>
                            <a href="{{ route('reports.export.excel', request()->segment(2)) }}" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @yield('report-filters')
                        @yield('report-content')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
