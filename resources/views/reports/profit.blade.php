<!-- resources/views/reports/profit.blade.php -->
@extends('reports.layout')

@section('report-title', 'Laporan Keuntungan')

@section('report-filters')
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control"
                    value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" onchange="this.form.submit()">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control"
                    value="{{ request('end_date', now()->format('Y-m-d')) }}" onchange="this.form.submit()">
            </div>
        </div>
    </form>
@endsection

@section('report-content')
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Tanggal</th>
                    <th>Pendapatan</th>
                    <th>Modal</th>
                    <th>Keuntungan</th>
                    <th>Margin</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($profits as $profit)
                    <tr>
                        <td>{{ $profit['invoice_number'] }}</td>
                        <td>{{ Carbon\Carbon::parse($profit['date'])->format('Y-m-d H:i') }}</td>
                        <td class="text-end">{{ number_format($profit['revenue']) }}</td>
                        <td class="text-end">{{ number_format($profit['cost']) }}</td>
                        <td class="text-end">{{ number_format($profit['profit']) }}</td>
                        <td class="text-end">
                            {{ number_format(($profit['profit'] / $profit['revenue']) * 100, 2) }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-end"><strong>Total</strong></td>
                    <td class="text-end">
                        <strong>{{ number_format($profits->sum('revenue')) }}</strong>
                    </td>
                    <td class="text-end">
                        <strong>{{ number_format($profits->sum('cost')) }}</strong>
                    </td>
                    <td class="text-end">
                        <strong>{{ number_format($profits->sum('profit')) }}</strong>
                    </td>
                    <td class="text-end">
                        <strong>
                            {{ number_format(($profits->sum('profit') / $profits->sum('revenue')) * 100, 2) }}%
                        </strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection
