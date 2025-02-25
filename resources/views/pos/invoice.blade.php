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
            line-height: 1.4;
            margin: 0;
            padding: 0;
            /* Font size akan diatur secara dinamis berdasarkan ukuran kertas */
            font-size: {{ (int) $setting->paper_size < 70 ? '10px' : '12px' }};
        }

        /* Container Settings */
        .invoice-box {
            width: 100%;
            margin: 0 auto;
            padding: 5px;
            max-width: {{ $setting->paper_size ?? '78mm' }};
        }

        /* Header Section */
        .header {
            text-align: center;
            margin-bottom: 10px;
            padding: 0 5px;
        }

        .company-name {
            font-weight: bold;
            margin-bottom: 3px;
            font-size: {{ (int) $setting->paper_size < 70 ? '12px' : '14px' }};
        }

        /* Info Section */
        .info {
            margin-bottom: 10px;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: {{ (int) $setting->paper_size < 70 ? '10px' : '11px' }};
            margin-bottom: 2px;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: {{ (int) $setting->paper_size < 70 ? '9px' : '11px' }};
        }

        th, td {
            text-align: left;
            padding: {{ (int) $setting->paper_size < 70 ? '2px 1px' : '3px 2px' }};
        }

        th {
            border-bottom: 1px dashed #000;
        }

        /* Column Widths */
        th:nth-child(1), td:nth-child(1) { width: 40%; } /* Item */
        th:nth-child(2), td:nth-child(2) { width: 15%; } /* Qty */
        th:nth-child(3), td:nth-child(3) { width: 20%; } /* Harga */
        th:nth-child(4), td:nth-child(4) { width: 25%; } /* Total */

        /* Totals Section */
        .totals {
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: {{ (int) $setting->paper_size < 70 ? '10px' : '11px' }};
        }

        .total-row.grand-total {
            font-weight: bold;
            font-size: {{ (int) $setting->paper_size < 70 ? '11px' : '12px' }};
            margin: 5px 0;
        }

        /* Footer Section */
        .footer {
            text-align: center;
            margin-top: 10px;
            padding-top: 5px;
            border-top: 1px dashed #000;
            font-size: 10px;
        }

        .footer p {
            margin-bottom: 3px;
        }

        /* Print Specific Styles */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .invoice-box {
                border: none;
            }

            .no-print {
                display: none;
            }
        }

        /* Helper Classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .mt-1 { margin-top: 5px; }
        .mb-1 { margin-bottom: 5px; }

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
            <th style="width: 40%">Item</th>
            <th style="width: 15%">Qty</th>
            <th style="width: 20%">Harga</th>
            <th style="width: 25%">Total</th>
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
                <td>{{ number_format($item->quantity, 0) }} {{ $item->unit->name }}</td>
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
