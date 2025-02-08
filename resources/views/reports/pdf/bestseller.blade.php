<!-- resources/views/reports/pdf/bestseller.blade.php -->
<!DOCTYPE html>
<html>

<head>
    <title>Laporan Produk Terlaris</title>
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
    <h2>Laporan Produk Terlaris</h2>
    <p>Periode: {{ $startDate }} - {{ $endDate }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Produk</th>
                <th>Total Quantity</th>
                <th>Total Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['product']->name }}</td>
                    <td>{{ number_format($item['total_quantity']) }}</td>
                    <td>{{ number_format($item['total_amount']) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" align="right"><strong>Total</strong></td>
                <td><strong>{{ number_format($products->sum('total_quantity')) }}</strong></td>
                <td><strong>{{ number_format($products->sum('total_amount')) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
