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
            /* Tambahkan margin untuk mencegah pemotongan */
            margin: 0mm 1mm; /* 0mm atas/bawah, 1mm kiri/kanan */
            /* Explicitly set both width and height */
            size: {{ $setting->paper_size ?? '78mm' }} auto;
        }

        /* Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            /* Font size lebih kecil untuk memastikan konten muat */
            font-size: {{ (int) $setting->paper_size < 70 ? '9px' : '10px' }};
            /* Kurangi lebar body untuk memberikan ruang margin yang cukup */
            width: 100%;
            max-width: {{ (int) $setting->paper_size - 8 }}mm;
            margin: 0 auto; /* Tambahkan auto margin untuk center content */
        }

        /* Container Settings - pastikan lebih kecil dari ukuran kertas */
        .invoice-box {
            width: 100%;
            /* Kurangi lebar untuk mencegah pemotongan */
            max-width: {{ (int) $setting->paper_size - 10 }}mm;
            margin: 0 auto;
            padding: 2px 4px 2px 2px; /* Tambahkan padding kanan lebih besar: top right bottom left */
            overflow: hidden;
        }

        /* Header Section */
        .header {
            text-align: center;
            margin-bottom: 8px;
            padding: 0 1px; /* Kurangi padding horizontal */
        }

        .company-name {
            font-weight: bold;
            margin-bottom: 2px;
            font-size: {{ (int) $setting->paper_size < 70 ? '11px' : '12px' }};
        }

        /* Info Section */
        .info {
            margin-bottom: 8px;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 4px 0;
        }

        .info-row {
            display: grid;
            grid-template-columns: 40% 55%; /* Left 40%, right 55%, dengan 5% gap */
            font-size: {{ (int) $setting->paper_size < 70 ? '9px' : '10px' }};
            margin-bottom: 2px;
        }

        .info-row span:last-child {
            text-align: right;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Table Styles - membuat table lebih kompak */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            table-layout: fixed;
            font-size: {{ (int) $setting->paper_size < 70 ? '8px' : '9px' }};
        }

        th, td {
            text-align: left;
            padding: {{ (int) $setting->paper_size < 70 ? '1px 0' : '1px 0' }}; /* Kurangi padding */
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Untuk item nama produk, izinkan wrap text */
        td:first-child {
            white-space: normal;
            word-break: break-word;
        }

        th {
            border-bottom: 1px dashed #000;
        }

        /* Column Widths - sesuaikan untuk kertas 78mm */
        th:nth-child(1), td:nth-child(1) { width: 36%; } /* Item - kurangi sedikit */
        th:nth-child(2), td:nth-child(2) { width: 12%; } /* Qty */
        th:nth-child(3), td:nth-child(3) { width: 19%; } /* Harga - kurangi sedikit */
        th:nth-child(4), td:nth-child(4) { width: 28%; } /* Total - kurangi sedikit */
        /* Total % sekarang adalah 95%, memberi ruang tambahan 5% */

        /* Totals Section */
        .totals {
            margin-top: 8px;
            border-top: 1px dashed #000;
            padding-top: 4px;
        }

        .total-row {
            display: grid;
            grid-template-columns: 40% 55%; /* Left 40%, right 55%, dengan 5% gap */
            margin-bottom: 2px;
            font-size: {{ (int) $setting->paper_size < 70 ? '9px' : '10px' }};
        }

        .total-row span:last-child {
            text-align: right;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .total-row.grand-total {
            font-weight: bold;
            font-size: {{ (int) $setting->paper_size < 70 ? '10px' : '11px' }};
            margin: 4px 0;
        }

        .total-row.grand-total span:last-child {
            /* Pastikan total yang penting tidak terpotong */
            padding-right: 1mm;
        }

        /* Footer Section */
        .footer {
            text-align: center;
            margin-top: 8px;
            padding-top: 4px;
            border-top: 1px dashed #000;
            font-size: 9px;
        }

        .footer p {
            margin-bottom: 2px;
        }

        /* Print Specific Styles */
        @media print {
            body {
                margin: 0 auto;
                padding: 0;
                /* Kurangi lebar untuk memberikan ruang margin tambahan */
                width: {{ (int) $setting->paper_size - 10 }}mm;
            }

            .invoice-box {
                border: none;
                width: 100%;
                /* Tambahkan padding kanan lebih besar untuk mencegah pemotongan */
                padding: 0 5px 0 1px; /* top right bottom left */
            }

            /* Pastikan info dan total row tidak terpotong saat print */
            .info-row span:last-child,
            .total-row span:last-child {
                padding-right: 2mm;
            }

            .no-print {
                display: none;
            }
        }

        /* Helper Classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .mt-1 { margin-top: 4px; }
        .mb-1 { margin-bottom: 4px; }

        /* Print Button Styles */
        .no-print {
            text-align: center;
            margin-top: 20px;
        }

        .no-print button {
            padding: 8px 16px;
            margin: 0 5px;
            cursor: pointer;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #fff;
        }

        .no-print button:hover {
            background: #f5f5f5;
        }
    </style>

</head>

<body>
<div class="invoice-box">
    <div class="header">
        <div class="company-name">{{ $company['name'] }}</div>
        <div>{{ $company['address'] }}</div>
        <div>{{ $company['phone'] }}</div>
    </div>

    <div class="info">
        <div class="info-row">
            <span>No. Invoice:</span>
            <span>#{{ $transaction->invoice_number }}</span>
        </div>
        <div class="info-row">
            <span>Tanggal:</span>
            <span>{{ $transaction->invoice_date->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span>Kasir:</span>
            <span>{{ $transaction->user->name }}</span>
        </div>
        @if($transaction->customer)
            <div class="info-row">
                <span>Customer:</span>
                <span>{{ $transaction->customer->name }}</span>
            </div>
        @endif
    </div>

    <table>
        <thead>
        <tr>
            <th>Item</th>
            <th>Qty</th>
            <th>Harga</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($transaction->items as $item)
            <tr>
                <td>
                    {{ $item->product->name }}
                    @if($item->discount > 0)
                        <br><small>Disc: {{ $item->discount }}</small>
                    @endif
                </td>
                <td>{{ number_format($item->quantity, 0) }}</td>
                <td>{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td>{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
        </div>
        @if($transaction->tax_amount > 0)
            <div class="total-row">
                <span>Pajak:</span>
                <span>{{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
            </div>
        @endif
        @if($transaction->discount_amount > 0)
            <div class="total-row">
                <span>Diskon:</span>
                <span>{{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
            </div>
        @endif
        <div class="total-row grand-total">
            <span>Total:</span>
            <span>{{ number_format($transaction->final_amount, 0, ',', '.') }}</span>
        </div>

        <div class="total-row">
            <span>Pembayaran:</span>
            <span>{{ strtoupper($transaction->payment_type) }}</span>
        </div>

        @if($transaction->payment_type === 'cash')
            <div class="total-row">
                <span>Tunai:</span>
                <span>{{ number_format($transaction->cash_amount, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span>Kembalian:</span>
                <span>{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
        @endif

        @if($transaction->reference_number)
            <div class="total-row">
                <span>No. Ref:</span>
                <span>{{ $transaction->reference_number }}</span>
            </div>
        @endif
    </div>

    <div class="footer">
        <p>Terima kasih telah berbelanja</p>
        <p>Barang yang sudah dibeli tidak dapat dikembalikan</p>
    </div>
</div>

<div class="no-print" style="text-align: center; margin-top: 20px;">
    <button onclick="window.print()">Print Invoice</button>
    <button onclick="window.close()">Tutup</button>
</div>

<script>
    window.onload = function() {
        @if($setting->auto_print)
        // Hanya print otomatis jika setting auto_print aktif
        setTimeout(function() {
            window.print();
        }, 500);
        @endif
    };
</script>
</body>
</html>
