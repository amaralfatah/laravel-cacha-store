<!-- resources/views/pos/invoice.blade.php - Optimized with no left margin when printing -->
<!DOCTYPE html>
<html>

<head>
    <title>Invoice #{{ $transaction->invoice_number }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* Page Settings - Precisely set for thermal printers */
        @page {
            margin: 0;
            padding: 0;
            size: 62mm auto;
        }

        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            /* Main font settings - optimized for thermal printers to match example */
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            line-height: 1.2;
            width: 60mm;
            max-width: 60mm;
            padding: 1mm 0;
            /* CRITICAL: No horizontal padding/margin */
            color: black;
            margin: 0;
            /* No margin */
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            /* Set normal weight as default to match thermal printer appearance */
            font-weight: normal;
        }

        /* Core container */
        .invoice-box {
            width: 60mm;
            /* Match body width */
            padding: 0;
            margin: 0;
            /* No margin */
        }

        /* HEADER SECTION - Match styling from example */
        .header {
            text-align: center;
            padding: 0 0 2px 0;
            margin-bottom: 3px;
            width: 100%;
            font-weight: normal;
        }

        .company-name {
            font-weight: normal;
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

        /* TRANSACTION INFO - Compact with no left margin */
        .info-section {
            margin-bottom: 4px;
            width: 100%;
            padding: 0 1mm;
            /* Only add minimal horizontal padding here */
        }

        .info-row {
            display: flex;
            margin: 1px 0;
            width: 100%;
        }

        .info-label {
            width: 40px;
            text-align: left;
        }

        .info-colon {
            width: 8px;
            text-align: left;
        }

        .info-value {
            width: calc(100% - 100px);
            text-align: left;
        }

        .info-date {
            width: 90px;
            text-align: right;
            font-size: 11px;
            font-weight: normal;
        }

        /* ITEMS SECTION - EXACT LAYOUT MATCHING RECEIPT EXAMPLE */
        .items-section {
            width: 100%;
            margin-bottom: 3px;
            padding: 0 1mm;
        }

        .item-row {
            margin-bottom: 1px;
            width: 100%;
        }

        /* Product name styling */
        .item-name {
            text-transform: uppercase;
            width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 0;
            font-weight: normal;
        }

        /* Price line with quantity, unit, price and subtotal */
        .item-price-line {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 0;
        }

        .item-quantity-info {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 70%;
            text-align: left;
            font-weight: normal;
        }

        .item-subtotal-value {
            width: 30%;
            text-align: right;
            font-weight: normal;
        }

        /* Discount display with proper right alignment under subtotal */
        .item-discount {
            display: flex;
            width: 100%;
            justify-content: flex-end;
        }

        .item-discount-label {
            text-align: right;
            padding-right: 8px;
            font-weight: normal;
        }

        .item-discount-value {
            width: 70px; /* Match with subtotal value width */
            text-align: right;
            font-weight: normal;
        }

        /* SUMMARY AND PAYMENT SECTIONS - MATCH EXAMPLE */
        .summary-section, .payment-info {
            margin-top: 3px;
            margin-bottom: 3px;
            width: 100%;
            padding: 0 1mm;
        }

        .summary-row, .info-row {
            display: flex;
            margin: 0;
            width: 100%;
            font-weight: normal;
        }

        .summary-label, .info-label {
            width: 100px;
            text-align: left;
            font-weight: normal;
        }

        .summary-colon, .info-colon {
            width: 8px;
            text-align: left;
            font-weight: normal;
        }

        .summary-value, .info-value {
            width: calc(100% - 78px);
            text-align: right;
            font-weight: normal;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            margin-top: 3px;
            padding-top: 1px;
            padding-bottom: 2px;
            line-height: 1.3;
            width: 100%;
            font-weight: normal;
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
                width: 62mm !important;
                max-width: 62mm !important;
                margin: 0 !important;
                /* No margin */
                padding: 0 !important;
                /* No padding */
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .invoice-box {
                width: 62mm !important;
                margin: 0 !important;
                /* No margin */
                padding: 0 !important;
                /* No padding */
                position: absolute !important;
                left: 0 !important;
                /* Force left alignment */
            }

            .info-section,
            .items-section,
            .summary-section,
            .payment-info {
                padding: 0 1mm !important;
                /* Minimal padding */
            }

            .no-print {
                display: none !important;
            }

            /* Space for paper cutting */
            body::after {
                content: "";
                display: block;
                height: 10mm;
            }

            /* Force all text to be bold for better thermal printing */
            * {
                font-weight: bold !important;
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

        <!-- TRANSACTION INFO - MATCH EXAMPLE -->
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">No</span>
                <span class="info-colon">:</span>
                <span class="info-value">{{ $transaction->invoice_number }}</span>
                <span class="info-date">#{{ $transaction->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kasir</span>
                <span class="info-colon">:</span>
                <span class="info-value">{{ $transaction->user->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Pel.</span>
                <span class="info-colon">:</span>
                <span class="info-value">{{ strtoupper($transaction->payment_type === 'cash' ? 'Tunai' : $transaction->payment_type) }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- ITEMS - EXACT LAYOUT WITH RIGHT-ALIGNED DISCOUNT -->
        <div class="items-section">
            @foreach ($transaction->items as $item)
                <div class="item-row">
                    <!-- Product name on first line -->
                    <div class="item-name">
                        {{ strtoupper($item->product->name) }}
                    </div>

                    <!-- Quantity, unit, price, and subtotal on second line -->
                    <div class="item-price-line">
                        <div class="item-quantity-info">
                            {{ number_format($item->quantity, 2) }} {{ strtoupper($item->unit->code ?? 'PCS') }} x {{ number_format($item->unit_price, 0, ',', '.') }} :
                        </div>
                        <div class="item-subtotal-value">
                            {{ number_format($item->subtotal, 0, ',', '.') }}
                        </div>
                    </div>

                    <!-- Discount if applicable - right aligned under subtotal -->
                    @if ($item->discount > 0)
                        <div class="item-discount">
                            <div class="item-discount-label">Potongan :</div>
                            <div class="item-discount-value">
                                -{{ number_format($item->discount * $item->quantity, 0, ',', '.') }}
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="divider"></div>

        <!-- SUMMARY SECTION - MATCH RECEIPT EXAMPLE -->
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
                <span class="summary-value">{{ number_format($transaction->items->sum('subtotal'), 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- PAYMENT INFO - MATCH RECEIPT EXAMPLE -->
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

        <!-- FOOTER - MATCH RECEIPT EXAMPLE -->
        <div class="footer">
            <div>Terima kasih telah belanja di toko {{ $company['name'] }}</div>
            <div>---Kami tunggu kedatangannya kembali---</div>
        </div>

        <div class="footer-divider"></div>
    </div>

    <!-- Print script with enhanced zero margin handling -->
    <script>
        function printInvoice() {
            // Force exact positioning before printing
            document.body.style.margin = '0';
            document.body.style.padding = '0';
            document.querySelector('.invoice-box').style.position = 'absolute';
            document.querySelector('.invoice-box').style.left = '0';

            setTimeout(function() {
                window.print();
            }, 200);
        }

        @if ($setting->auto_print ?? false)
            window.onload = function() {
                setTimeout(function() {
                    printInvoice();
                    setTimeout(function() {
                        window.close();
                    }, 1000);
                }, 300);
            };
        @endif
    </script>
</body>

</html>
