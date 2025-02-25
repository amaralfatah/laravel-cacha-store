<!-- resources/views/pos/invoice.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $transaction->invoice_number }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* Page Settings */
        @page {
            margin: 0;
            size: {{ $setting->paper_size ?? (request('size') == '57' ? '57mm' : '78mm') }} auto;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace; /* Font standar untuk printer termal */
            line-height: 1.2;
            margin: 0;
            padding: 0;
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '9px' : '11px' }};
            width: 100%;
            max-width: {{ (int) ($setting->paper_size ?? 78) }}mm;
        }

        .invoice-box {
            width: 100%;
            max-width: {{ (int) ($setting->paper_size ?? 78) }}mm;
            padding: 3px;
        }

        .header {
            text-align: center;
            margin-bottom: 8px;
        }

        .company-logo {
            margin-bottom: 5px;
            max-width: 100%;
            height: auto;
        }

        .company-name {
            font-weight: bold;
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '12px' : '16px' }};
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .company-details {
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '8px' : '10px' }};
            line-height: 1.2;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        .divider {
            border: none;
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .info-section {
            margin-bottom: 8px;
        }

        .info {
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '8px' : '10px' }};
            display: grid;
            grid-template-columns: {{ (int) ($setting->paper_size ?? 78) < 70 ? '30% 70%' : '35% 65%' }};
            row-gap: 2px;
        }

        .info-label {
            font-weight: bold;
        }

        .info-value {
            text-align: right;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
            table-layout: fixed;
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '8px' : '10px' }};
        }

        thead {
            border-bottom: 1px solid #000;
        }

        th {
            text-align: left;
            padding: 3px 0;
            font-weight: bold;
            text-transform: uppercase;
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '8px' : '10px' }};
        }

        td {
            padding: 3px 1px;
            vertical-align: top;
        }

        tr.item-row {
            border-bottom: 1px dotted #eee;
        }

        .item-name {
            word-break: break-word;
            white-space: normal;
            line-height: 1.2;
            max-width: 100%;
            overflow-wrap: break-word;
            font-weight: {{ (int) ($setting->paper_size ?? 78) < 70 ? 'normal' : 'bold' }};
        }

        .discount-info {
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '7px' : '9px' }};
            font-style: italic;
        }

        /* Column widths - Responsif berdasarkan ukuran kertas */
        @if((int) ($setting->paper_size ?? 78) < 70)
        .col-item { width: 38%; }
        .col-qty { width: 15%; text-align: center; }
        .col-price { width: 20%; text-align: right; }
        .col-total { width: 22%; text-align: right; }
        @else
        .col-item { width: 42%; }
        .col-qty { width: 13%; text-align: center; }
        .col-price { width: 20%; text-align: right; }
        .col-total { width: 22%; text-align: right; }
        @endif

        .totals-section {
            margin-top: 5px;
        }

        .totals {
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '8px' : '10px' }};
            display: grid;
            grid-template-columns: 50% 50%;
            row-gap: 2px;
        }

        .total-label {
            font-weight: bold;
            text-align: left;
        }

        .total-value {
            text-align: right;
        }

        .grand-total {
            font-weight: bold;
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '11px' : '14px' }};
            margin: 5px 0;
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 3px 0;
        }

        .payment-info {
            margin-top: 5px;
            padding-top: 3px;
        }

        .payment-method {
            font-weight: bold;
            text-transform: uppercase;
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '9px' : '11px' }};
            margin-bottom: 2px;
            text-align: center;
        }

        .barcode {
            text-align: center;
            margin: 8px 0;
        }

        .barcode img {
            max-width: 90%;
            height: auto;
        }

        .footer {
            text-align: center;
            margin-top: 8px;
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '8px' : '10px' }};
        }

        .footer-message {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .footer-policy {
            font-style: italic;
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '7px' : '9px' }};
            line-height: 1.2;
        }

        .contact-info {
            margin-top: 5px;
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '7px' : '9px' }};
        }

        /* Print settings */
        @media print {
            html, body {
                width: {{ (int) ($setting->paper_size ?? (request('size') == '57' ? '57' : '78')) }}mm;
                max-width: {{ (int) ($setting->paper_size ?? (request('size') == '57' ? '57' : '78')) }}mm;
            }

            .no-print {
                display: none;
            }
        }

        .no-print {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
        }

        .no-print button {
            padding: 8px 15px;
            margin: 0 5px;
            cursor: pointer;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f8f8f8;
            font-weight: bold;
            transition: all 0.3s;
        }

        .no-print button:hover {
            background: #ebebeb;
        }
    </style>
</head>

<body>
<div class="invoice-box">
    <div class="header">
        <!-- Tambahan logo jika ada -->
        <!--<div class="company-logo">
            <img src="{{ asset('/img/logo.png') }}" alt="Logo" style="max-width: 100%; max-height: 50px;">
        </div>-->
        <div class="company-name">{{ $company['name'] }}</div>
        <div class="company-details">{{ $company['address'] }}</div>
        <div class="company-details">Tel: {{ $company['phone'] }}</div>
    </div>

    <hr class="divider">

    <div class="info-section">
        <div class="info">
            <div class="info-label">No. Invoice:</div>
            <div class="info-value">#{{ $transaction->invoice_number }}</div>

            <div class="info-label">Tanggal:</div>
            <div class="info-value">{{ $transaction->invoice_date->format('d/m/Y') }}</div>

            <div class="info-label">Waktu:</div>
            <div class="info-value">{{ $transaction->invoice_date->format('H:i') }}</div>

            <div class="info-label">Kasir:</div>
            <div class="info-value">{{ $transaction->user->name }}</div>

            @if($transaction->customer)
                <div class="info-label">Pelanggan:</div>
                <div class="info-value">{{ $transaction->customer->name }}</div>
            @endif
        </div>
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
            <tr class="item-row">
                <td class="col-item">
                    <div class="item-name">{{ $item->product->name }}</div>
                    @if($item->discount > 0)
                        <div class="discount-info">Disc: {{ $item->discount }}</div>
                    @endif
                </td>
                <td class="col-qty">{{ $item->quantity }} {{ isset($item->unit) ? $item->unit->short_name : '' }}</td>
                <td class="col-price">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="col-total">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="totals-section">
        <div class="totals">
            <div class="total-label">Subtotal:</div>
            <div class="total-value">{{ number_format($transaction->total_amount, 0, ',', '.') }}</div>

            @if($transaction->tax_amount > 0)
                <div class="total-label">Pajak ({{ $transaction->tax_percentage ?? 11 }}%):</div>
                <div class="total-value">{{ number_format($transaction->tax_amount, 0, ',', '.') }}</div>
            @endif

            @if($transaction->discount_amount > 0)
                <div class="total-label">Diskon:</div>
                <div class="total-value">{{ number_format($transaction->discount_amount, 0, ',', '.') }}</div>
            @endif
        </div>

        <div class="grand-total">
            <div>TOTAL</div>
            <div>Rp {{ number_format($transaction->final_amount, 0, ',', '.') }}</div>
        </div>

        <div class="payment-info">
            <div class="payment-method">{{ strtoupper($transaction->payment_type) }}</div>

            <div class="totals">
                @if($transaction->payment_type === 'cash')
                    <div class="total-label">Tunai:</div>
                    <div class="total-value">{{ number_format($transaction->cash_amount, 0, ',', '.') }}</div>

                    <div class="total-label">Kembalian:</div>
                    <div class="total-value">{{ number_format($transaction->change_amount, 0, ',', '.') }}</div>
                @endif

                @if($transaction->reference_number)
                    <div class="total-label">No. Referensi:</div>
                    <div class="total-value">{{ $transaction->reference_number }}</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Barcode atau QR Code untuk invoice -->
    <div class="barcode">
        {!! DNS1D::getBarcodeHTML($transaction->invoice_number, 'C128', 1, 30) !!}
        <div style="font-size: 8px; margin-top: 2px;">{{ $transaction->invoice_number }}</div>
    </div>

    <hr class="divider">

    <div class="footer">
        <div class="footer-message">Terima kasih telah berbelanja</div>
        <div class="footer-policy">Barang yang sudah dibeli tidak dapat dikembalikan</div>
        <div class="contact-info">www.tokoanda.com | info@tokoanda.com</div>
    </div>
</div>

<div class="no-print">
    <button onclick="window.print()">Print Invoice</button>
    <button onclick="window.close()">Tutup</button>
</div>

<script>
    window.onload = function() {
        @if(isset($setting->auto_print) && $setting->auto_print)
        setTimeout(function() {
            window.print();
        }, 800); // Timeout yang lebih lama untuk memastikan semua elemen terload
        @endif
    };
</script>
</body>
</html>
