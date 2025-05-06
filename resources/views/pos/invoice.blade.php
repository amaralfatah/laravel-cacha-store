<!-- resources/views/pos/invoice.blade.php - Optimized for POS Thermal Printers -->
<!DOCTYPE html>
<html>

<head>
    <title>Invoice #{{ $transaction->invoice_number }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* Page Settings - Strict dimensions for thermal printer */
        @page {
            margin: 0;
            size: {{ request('size') == '57' ? '57mm' : '72mm' }} auto; /* Reduced from 78mm to 72mm */
        }

        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Courier New", Courier, monospace;
            font-size: {{ request('size') == '57' ? '9px' : '10px' }}; /* Increased for better clarity */
            line-height: 1.3; /* Increased line height for better readability */
            width: {{ request('size') == '57' ? '57mm' : '70mm' }}; /* Reduced from 78mm to 70mm */
            max-width: 100%;
            padding: 0;
            color: black;
            margin: 0 auto; /* Center content */
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-weight: 500; /* Slightly bold all text for better printing */
        }

        /* Core container */
        .invoice-box {
            width: 100%;
            padding: 1px; /* Reduced padding from 2px to 1px */
        }

        /* HEADER SECTION */
        .header {
            text-align: center;
            padding: 2px 0;
            border-bottom: 1px solid black;
            margin-bottom: 3px; /* Reduced margin */
        }

        .company-name {
            font-weight: bold;
            font-size: {{ request('size') == '57' ? '12px' : '13px' }}; /* Increased for better visibility */
            letter-spacing: 0.5px; /* Add slight letter spacing for clarity */
        }

        /* CLEAR DIVIDERS */
        .divider {
            border-bottom: 1px solid black;
            margin: 3px 0; /* Reduced margin from 5px to 3px */
            clear: both;
        }

        /* TRANSACTION INFO */
        .info-section {
            margin-bottom: 3px; /* Reduced margin */
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 1px 0; /* Reduced margin */
            overflow: hidden; /* Prevent overflow */
            white-space: nowrap; /* Prevent line breaks in important info */
        }

        .info-label {
            font-weight: bold;
            letter-spacing: 0.3px;
            font-size: 10px; /* Ensure clear visibility */
        }

        /* ITEMS TABLE - Minimal borders for clarity */
        .items-table {
            width: 100%;
            margin: 3px 0; /* Reduced margin */
            border-collapse: collapse;
            table-layout: fixed; /* Fixed table layout */
        }

        .items-table th {
            text-align: left;
            padding: 2px 1px; /* Added vertical padding */
            border-bottom: 1px solid black;
            font-weight: bold;
            font-size: 11px; /* Increased font size for headers */
        }

        .items-table td {
            padding: 2px 1px; /* Increased vertical padding */
            vertical-align: top;
            word-break: break-word; /* Break words if too long */
        }

        .item-name {
            font-weight: bold;
            overflow: hidden;
            text-overflow: ellipsis; /* Add ellipsis for long text */
            letter-spacing: 0.3px; /* Better letter spacing */
        }

        .item-detail {
            padding-left: 3px; /* Reduced padding */
            font-size: 9px; /* Slightly larger than before */
            font-weight: normal; /* Normal weight for details */
        }

        /* SUMMARY SECTION */
        .summary-section {
            margin-top: 3px; /* Reduced margin */
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 1px 0; /* Reduced margin */
        }

        /* GRAND TOTAL - Very clear */
        .grand-total {
            font-weight: bold;
            font-size: {{ request('size') == '57' ? '11px' : '12px' }}; /* Increased for better visibility */
            border-top: 1px solid black;
            border-bottom: 1px solid black;
            padding: 3px 0; /* Increased padding for emphasis */
            margin: 4px 0; /* Slightly more space around total */
            letter-spacing: 0.5px; /* Better letter spacing */
        }

        /* PAYMENT METHOD */
        .payment-method {
            text-align: center;
            font-weight: bold;
            margin: 3px 0; /* Reduced margin */
        }

        /* FOOTER */
        .footer {
            text-align: center;
            margin-top: 5px; /* Reduced margin */
            border-top: 1px solid black;
            padding-top: 3px; /* Reduced padding */
        }

        .footer-text {
            font-weight: bold;
            font-size: 11px; /* Larger size for thank you message */
            letter-spacing: 0.5px; /* Better letter spacing */
        }

        /* PRINT CONTROLS */
        @media print {
            html,
            body {
                width: {{ request('size') == '57' ? '57mm' : '70mm' }}; /* Reduced from 78mm to 70mm */
                margin: 0 auto;
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none;
            }

            /* Improve print quality */
            * {
                text-rendering: optimizeLegibility;
                -webkit-font-smoothing: antialiased;
            }

            /* Space for paper cutting */
            body::after {
                content: "";
                display: block;
                height: 10mm; /* Reduced from 15mm to 10mm */
            }
        }

        .no-print {
            text-align: center;
            margin-top: 15px; /* Reduced margin */
            padding: 5px; /* Reduced padding */
            border-top: 1px dashed black;
        }

        .no-print button {
            padding: 8px; /* Reduced padding */
            margin: 0 3px; /* Reduced margin */
            cursor: pointer;
        }
    </style>
</head>

<body>
<div class="invoice-box">
    <!-- HEADER -->
    <div class="header">
        <div class="company-name">{{ $company['name'] }}</div>
        <div>{{ $company['address'] }}</div>
        <div>Telp: {{ $company['phone'] }}</div>
    </div>

    <!-- TRANSACTION INFO -->
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">INVOICE:</span>
            <span>#{{ $transaction->invoice_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">TANGGAL:</span>
            <span>{{ $transaction->invoice_date->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">KASIR:</span>
            <span>{{ $transaction->user->name }}</span>
        </div>

        @if ($transaction->customer && $transaction->customer_id != 1)
            <div class="info-row">
                <span class="info-label">PELANGGAN:</span>
                <span>{{ $transaction->customer->name }}</span>
            </div>
        @endif
    </div>

    <div class="divider"></div>

    <!-- ITEMS TABLE -->
    <table class="items-table">
        <thead>
        <tr>
            <th style="width: 65%">ITEM</th>
            <th style="width: 35%; text-align: right">TOTAL</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($transaction->items as $item)
            <tr>
                <td style="width: 65%">
                    <div class="item-name">{{ $item->product->name }}</div>
                    <div class="item-detail">
                        {{ number_format($item->quantity, 0) }} {{ $item->unit->name ?? 'pcs' }}
                        x
                        {{ number_format($item->unit_price, 0, ',', '.') }}
                    </div>
                </td>
                <td style="width: 35%; text-align: right">
                    {{ number_format($item->subtotal, 0, ',', '.') }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <!-- SUMMARY SECTION -->
    <div class="summary-section">
        <div class="summary-row">
            <span>Subtotal:</span>
            <span>{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
        </div>

        @if ($transaction->discount_amount > 0)
            <div class="summary-row">
                <span>Diskon:</span>
                <span>-{{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
            </div>
        @endif

        @if ($transaction->tax_amount > 0)
            <div class="summary-row">
                <span>Pajak (10%):</span>
                <span>{{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
            </div>
        @endif

        <!-- GRAND TOTAL -->
        <div class="grand-total">
            <div class="summary-row">
                <span>TOTAL BAYAR:</span>
                <span>{{ number_format($transaction->final_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- PAYMENT INFO -->
        <div class="payment-method">
            {{ strtoupper($transaction->payment_type) }}
        </div>

        @if ($transaction->payment_type === 'cash')
            <div class="summary-row">
                <span>Tunai:</span>
                <span>{{ number_format($transaction->cash_amount, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span>Kembalian:</span>
                <span>{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
        @endif

        @if ($transaction->reference_number)
            <div class="summary-row">
                <span>No. Ref:</span>
                <span>{{ $transaction->reference_number }}</span>
            </div>
        @endif
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div class="footer-text">TERIMA KASIH</div>
        <div>Barang yang sudah dibeli tidak dapat dikembalikan</div>
        <div style="font-size: 7px; margin-top: 3px">
            {{ $transaction->store_id }}/{{ $transaction->id }}/{{ now()->format('YmdHi') }}
        </div>
    </div>
</div>

<!-- PRINT CONTROLS - Not visible when printed -->
<div class="no-print">
    <button onclick="window.print()">Cetak Struk</button>
    <button onclick="window.close()">Tutup</button>
</div>
</body>

</html>
