<!-- resources/views/pos/invoice.blade.php - Optimized for 78mm dot matrix printer -->
<!DOCTYPE html>
<html>

<head>
    <title>Invoice #{{ $transaction->invoice_number }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* Page Settings - Adjusted for 78mm dot matrix printer */
        @page {
            margin: 0;
            size: 78mm auto;
            /* Set width to 78mm for dot matrix printer */
        }

        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            /* Optimized font settings for dot matrix */
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            /* Increased font size for better readability on dot matrix */
            line-height: 1.4;
            /* Increased line height for dot matrix */
            width: 76mm;
            /* Adjusted width for content area */
            max-width: 76mm;
            padding: 2mm;
            /* Increased padding */
            color: black;
            margin: 0 auto;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-weight: bold;
            /* Kept bold for better visibility on dot matrix */
        }

        /* Core container */
        .invoice-box {
            width: 72mm;
            /* Increased width */
            padding: 0;
            margin: 0 auto;
        }

        /* HEADER SECTION */
        .header {
            text-align: center;
            padding: 2px 0 3px 0;
            /* Increased padding */
            margin-bottom: 5px;
            /* Increased margin */
            width: 100%;
        }

        .company-name {
            font-weight: bold;
            margin-bottom: 2px;
            /* Increased spacing */
            text-transform: uppercase;
            width: 100%;
            font-size: 14px;
            /* Larger font for company name */
        }

        /* CLEAR DIVIDERS - Dot matrix friendly */
        .divider {
            border-bottom: none;
            border-top: none;
            height: 1px;
            background-image: repeating-linear-gradient(to right, #000, #000 4px, transparent 4px, transparent 8px);
            /* Clearer dot pattern for dot matrix */
            margin: 6px 0;
            /* Increased margin */
            clear: both;
            width: 100%;
        }

        /* TRANSACTION INFO - Adapted for 78mm */
        .info-section {
            margin-bottom: 6px;
            /* Increased margin */
            width: 100%;
        }

        .info-row {
            display: flex;
            margin: 2px 0;
            /* Increased margin */
            width: 100%;
        }

        .info-label {
            width: 70px;
            /* Increased width for labels */
            text-align: left;
        }

        .info-colon {
            width: 10px;
            text-align: left;
        }

        .info-value {
            width: calc(100% - 150px);
            /* Adjusted calculation */
            text-align: left;
        }

        .info-date {
            width: 70px;
            /* Increased width for date */
            text-align: right;
            font-size: 11px;
            /* Increased font size slightly */
        }

        /* ITEMS SECTION - Adapted for 78mm */
        .items-section {
            width: 100%;
            margin-bottom: 6px;
            /* Increased margin */
        }

        .item-row {
            margin-bottom: 5px;
            /* Increased margin */
            width: 100%;
        }

        .item-name {
            font-weight: bold;
            text-transform: uppercase;
            width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .item-detail {
            display: flex;
            width: 100%;
            margin-top: 1px;
            /* Added margin */
        }

        .item-quantity {
            width: 65%;
            /* Adjusted width */
            text-align: left;
        }

        .item-total {
            width: 35%;
            /* Adjusted width */
            text-align: right;
        }

        .item-discount {
            display: flex;
            width: 100%;
        }

        .item-discount-label {
            width: 65%;
            /* Adjusted width */
            text-align: left;
            padding-left: 20px;
        }

        .item-discount-value {
            width: 35%;
            /* Adjusted width */
            text-align: right;
        }

        /* SUMMARY SECTION - Adapted for 78mm */
        .summary-section {
            margin-top: 6px;
            margin-bottom: 6px;
            /* Increased margins */
            width: 100%;
        }

        .summary-row {
            display: flex;
            margin: 2px 0;
            /* Increased margin */
            width: 100%;
        }

        .summary-label {
            width: 90px;
            /* Increased width */
            text-align: left;
        }

        .summary-colon {
            width: 10px;
            text-align: left;
        }

        .summary-value {
            width: calc(100% - 100px);
            /* Adjusted calculation */
            text-align: right;
        }

        /* PAYMENT INFO */
        .payment-info {
            margin-top: 6px;
            margin-bottom: 6px;
            /* Increased margins */
            width: 100%;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            margin-top: 6px;
            /* Increased margin */
            padding-top: 3px;
            padding-bottom: 3px;
            /* Increased padding */
            line-height: 1.4;
            /* Increased line height */
            width: 100%;
        }

        .footer-divider {
            border-bottom: none;
            border-top: none;
            height: 1px;
            background-image: repeating-linear-gradient(to right, #000, #000 4px, transparent 4px, transparent 8px);
            /* Clearer dot pattern for dot matrix */
            margin: 6px 0;
            /* Increased margin */
            clear: both;
            width: 100%;
        }

        /* PRINT CONTROLS */
        @media print {

            html,
            body {
                width: 78mm;
                max-width: 78mm;
                margin: 0 auto;
                padding: 2mm;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .invoice-box {
                width: 74mm;
                /* Adjusted width */
            }

            .no-print {
                display: none;
            }

            /* Space for paper cutting */
            body::after {
                content: "";
                display: block;
                height: 15mm;
                /* Increased height */
            }
        }

        .no-print {
            text-align: center;
            margin-top: 10px;
            /* Increased margin */
            padding: 5px;
            /* Increased padding */
            border-top: 1px dashed black;
            /* Changed to dashed */
            width: 100%;
        }

        .no-print button {
            padding: 6px 12px;
            /* Increased button size */
            margin: 0 3px;
            /* Increased margin */
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
            <div>Telp/WA {{ $company['phone'] }}</div>
        </div>

        <!-- TRANSACTION INFO -->
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">No</span>
                <span class="info-colon">:</span>
                <span class="info-value">{{ $transaction->invoice_number }}</span>
                <span class="info-date">{{ $transaction->invoice_date->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kasir</span>
                <span class="info-colon">:</span>
                <span class="info-value">{{ $transaction->user->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Pembaya</span>
                <span class="info-colon">:</span>
                <span class="info-value">{{ strtoupper($transaction->payment_type) }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- ITEMS -->
        <div class="items-section">
            @foreach ($transaction->items as $item)
                <div class="item-row">
                    <div class="item-name">{{ strtoupper($item->product->name) }}</div>
                    <div class="item-detail">
                        <div class="item-quantity">{{ number_format($item->quantity, 2) }}
                            {{ strtoupper($item->unit->name ?? 'PCS') }} x
                            {{ number_format($item->unit_price, 0, ',', '.') }}</div>
                        <div class="item-total">{{ number_format($item->subtotal, 0, ',', '.') }}</div>
                    </div>
                    @if ($item->discount > 0)
                        <div class="item-discount">
                            <div class="item-discount-label">Potongan</div>
                            <div class="item-discount-value">
                                -{{ number_format($item->discount * $item->quantity, 0, ',', '.') }}</div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="divider"></div>

        <!-- SUMMARY SECTION -->
        <div class="summary-section">
            <div class="summary-row">
                <span class="summary-label">Total Jenis</span>
                <span class="summary-colon">:</span>
                <span class="summary-value">{{ $transaction->items->count() }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Total Item</span>
                <span class="summary-colon">:</span>
                <span class="summary-value">{{ number_format($transaction->items->sum('quantity'), 2) }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- PAYMENT INFO -->
        <div class="payment-info">
            <div class="summary-row">
                <span class="summary-label">Total</span>
                <span class="summary-colon">:</span>
                <span class="summary-value">{{ number_format($transaction->final_amount, 0, ',', '.') }}</span>
            </div>
            @if ($transaction->payment_type === 'cash')
                <div class="summary-row">
                    <span class="summary-label">Tunai</span>
                    <span class="summary-colon">:</span>
                    <span class="summary-value">{{ number_format($transaction->cash_amount, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Kembali</span>
                    <span class="summary-colon">:</span>
                    <span class="summary-value">{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>

        <div class="footer-divider"></div>
        <div class="footer-divider"></div>

        <!-- FOOTER -->
        <div class="footer">
            <div>Terima kasih telah belanja di toko {{ $company['name'] }}</div>
            <div>Jangan lupa kunjungi tokocacha.com</div>
        </div>

        <div class="footer-divider"></div>
    </div>

    <!-- PRINT CONTROLS -->
    <div class="no-print">
        <button onclick="window.print()">Cetak Struk</button>
        <button onclick="window.close()">Tutup</button>
    </div>
</body>

</html>
