<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $transaction->invoice_number }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        @page {
            margin: 0;
            size: {{ request('size') == '57' ? '58mm' : '80mm' }} auto;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: {{ request('size') == '57' ? '11px' : '13px' }};
            width: {{ request('size') == '57' ? '58mm' : '80mm' }};
            color: #000;
            padding: 0 2mm;
        }

        .invoice-box {
            padding: 4px;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
        }

        .company-name {
            font-size: {{ request('size') == '57' ? '13px' : '15px' }};
            text-transform: uppercase;
        }

        .company-details {
            font-size: {{ request('size') == '57' ? '10px' : '12px' }};
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 3px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: {{ request('size') == '57' ? '10px' : '12px' }};
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            font-size: {{ request('size') == '57' ? '10px' : '12px' }};
            padding: 2px;
        }

        th {
            border-bottom: 1px solid #000;
        }

        .col-item { width: 45%; }
        .col-qty { width: 15%; text-align: center; }
        .col-price, .col-total { width: 20%; text-align: right; }

        .grand-total {
            font-weight: bold;
            font-size: {{ request('size') == '57' ? '12px' : '14px' }};
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 3px 0;
            text-align: right;
        }

        .footer {
            text-align: center;
            font-size: {{ request('size') == '57' ? '10px' : '12px' }};
            margin-top: 5px;
        }

        @media print {
            html, body {
                width: {{ request('size') == '57' ? '58mm' : '80mm' }};
            }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
<div class="invoice-box">
    <div class="header">
        <div class="company-name">{{ $company['name'] }}</div>
        <div class="company-details">{{ $company['address'] }}</div>
        <div class="company-details">Tel: {{ $company['phone'] }}</div>
    </div>

    <hr class="divider">

    <div class="info-row"><div>No. Invoice:</div><div>#{{ $transaction->invoice_number }}</div></div>
    <div class="info-row"><div>Tanggal:</div><div>{{ $transaction->invoice_date->format('d/m/Y') }}</div></div>
    <div class="info-row"><div>Kasir:</div><div>{{ $transaction->user->name }}</div></div>

    @if($transaction->customer)
        <div class="info-row"><div>Pelanggan:</div><div>{{ $transaction->customer->name }}</div></div>
    @endif

    <hr class="divider">

    <table>
        <thead>
        <tr>
            <th class="col-item">Item</th>
            <th class="col-qty">Qty</th>
            <th class="col-price">Harga</th>
            <th class="col-total">Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($transaction->items as $item)
            <tr>
                <td class="col-item">{{ $item->product->name }}</td>
                <td class="col-qty">{{ number_format($item->quantity) }}</td>
                <td class="col-price">{{ number_format($item->unit_price) }}</td>
                <td class="col-total">{{ number_format($item->subtotal) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="grand-total">Rp {{ number_format($transaction->final_amount, 0, ',', '.') }}</div>

    <div class="footer">TERIMA KASIH TELAH BERBELANJA</div>
</div>

<div class="no-print">
    <button onclick="window.print()">Print</button>
    <button onclick="window.close()">Tutup</button>
</div>
</body>
</html>
