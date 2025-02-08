<!-- resources/views/reports/pdf/profit.blade.php -->
<!DOCTYPE html>
<html>

<head>
    <title>Laporan Keuntungan</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
        }
    </style>
</head>

<body>
    <h2>Laporan Keuntungan</h2>
    <p>Periode: {{ $startDate }} - {{ $endDate }}</p>

    <table>
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Tanggal</th>
                <th>Pendapatan</th>
                <th>Modal</th>
                <th>Keuntungan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($profits as $profit)
                <tr>
                    <td>{{ $profit['invoice_number'] }}</td>
                    <td>{{ $profit['date'] }}</td>
                    <td>{{ number_format($profit['revenue']) }}</td>
                    <td>{{ number_format($profit['cost']) }}</td>
                    <td>{{ number_format($profit['profit']) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" align="right"><strong>Total</strong></td>
                <td><strong>{{ number_format($profits->sum('revenue')) }}</strong></td>
                <td><strong>{{ number_format($profits->sum('cost')) }}</strong></td>
                <td><strong>{{ number_format($profits->sum('profit')) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
