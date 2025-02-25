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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.2;
            margin: 0;
            padding: 0;
            font-size: {{ (int) $setting->paper_size < 70 ? '9px' : '10px' }};
            width: 100%;
            max-width: {{ (int) $setting->paper_size }}mm;
        }

        .invoice-box {
            width: 100%;
            max-width: {{ (int) $setting->paper_size }}mm;
            padding: 2px;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
        }

        .company-name {
            font-weight: bold;
            font-size: {{ (int) $setting->paper_size < 70 ? '12px' : '14px' }};
            margin-bottom: 1px;
        }

        .company-details {
            font-size: {{ (int) $setting->paper_size < 70 ? '8px' : '9px' }};
            line-height: 1.1;
        }

        .divider {
            border: none;
            border-top: 1px dotted #000;
            margin: 3px 0;
        }

        .info {
            font-size: {{ (int) $setting->paper_size < 70 ? '8px' : '9px' }};
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1px;
        }

        .info-row span:first-child {
            font-weight: bold;
            width: 40%;
        }

        .info-row span:last-child {
            text-align: right;
            width: 60%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 3px 0;
            table-layout: fixed;
            font-size: {{ (int) $setting->paper_size < 70 ? '8px' : '9px' }};
        }

        th {
            text-align: left;
            padding: 1px 0;
            font-weight: bold;
            font-size: {{ (int) $setting->paper_size < 70 ? '8px' : '9px' }};
        }

        td {
            padding: 1px 0;
            vertical-align: top;
        }

        .item-name {
            word-break: break-word;
            white-space: normal;
            line-height: 1.1;
        }

        .discount-info {
            font-size: {{ (int) $setting->paper_size < 70 ? '7px' : '8px' }};
        }

        /* Column widths */
        .col-item { width: 40%; }
        .col-qty { width: 10%; text-align: center; }
        .col-price { width: 20%; text-align: right; }
        .col-total { width: 25%; text-align: right; }

        .totals {
            font-size: {{ (int) $setting->paper_size < 70 ? '8px' : '9px' }};
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1px;
        }

        .total-row span:first-child {
            font-weight: bold;
            width: 50%;
        }

        .total-row span:last-child {
            text-align: right;
            width: 50%;
        }

        .grand-total {
            font-weight: bold;
            font-size: {{ (int) $setting->paper_size < 70 ? '11px' : '12px' }};
            margin: 2px 0;
        }

        .footer {
            text-align: center;
            margin-top: 5px;
            font-size: {{ (int) $setting->paper_size < 70 ? '8px' : '9px' }};
            font-style: italic;
        }

        .footer p {
            margin-bottom: 1px;
            line-height: 1.1;
        }

        /* Print settings */
        @media print {
            body {
                width: {{ (int) $setting->paper_size }}mm;
            }

            .no-print {
                display: none;
            }
        }

        .no-print {
            text-align: center;
            margin-top: 15px;
        }

        .no-print button {
            padding: 5px 10px;
            margin: 0 3px;
            cursor: pointer;
            border: 1px solid #ddd;
            border-radius: 3px;
            background: #fff;
        }
    </style>
</head>

<body>
<div class="invoice-box">
    <div class="header">
        <div class="company-name">{{ $company['name'] }}</div>
        <div class="company-details">{{ $company['address'] }}</div>
        <div class="company-details">{{ $company['phone'] }}</div>
    </div>

    <hr class="divider">

    <div class="info">
        <div class="info-row">
            <span>No:</span>
            <span>#{{ $transaction->invoice_number }}</span>
        </div>
        <div class="info-row">
            <span>Tgl:</span>
            <span>{{ $transaction->invoice_date->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span>Kasir:</span>
            <span>{{ $transaction->user->name }}</span>
        </div>
        @if($transaction->customer)
            <div class="info-row">
                <span>Cust:</span>
                <span>{{ $transaction->customer->name }}</span>
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
            <tr>
                <td class="col-item">
                    <div class="item-name">{{ $item->product->name }}</div>
                    @if($item->discount > 0)
                        <div class="discount-info">Disc: {{ $item->discount }}</div>
                    @endif
                </td>
                <td class="col-qty">{{ $item->quantity }}</td>
                <td class="col-price">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="col-total">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <hr class="divider">

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
            <span>TOTAL:</span>
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

    <hr class="divider">

    <div class="footer">
        <p>Terima kasih telah berbelanja</p>
        <p>Barang yang sudah dibeli tidak dapat dikembalikan</p>
    </div>
</div>

<div class="no-print">
    <button onclick="window.print()">Print Invoice</button>
    <button onclick="window.close()">Tutup</button>
</div>

<script>
    window.onload = function() {
        @if($setting->auto_print)
        setTimeout(function() {
            window.print();
        }, 300);
        @endif
    };
</script>
</body>
</html>
