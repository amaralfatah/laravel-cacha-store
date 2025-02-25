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
            size: {{ $setting->paper_size ?? (request('size') == '57' ? '57mm' : '78mm') }} auto;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            /* Solid font optimized for thermal printers */
            font-family: 'Arial', 'Helvetica', sans-serif;
            line-height: 1.2;
            margin: 0;
            padding: 0;
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '10px' : '12px' }};
            width: 100%;
            max-width: {{ (int) ($setting->paper_size ?? 78) }}mm;
            /* Solid black for clearer printing */
            color: #000000;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            background-color: white !important;
        }

        .invoice-box {
            width: 100%;
            max-width: {{ (int) ($setting->paper_size ?? 78) }}mm;
            padding: 3px;
        }

        .header {
            text-align: center;
            margin-bottom: 6px;
        }

        .company-name {
            font-weight: 900;
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '12px' : '14px' }};
            margin-bottom: 2px;
            text-transform: uppercase;
            /* Text stroke for bolder printing */
            -webkit-text-stroke: 0.3px black;
            letter-spacing: 0.5px;
        }

        .company-details {
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '8px' : '10px' }};
            line-height: 1.2;
            font-weight: 600;
        }

        .divider {
            border: none;
            border-top: 1px dashed #000;
            margin: 4px 0;
            clear: both;
        }

        .info-section {
            margin-bottom: 4px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1px;
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '8px' : '10px' }};
        }

        .info-label {
            font-weight: 700;
            width: 40%;
        }

        .info-value {
            text-align: right;
            width: 60%;
            font-weight: 500;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0;
            table-layout: fixed;
        }

        th {
            text-align: left;
            padding: 2px 1px;
            font-weight: 700;
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '8px' : '10px' }};
            border-bottom: 1px solid #000;
        }

        td {
            padding: 3px 1px;
            vertical-align: top;
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '8px' : '10px' }};
        }

        tr.item-row:not(:last-child) {
            border-bottom: 1px dotted #ccc;
        }

        .item-name {
            font-weight: 600;
            word-break: keep-all;
            overflow-wrap: break-word;
            line-height: 1.2;
        }

        .discount-info {
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '7px' : '9px' }};
            font-weight: 600;
            padding-top: 1px;
        }

        /* Column widths - optimized for readability */
        @if((int) ($setting->paper_size ?? 78) < 70)
        .col-item { width: 40%; }
        .col-qty { width: 15%; text-align: center; }
        .col-price { width: 20%; text-align: right; }
        .col-total { width: 25%; text-align: right; }
        @else
        .col-item { width: 45%; }
        .col-qty { width: 15%; text-align: center; }
        .col-price { width: 18%; text-align: right; }
        .col-total { width: 22%; text-align: right; }
        @endif

        .totals-section {
            margin-top: 4px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '8px' : '10px' }};
        }

        .total-label {
            font-weight: 700;
            width: 50%;
        }

        .total-value {
            text-align: right;
            width: 50%;
            font-weight: 600;
        }

        .grand-total {
            font-weight: 900;
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '11px' : '13px' }};
            margin: 5px 0;
            display: flex;
            justify-content: space-between;
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
            padding: 3px 0;
            letter-spacing: 0.5px;
        }

        .payment-method {
            font-weight: 700;
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '9px' : '11px' }};
            margin: 5px 0;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .footer {
            text-align: center;
            margin-top: 8px;
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '8px' : '10px' }};
        }

        .footer-message {
            font-weight: 700;
            margin-bottom: 2px;
            letter-spacing: 0.3px;
        }

        .footer-policy {
            font-weight: 500;
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '7px' : '9px' }};
            line-height: 1.2;
            margin-bottom: 2px;
        }

        /* QR Code center alignment */
        .qr-container {
            text-align: center;
            margin: 5px 0;
        }

        .qr-code {
            margin: 0 auto;
            display: block;
            max-width: {{ (int) ($setting->paper_size ?? 78) < 70 ? '80%' : '90%' }};
            height: auto;
        }

        .qr-text {
            font-size: {{ (int) ($setting->paper_size ?? 78) < 70 ? '7px' : '8px' }};
            text-align: center;
            margin-top: 2px;
        }

        /* Print settings */
        @media print {
            html, body {
                width: {{ (int) ($setting->paper_size ?? (request('size') == '57' ? '57' : '78')) }}mm;
                max-width: {{ (int) ($setting->paper_size ?? (request('size') == '57' ? '57' : '78')) }}mm;
                font-weight: 500;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }

            /* Ensure numbers print clearly */
            .total-value, .col-price, .col-total, .col-qty {
                font-weight: 600 !important;
                letter-spacing: 0.3px !important;
            }

            /* Make borders darker */
            .divider, th, .grand-total {
                border-color: #000 !important;
            }

            /* Fix for some thermal printers that cut off content */
            body::after {
                content: "";
                display: block;
                height: 5mm;
            }
        }

        .no-print {
            text-align: center;
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #ccc;
        }

        .no-print button {
            padding: 8px 15px;
            margin: 0 5px;
            cursor: pointer;
            border: 1px solid #333;
            border-radius: 4px;
            background: #f8f8f8;
            font-weight: bold;
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

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">No. Invoice:</div>
            <div class="info-value">#{{ $transaction->invoice_number }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal:</div>
            <div class="info-value">{{ $transaction->invoice_date->format('d/m/Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Waktu:</div>
            <div class="info-value">{{ $transaction->invoice_date->format('H:i') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Kasir:</div>
            <div class="info-value">{{ $transaction->user->name }}</div>
        </div>
        @if($transaction->customer)
            <div class="info-row">
                <div class="info-label">Pelanggan:</div>
                <div class="info-value">{{ $transaction->customer->name }}</div>
            </div>
        @endif
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

    <hr class="divider">

    <div class="totals-section">
        <div class="total-row">
            <div class="total-label">Subtotal:</div>
            <div class="total-value">{{ number_format($transaction->total_amount, 0, ',', '.') }}</div>
        </div>

        @if($transaction->tax_amount > 0)
            <div class="total-row">
                <div class="total-label">Pajak ({{ $transaction->tax_percentage ?? 11 }}%):</div>
                <div class="total-value">{{ number_format($transaction->tax_amount, 0, ',', '.') }}</div>
            </div>
        @endif

        @if($transaction->discount_amount > 0)
            <div class="total-row">
                <div class="total-label">Diskon:</div>
                <div class="total-value">{{ number_format($transaction->discount_amount, 0, ',', '.') }}</div>
            </div>
        @endif

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
    </div>

    <!-- Barcode untuk referensi cepat -->
    <div class="qr-container">
        <img class="qr-code" src="data:image/png;base64,{{ DNS1D::getBarcodePNG($transaction->invoice_number, 'C128', 2, 30) }}" alt="Barcode">
        <div class="qr-text">{{ $transaction->invoice_number }}</div>
    </div>

    <hr class="divider">

    <div class="footer">
        <div class="footer-message">TERIMA KASIH TELAH BERBELANJA</div>
        <div class="footer-policy">Barang yang sudah dibeli tidak dapat dikembalikan</div>
    </div>
</div>

<div class="no-print">
    <button onclick="window.print()">Print Invoice</button>
    <button onclick="window.close()">Tutup</button>
</div>

<script>
    window.onload = function() {
        // Menambahkan timeout lebih lama untuk memastikan semua elemen terload sempurna
        @if(isset($setting->auto_print) && $setting->auto_print)
        setTimeout(function() {
            window.print();
            // Tambahkan callback setelah cetak selesai jika perlu
            // Pada beberapa browser, ini akan tereksekusi setelah dialog cetak ditutup
            if (window.matchMedia) {
                var mediaQueryList = window.matchMedia('print');
                mediaQueryList.addListener(function(mql) {
                    if (!mql.matches) {
                        // Cetak selesai atau dibatalkan
                        console.log('Cetak selesai');
                    }
                });
            }
        }, 1200);
        @endif
    };
</script>
</body>
</html>
