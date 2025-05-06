<!-- resources/views/pos/invoice.blade.php - Balanced spacing -->
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
            size: 58mm auto;
        }

        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 9px;
            line-height: 1.1;
            width: 56mm;
            max-width: 56mm;
            padding: 0.5mm; /* Reduced padding */
            color: black;
            margin: 0 auto;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-weight: bold;
        }

        /* Core container */
        .invoice-box {
            width: 55mm; /* Slightly wider */
            padding: 0;
            margin: 0 auto;
        }

        /* HEADER SECTION */
        .header {
            text-align: center;
            padding: 1px 0 2px 0;
            margin-bottom: 2px;
            width: 100%;
        }

        .company-name {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 1px;
            text-transform: uppercase;
            width: 100%;
        }

        /* CLEAR DIVIDERS */
        .divider {
            border-bottom: 1px dotted black;
            margin: 2px 0;
            clear: both;
            width: 100%;
        }

        /* TRANSACTION INFO - Adjusted spacing */
        .info-section {
            margin-bottom: 2px;
            width: 100%;
        }

        .info-row {
            display: flex;
            margin: 1px 0;
            width: 100%;
        }

        .info-label {
            width: 32px; /* Even smaller width */
            text-align: left;
            padding-left: 0; /* No left padding */
        }

        .info-colon {
            width: 6px; /* Narrower colon */
            text-align: left;
            padding-left: 0; /* No left padding */
        }

        .info-value {
            width: calc(100% - 98px); /* Adjusted width */
            text-align: left;
            padding-left: 0; /* No left padding */
        }

        .info-date {
            width: 60px;
            text-align: right;
        }

        /* ITEMS SECTION - Adjusted spacing */
        .items-section {
            width: 100%;
            padding-left: 0; /* No left padding */
        }

        .item-row {
            margin-bottom: 1px;
            width: 100%;
        }

        .item-name {
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding-left: 0; /* No left padding */
        }

        .item-detail {
            display: flex;
            width: 100%;
        }

        .item-quantity {
            width: 60%;
            text-align: left;
            font-size: 9px;
            padding-left: 0; /* No left padding */
        }

        .item-total {
            width: 40%;
            text-align: right;
            font-size: 9px;
        }

        .item-discount {
            display: flex;
            width: 100%;
        }

        .item-discount-label {
            width: 60%;
            text-align: left;
            padding-left: 15px; /* Further reduced padding */
            font-size: 9px;
        }

        .item-discount-value {
            width: 40%;
            text-align: right;
            font-size: 9px;
        }

        /* SUMMARY SECTION - Adjusted spacing */
        .summary-section {
            margin-top: 2px;
            margin-bottom: 2px;
            width: 100%;
        }

        .summary-row {
            display: flex;
            margin: 1px 0;
            width: 100%;
        }

        .summary-label {
            width: 68px; /* Slightly reduced width */
            text-align: left;
            font-size: 9px;
            padding-left: 0; /* No left padding */
        }

        .summary-colon {
            width: 6px; /* Narrower colon */
            text-align: left;
            font-size: 9px;
            padding-left: 0; /* No left padding */
        }

        .summary-value {
            width: calc(100% - 74px);
            text-align: right;
            font-size: 9px;
        }

        /* PAYMENT INFO */
        .payment-info {
            margin-top: 2px;
            margin-bottom: 2px;
            width: 100%;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            margin-top: 3px;
            padding-top: 2px;
            padding-bottom: 2px;
            font-size: 9px;
            line-height: 1.1;
            width: 100%;
        }

        .footer-divider {
            border-bottom: 1px dotted black;
            margin: 2px 0;
            clear: both;
            width: 100%;
        }

        /* Tighter text */
        .text-tight {
            letter-spacing: -0.3px; /* More negative letter spacing */
        }

        /* PRINT CONTROLS */
        @media print {
            html,
            body {
                width: 56mm;
                max-width: 56mm;
                margin: 0 auto;
                padding: 0.5mm; /* Reduced padding */
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .invoice-box {
                width: 55mm;
            }

            .no-print {
                display: none;
            }

            /* Space for paper cutting */
            body::after {
                content: "";
                display: block;
                height: 10mm;
            }
        }

        .no-print {
            text-align: center;
            margin-top: 8px;
            padding: 3px;
            border-top: 1px dotted black;
            width: 100%;
        }

        .no-print button {
            padding: 5px;
            margin: 0 2px;
            cursor: pointer;
            font-size: 9px;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <!-- HEADER -->
        <div class="header">
            <div class="company-name">TOKO {{ $company['name'] }}</div>
            <div class="text-tight">{{ $company['address'] }}</div>
            <div class="text-tight">Telp/WA {{ $company['phone'] }}</div>
        </div>

        <!-- TRANSACTION INFO -->
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">No</span>
                <span class="info-colon">:</span>
                <span class="info-value text-tight">{{ $transaction->invoice_number }}</span>
                <span class="info-date">{{ $transaction->invoice_date->format('#d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kasir</span>
                <span class="info-colon">:</span>
                <span class="info-value text-tight">{{ $transaction->user->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Pel.</span>
                <span class="info-colon">:</span>
                <span class="info-value text-tight">{{ strtoupper($transaction->payment_type) }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- ITEMS -->
        <div class="items-section">
            @foreach ($transaction->items as $item)
                <div class="item-row">
                    <div class="item-name text-tight">{{ strtoupper($item->product->name) }}</div>
                    <div class="item-detail">
                        <div class="item-quantity text-tight">{{ number_format($item->quantity, 2) }}
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
            <div class="text-tight">Terima kasih telah belanja di toko {{ $company['name'] }}</div>
            <div class="text-tight">Jangan lupa kunjungi tokocacha.com</div>
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
