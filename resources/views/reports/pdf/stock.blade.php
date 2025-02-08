<!-- resources/views/reports/pdf/stock.blade.php -->
<!DOCTYPE html>
<html>

<head>
    <title>Laporan Stok</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
        }

        th {
            background-color: #f0f0f0;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .status-low {
            color: red;
            font-weight: bold;
        }

        .status-normal {
            color: green;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .date {
            text-align: right;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <h2>Laporan Stok</h2>
    <div class="date">Tanggal: {{ now()->format('d/m/Y H:i') }}</div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Produk</th>
                <th>Kategori</th>
                <th>Unit</th>
                <th>Stok</th>
                <th>Min Stok</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stocks as $index => $stock)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $stock->product->name }}</td>
                    <td>{{ $stock->product->category->name }}</td>
                    <td>{{ $stock->unit->name }}</td>
                    <td class="text-right">{{ number_format($stock->quantity) }}</td>
                    <td class="text-right">{{ number_format($stock->min_stock) }}</td>
                    <td class="text-center {{ $stock->quantity <= $stock->min_stock ? 'status-low' : 'status-normal' }}">
                        {{ $stock->quantity <= $stock->min_stock ? 'Stok Rendah' : 'Normal' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right"><strong>Total Jenis Produk</strong></td>
                <td colspan="3" class="text-center"><strong>{{ $stocks->count() }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px">
        <p><strong>Keterangan Status:</strong></p>
        <p class="status-normal">Normal: Stok di atas minimum</p>
        <p class="status-low">Stok Rendah: Stok sama dengan atau di bawah minimum</p>
    </div>
</body>

</html>
