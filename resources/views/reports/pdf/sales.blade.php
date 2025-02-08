<!DOCTYPE html>
<html>

<head>
    <title>Laporan Penjualan</title>
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
    <h2>Laporan Penjualan</h2>
    <table>
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
                <tr>
                    <td>{{ $sale->invoice_number }}</td>
                    <td>{{ $sale->invoice_date }}</td>
                    <td>{{ $sale->customer->name }}</td>
                    <td>{{ number_format($sale->final_amount) }}</td>
                    <td>{{ $sale->payment_type }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" align="right"><strong>Total</strong></td>
                <td colspan="2"><strong>{{ number_format($sales->sum('final_amount')) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
