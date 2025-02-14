@extends('layouts.app')

@section('title', 'Financial Report')

@section('content')

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div><i class="fas fa-chart-line me-1"></i> Financial Report</div>
                <button type="button" class="btn btn-sm btn-success" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Print
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <form method="GET" action="{{ route('reports.financial') }}" class="row g-3">
                                <div class="col-md-4">
                                    <label for="start_date" class="form-label small mb-1">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                           value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="end_date" class="form-label small mb-1">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                           value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-filter me-1"></i> Filter
                                    </button>
                                    <a href="{{ route('reports.financial') }}" class="btn btn-secondary">
                                        <i class="fas fa-sync-alt me-1"></i> Reset
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-primary text-white mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small">Total Income</div>
                                    <div class="text-lg fw-bold">
                                        Rp {{ number_format($summary['total_in'], 0, ',', '.') }}</div>
                                </div>
                                <div>
                                    <i class="fas fa-money-bill-wave fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-danger text-white mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small">Total Expenses</div>
                                    <div class="text-lg fw-bold">
                                        Rp {{ number_format($summary['total_out'], 0, ',', '.') }}</div>
                                </div>
                                <div>
                                    <i class="fas fa-hand-holding-usd fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small">Net Amount</div>
                                    <div class="text-lg fw-bold">
                                        Rp {{ number_format($summary['net_amount'], 0, ',', '.') }}</div>
                                </div>
                                <div>
                                    <i class="fas fa-chart-line fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-info text-white mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small">Current Balance</div>
                                    <div class="text-lg fw-bold">
                                        Rp {{ number_format($summary['current_balance'], 0, ',', '.') }}</div>
                                </div>
                                <div>
                                    <i class="fas fa-wallet fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="financialTable">
                    <thead class="table-light">
                    <tr>
                        <th class="text-center">Date</th>
                        <th class="text-center">Type</th>
                        <th class="text-center">Amount</th>
                        <th>Source</th>
                        <th class="text-center">Previous Balance</th>
                        <th class="text-center">Current Balance</th>
                        <th>Created By</th>
                        <th>Notes</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($mutations as $mutation)
                        <tr>
                            <td class="text-center">{{ $mutation->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                    <span class="badge bg-{{ $mutation->type === 'in' ? 'success' : 'danger' }}">
                                        {{ strtoupper($mutation->type) }}
                                    </span>
                            </td>
                            <td class="text-end">Rp {{ number_format($mutation->amount, 0, ',', '.') }}</td>
                            <td>
                                {{ ucwords(str_replace('_', ' ', $mutation->source_type)) }}
                                @if($mutation->source_id)
                                    #{{ $mutation->source_id }}
                                @endif
                            </td>
                            <td class="text-end">Rp {{ number_format($mutation->previous_balance, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($mutation->current_balance, 0, ',', '.') }}</td>
                            <td>{{ $mutation->createdBy->name }}</td>
                            <td>{{ $mutation->notes }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No transactions found</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#financialTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel me-1"></i> Excel',
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                        className: 'btn btn-danger btn-sm',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print me-1"></i> Print',
                        className: 'btn btn-primary btn-sm',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ],
                pageLength: 25,
                order: [[0, 'desc']],
                language: {
                    search: "",
                    searchPlaceholder: "Search transactions..."
                }
            });
        });
    </script>

    <style>
        @media print {
            .breadcrumb, .card-header, form, .dataTables_filter, .dataTables_length, .dataTables_paginate, .dt-buttons {
                display: none !important;
            }
        }
    </style>
@endpush
