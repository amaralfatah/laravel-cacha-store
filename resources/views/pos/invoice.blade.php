<!-- resources/views/pos/invoice.blade.php - Optimized for better readability -->
<!DOCTYPE html>
<html>

<head>
    <title>Invoice #{{ $transaction->invoice_number }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* Page Settings - Adjusted width for better visibility */
        @page {
            margin: 0;
            size: {{ request('size') == '57' ? '57mm' : '76mm' }} auto;
            /* Increased width */
        }

        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            /* Font and text contrast adjustment */
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            /* Slightly larger for better readability */
            line-height: 1.4;
            /* Increased line height */
            width: {{ request('size') == '57' ? '57mm' : '72mm' }};
            /* Increased width */
            max-width: 100%;
            padding: 1mm;
            color: black;
            margin: 0 auto;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-weight: bold;
            /* Make all text bold for better contrast */
            letter-spacing: 0.3px;
            /* Slightly increased letter spacing */
        }

        /* Core container */
        .invoice-box {
            width: 100%;
            padding: 1px;
        }

        /* HEADER SECTION - Centered */
        .header {
            text-align: center;
            padding: 2px 0 5px 0;
            margin-bottom: 5px;
        }

        .company-name {
            font-weight: bold;
            font-size: 14px;
            /* Larger font for company name */
            letter-spacing: 1px;
            /* More spacing for header */
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        /* CLEAR DIVIDERS - Changed to more visible styling */
        .divider {
            border-bottom: 1px dotted black;
            margin: 7px 0;
            /* Increased margin */
            clear: both;
            width: 100%;
            overflow: hidden;
            border-width: 2px;
            /* Thicker border */
        }

        /* TRANSACTION INFO - Adjusted layout */
        .info-section {
            margin-bottom: 7px;
        }

        .info-row {
            display: flex;
            margin: 3px 0;
            /* Increased margin */
            white-space: nowrap;
        }

        .info-label {
            width: 70px;
            /* Adjusted width */
            text-align: left;
            font-weight: bold;
            letter-spacing: 0.3px;
            font-size: 11px;
        }

        .info-colon {
            width: 10px;
            text-align: left;
            padding-right: 5px;
        }

        .info-value {
            flex: 1;
            text-align: left;
        }

        .info-date {
            text-align: right;
            margin-left: auto;
            padding-right: 2px;
            /* Ensure not cut off */
        }

        /* ITEMS SECTION - Better spacing */
        .items-section {
            width: 100%;
        }

        .item-row {
            margin-bottom: 5px;
            /* Increased space between items */
        }

        .item-name {
            font-weight: bold;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: 0.5px;
            font-size: 12px;
            /* Larger font for item name */
            text-transform: uppercase;
        }

        .item-detail {
            display: flex;
            justify-content: space-between;
            padding-left: 5px;
            font-size: 11px;
            font-weight: bold;
            margin-top: 2px;
        }

        .item-quantity {
            width: 60%;
            /* Adjusted width */
            text-align: left;
        }

        .item-total {
            width: 40%;
            /* Adjusted width */
            text-align: right;
            padding-right: 2px;
            /* Prevent cutoff */
        }

        .item-discount {
            display: flex;
            justify-content: space-between;
            padding-left: 30px;
            font-size: 11px;
            font-weight: bold;
            margin-top: 2px;
        }

        .item-discount-label {
            width: 60%;
            text-align: left;
        }

        .item-discount-value {
            width: 40%;
            text-align: right;
            padding-right: 2px;
            /* Prevent cutoff */
        }

        /* SUMMARY SECTION - Better alignment */
        .summary-section {
            margin-top: 7px;
            margin-bottom: 5px;
        }

        .summary-row {
            display: flex;
            margin: 3px 0;
        }

        .summary-label {
            width: 130px;
            text-align: left;
        }

        .summary-colon {
            width: 10px;
            text-align: left;
        }

        .summary-value {
            flex: 1;
            text-align: right;
            padding-right: 2px;
            /* Prevent cutoff */
        }

        /* PAYMENT INFO - Better spacing */
        .payment-info {
            margin-top: 7px;
            margin-bottom: 5px;
        }

        /* FOOTER - Adjusted styling */
        .footer {
            text-align: center;
            margin-top: 10px;
            padding-top: 5px;
            padding-bottom: 5px;
            font-size: 11px;
            line-height: 1.5;
        }

        .footer-divider {
            border-bottom: 1px dotted black;
            margin: 7px 0;
            clear: both;
            width: 100%;
            overflow: hidden;
            border-width: 2px;
            /* Thicker border */
        }

        /* PRINT CONTROLS */
        @media print {

            html,
            body {
                width: {{ request('size') == '57' ? '57mm' : '72mm' }};
                margin: 0 auto;
                padding: 1mm;
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
                height: 20mm;
            }
        }

        .no-print {
            text-align: center;
            margin-top: 15px;
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

        <!-- ITEMS - Each on a separate row with better spacing -->
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

        <!-- SUMMARY SECTION - With aligned colons -->
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

        <!-- PAYMENT INFO - With aligned colons -->
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
        <p style="margin-top: 10px; font-size: 12px;">
            <b>Catatan:</b> Untuk hasil terbaik, pastikan pengaturan printer tidak memotong isi.
        </p>
    </div>
</body>

</html>
