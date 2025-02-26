<!-- resources/views/pos/invoice.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $transaction->invoice_number }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* Page Settings - Optimized for Thermal Printing */
        @page {
            margin: 0;
            size: {{ request('size') == '57' ? '57mm' : '78mm' }} auto;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: {{ request('size') == '57' ? '11px' : '13px' }};
            width: {{ request('size') == '57' ? '57mm' : '78mm' }};
            color: #000;
            background-color: white;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            padding-right: 5mm;
        }

        .invoice-box {
            padding: 5px;
            width: 100%;
            max-width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 8px;
        }

        .company-name {
            font-weight: bold;
            font-size: {{ request('size') == '57' ? '14px' : '16px' }};
            text-transform: uppercase;
        }

        .company-details {
            font-size: {{ request('size') == '57' ? '9px' : '11px' }};
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: {{ request('size') == '57' ? '9px' : '11px' }};
            margin-bottom: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }

        th, td {
            font-size: {{ request('size') == '57' ? '9px' : '11px' }};
            padding: 3px 2px;
        }

        th {
            border-bottom: 1px solid #000;
        }

        .col-item {
            width: 40%;
            word-break: break-word;
        }

        .col-qty {
            width: 15%;
            text-align: center;
        }

        .col-price, .col-total {
            width: 22%;
            text-align: right;
        }

        .totals-section {
            margin-top: 6px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: {{ request('size') == '57' ? '9px' : '11px' }};
        }

        .grand-total {
            font-weight: bold;
            font-size: {{ request('size') == '57' ? '12px' : '14px' }};
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 4px 0;
        }

        .payment-method {
            text-align: center;
            font-weight: bold;
            font-size: {{ request('size') == '57' ? '10px' : '12px' }};
            margin: 6px 0;
        }

        .footer {
            text-align: center;
            font-size: {{ request('size') == '57' ? '9px' : '11px' }};
            margin-top: 8px;
        }

        .qr-container {
            text-align: center;
            margin: 6px 0;
        }

        .qr-code {
            width: 70%;
            height: auto;
        }

        @media print {
            html, body {
                width: {{ request('size') == '57' ? '57mm' : '78mm' }};
            }

            .no-print {
                display: none;
            }

            body::after {
                content: "";
                display: block;
                height: 5mm;
            }
        }

        .no-print {
            text-align: center;
            margin-top: 15px;
        }

        .no-print button {
            padding: 8px;
            cursor: pointer;
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

    <div class="info-row">
        <div>No. Invoice:</div>
        <div>#{{ $transaction->invoice_number }}</div>
    </div>
    <div class="info-row">
        <div>Tanggal:</div>
        <div>{{ $transaction->invoice_date->format('d/m/Y') }}</div>
    </div>
    <div class="info-row">
        <div>Kasir:</div>
        <div>{{ $transaction->user->name }}</div>
    </div>

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
                <td class="col-qty">{{ $item->quantity }}</td>
                <td class="col-price">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="col-total">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <hr class="divider">

    <div class="grand-total">
        <div>TOTAL</div>
        <div>Rp {{ number_format($transaction->final_amount, 0, ',', '.') }}</div>
    </div>

    <div class="payment-method">{{ strtoupper($transaction->payment_type) }}</div>

    @if($transaction->payment_type === 'cash')
        <div class="total-row">
            <div class="total-label">Tunai:</div>
            <div class="total-value">{{ number_format($transaction->cash_amount, 0, ',', '.') }}</div>
        </div>

        <div class="total-row">
            <div class="total-label">Kembalian:</div>
            <div class="total-value">{{ number_format($transaction->change_amount, 0, ',', '.') }}</div>
        </div>
    @endif

    @if($transaction->reference_number)
        <div class="total-row">
            <div class="total-label">No. Referensi:</div>
            <div class="total-value">{{ $transaction->reference_number }}</div>
        </div>
    @endif

    <div class="footer">
        <div>TERIMA KASIH TELAH BERBELANJA</div>
    </div>
</div>

<div class="no-print">
    <button onclick="window.print()">Print</button>
    <button onclick="window.close()">Tutup</button>
</div>
</body>
</html>
