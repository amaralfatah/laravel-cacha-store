<!-- resources/views/pos/invoice.blade.php - With increased width -->
<!DOCTYPE html>
<html>

<head>
    <title>Invoice #{{ $transaction->invoice_number }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* Page Settings - Slightly increased width */
        @page {
            margin: 0;
            size: 62mm auto;
            /* Increased from 58mm to 62mm for thermal printers */
        }

        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            /* Main font settings - this controls all text */
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            /* Main font size control */
            line-height: 1.2;
            width: 60mm;
            /* Increased from 56mm to 60mm */
            max-width: 60mm;
            padding: 1mm;
            color: black;
            margin: 0 auto;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-weight: bold;
        }

        /* Core container */
        .invoice-box {
            width: 58mm;
            /* Increased from 54mm to 58mm */
            padding: 0;
            margin: 0 auto;
        }

        /* HEADER SECTION */
        .header {
            text-align: center;
            padding: 1px 0 2px 0;
            margin-bottom: 4px;
            width: 100%;
        }

        .company-name {
            font-weight: bold;
            margin-bottom: 1px;
            text-transform: uppercase;
            width: 100%;
        }

        /* CLEAR DIVIDERS */
        .divider {
            border-bottom: 1px dotted black;
            margin: 4px 0;
            clear: both;
            width: 100%;
        }

        /* TRANSACTION INFO - More compact */
        .info-section {
            margin-bottom: 4px;
            width: 100%;
        }

        .info-row {
            display: flex;
            margin: 1px 0;
            width: 100%;
        }

        .info-label {
            width: 35px;
            text-align: left;
        }

        .info-colon {
            width: 8px;
            text-align: left;
        }

        .info-value {
            width: calc(100% - 103px);
            text-align: left;
        }

        .info-date {
            width: 60px;
            text-align: right;
        }

        /* ITEMS SECTION - Compact */
        .items-section {
            width: 100%;
            margin-bottom: 4px;
        }

        .item-row {
            margin-bottom: 4px;
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
        }

        .item-quantity {
            width: 60%;
            text-align: left;
        }

        .item-total {
            width: 40%;
            text-align: right;
        }

        .item-discount {
            display: flex;
            width: 100%;
        }

        .item-discount-label {
            width: 60%;
            text-align: left;
            padding-left: 20px;
        }

        .item-discount-value {
            width: 40%;
            text-align: right;
        }

        /* SUMMARY SECTION - Compact */
        .summary-section {
            margin-top: 4px;
            margin-bottom: 4px;
            width: 100%;
        }

        .summary-row {
            display: flex;
            margin: 1px 0;
            width: 100%;
        }

        .summary-label {
            width: 70px;
            text-align: left;
        }

        .summary-colon {
            width: 8px;
            text-align: left;
        }

        .summary-value {
            width: calc(100% - 78px);
            text-align: right;
        }

        /* PAYMENT INFO */
        .payment-info {
            margin-top: 4px;
            margin-bottom: 4px;
            width: 100%;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            margin-top: 4px;
            padding-top: 2px;
            padding-bottom: 2px;
            line-height: 1.2;
            width: 100%;
        }

        .footer-divider {
            border-bottom: 1px dotted black;
            margin: 4px 0;
            clear: both;
            width: 100%;
        }

        /* Tighter text */
        .text-tight {
            letter-spacing: -0.2px;
        }

        /* PRINT CONTROLS */
        @media print {

            html,
            body {
                width: 62mm;
                max-width: 62mm;
                margin: 0 auto;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .invoice-box {
                width: 58mm;
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
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <!-- HEADER -->
        <div class="header">
            <div class="company-name">{{ $company['name'] }}</div>
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
