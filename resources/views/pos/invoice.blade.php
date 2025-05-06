<!-- resources/views/pos/invoice.blade.php - Compact but readable -->
<!DOCTYPE html>
<html>

<head>
    <title>Invoice #{{ $transaction->invoice_number }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* Page Settings - Adjusted width */
        @page {
            margin: 0;
            size: {{ request('size') == '57' ? '57mm' : '72mm' }} auto; /* Slightly wider */
        }

        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            /* Font settings */
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px; /* Back to original size */
            line-height: 1.2; /* Compact line height */
            width: {{ request('size') == '57' ? '57mm' : '70mm' }}; /* Slightly wider */
            max-width: 100%;
            padding: 0mm;
            color: black;
            margin: 0 auto;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-weight: bold; /* Keep bold for readability */
            letter-spacing: 0;
        }

        /* Core container */
        .invoice-box {
            width: 100%;
            padding: 1px;
        }

        /* HEADER SECTION - Centered */
        .header {
            text-align: center;
            padding: 2px 0 3px 0; /* Reduced padding */
            margin-bottom: 3px; /* Reduced margin */
        }

        .company-name {
            font-weight: bold;
            font-size: 12px;
            letter-spacing: 0;
            margin-bottom: 1px; /* Reduced margin */
            text-transform: uppercase;
        }

        /* CLEAR DIVIDERS */
        .divider {
            border-bottom: 1px dotted black;
            margin: 3px 0; /* Reduced margin */
            clear: both;
            width: 100%;
            overflow: hidden;
        }

        /* TRANSACTION INFO */
        .info-section {
            margin-bottom: 3px; /* Reduced margin */
        }

        .info-row {
            display: flex;
            margin: 2px 0; /* Compact margin */
            white-space: nowrap;
        }

        .info-label {
            width: 70px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }

        .info-colon {
            width: 10px;
            text-align: left;
        }

        .info-value {
            flex: 1;
            text-align: left;
        }

        .info-date {
            text-align: right;
            margin-left: auto;
            padding-right: 1px; /* Small padding to prevent cutoff */
        }

        /* ITEMS SECTION */
        .items-section {
            width: 100%;
        }

        .item-row {
            margin-bottom: 1px; /* Minimal spacing */
        }

        .item-name {
            font-weight: bold;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 10px;
            text-transform: uppercase;
        }

        .item-detail {
            display: flex;
            justify-content: space-between;
            padding-left: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .item-quantity {
            width: 65%;
            text-align: left;
        }

        .item-total {
            width: 35%;
            text-align: right;
            padding-right: 1px; /* Small padding to prevent cutoff */
        }

        .item-discount {
            display: flex;
            justify-content: space-between;
            padding-left: 30px;
            font-size: 10px;
            font-weight: bold;
        }

        .item-discount-label {
            width: 65%;
            text-align: left;
        }

        .item-discount-value {
            width: 35%;
            text-align: right;
            padding-right: 1px; /* Small padding to prevent cutoff */
        }

        /* SUMMARY SECTION */
        .summary-section {
            margin-top: 3px;
            margin-bottom: 3px;
        }

        .summary-row {
            display: flex;
            margin: 2px 0;
        }

        .summary-label {
            width: 110px; /* Reduced width */
            text-align: left;
        }

        .summary-colon {
            width: 10px;
            text-align: left;
        }

        .summary-value {
            flex: 1;
            text-align: right;
            padding-right: 1px; /* Small padding to prevent cutoff */
        }

        /* PAYMENT INFO */
        .payment-info {
            margin-top: 3px;
            margin-bottom: 3px;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            margin-top: 5px;
            padding-top: 3px;
            padding-bottom: 3px;
            font-size: 10px;
            line-height: 1.2;
        }

        .footer-divider {
            border-bottom: 1px dotted black;
            margin: 3px 0;
            clear: both;
            width: 100%;
            overflow: hidden;
        }

        /* PRINT CONTROLS */
        @media print {
            html,
            body {
                width: {{ request('size') == '57' ? '57mm' : '70mm' }};
                margin: 0 auto;
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none;
            }

            /* Space for paper cutting */
            body::after {
                content: "";
                display: block;
                height: 15mm;
            }
        }

        .no-print {
            text-align: center;
            margin-top: 10px;
            padding: 5px;
            border-top: 1px dotted black;
        }

        .no-print button {
            padding: 8px;
            margin: 0 3px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <!-- HEADER - Centered shop name and address -->
        <div class="header">
            <div class="company-name">TOKO {{ $company['name'] }}</div>
            <div>{{ $company['address'] }}</div>
            <div>Telp/WA {{ $company['phone'] }}</div>
        </div>

        <!-- TRANSACTION INFO - Left aligned with colon -->
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">No</span>
                <span class="info-colon">:</span>
                <span class="info-value">{{ $transaction->invoice_number }}</span>
                <span class="info-date">{{ $transaction->invoice_date->format('#d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kasir</span>
                <span class="info-colon">:</span>
                <span class="info-value">{{ $transaction->user->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Pel.</span>
                <span class="info-colon">:</span>
                <span class="info-value">{{ strtoupper($transaction->payment_type) }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- ITEMS - Compact layout -->
        <div class="items-section">
            @foreach ($transaction->items as $item)
                <div class="item-row">
                    <div class="item-name">{{ strtoupper($item->product->name) }}</div>
                    <div class="item-detail">
                        <div class="item-quantity">{{ number_format($item->quantity, 2) }} {{ strtoupper($item->unit->name ?? 'PCS') }} x {{ number_format($item->unit_price, 0, ',', '.') }}</div>
                        <div class="item-total">{{ number_format($item->subtotal, 0, ',', '.') }}</div>
                    </div>
                    @if ($item->discount > 0)
                        <div class="item-discount">
                            <div class="item-discount-label">Potongan</div>
                            <div class="item-discount-value">-{{ number_format($item->discount * $item->quantity, 0, ',', '.') }}</div>
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
            <div class="summary-row">
                <span class="summary-label">Total Jual</span>
                <span class="summary-colon">:</span>
                <span class="summary-value">{{ number_format($transaction->final_amount, 0, ',', '.') }}</span>
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
            <div>Kami tunggu kedatangannya kembali</div>
        </div>

        <div class="footer-divider"></div>
    </div>

    <!-- PRINT CONTROLS - Not visible when printed -->
    <div class="no-print">
        <button onclick="window.print()">Cetak Struk</button>
        <button onclick="window.close()">Tutup</button>
    </div>
</body>

</html>
