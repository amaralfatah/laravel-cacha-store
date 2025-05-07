<!-- resources/views/pos/invoice.blade.php - Fully optimized for both thermal and dot matrix printers -->
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>{{ isset($is_test) ? 'Test Print' : 'Invoice ' . $transaction->invoice_number }}</title>

    <style>
        /* Page Settings - Precisely optimized for both thermal and dot matrix printers */
        @page {
            margin: 0;
            /* Critical: Zero margins to prevent cutoffs */
            size: {{ $setting->paper_size ?? '80mm' }} auto;
            /* Support for various widths, defaulting to 80mm */
        }

        /* Reset and base styles with improved precision */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box !important;
            /* Ensure all elements use the same box model */
        }

        /* Body optimizations to prevent cutoff and ensure full width printing */
        body {
            /* Font optimization for matrix printing */
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            /* Slightly smaller to avoid right cutoff */
            line-height: 1.5;
            /* Increased line height for better separation */
            letter-spacing: 0.5px;
            /* Increased letter spacing critical for dot matrix */

            /* Width calculations that ensure no cutoff */
            width: {{ intval(str_replace('mm', '', $setting->paper_size ?? '80')) - 8 }}mm;
            max-width: {{ intval(str_replace('mm', '', $setting->paper_size ?? '80')) - 8 }}mm;

            /* Padding must be precise to prevent overflow */
            padding: 3mm 2mm;
            /* Reduced horizontal padding to prevent overflow */

            /* Alignment and appearance */
            color: black;
            margin: 0;
            /* No automatic margins, we control positioning precisely */
            text-align: left;
            /* Default left alignment for most content */

            /* Print settings */
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-weight: bold;
            /* Bold text improves readability on dot matrix */

            /* Ensure content stays within the page */
            overflow-x: hidden;
            word-wrap: break-word;
            /* Prevent long text from causing horizontal overflow */
        }

        /* Core container - precisely sized to prevent overflow */
        .invoice-box {
            width: {{ intval(str_replace('mm', '', $setting->paper_size ?? '80')) - 12 }}mm;
            /* Width calculation with safety margin */
            padding: 0;
            margin: 0 auto;
            /* Center the container */
            position: relative;
            overflow: hidden;
            /* Prevent overflow */
        }

        /* HEADER SECTION - improved for better printing */
        .header {
            text-align: center;
            padding: 2px 0;
            margin-bottom: 5px;
            width: 100%;
            overflow: hidden;
            /* Prevent overflow */
        }

        .company-name {
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
            font-size: 14px;
            /* Adjusted size */
            letter-spacing: 0.7px;
            /* Better character separation for dot matrix */
            word-spacing: 2px;
            /* Add space between words for better readability */
        }

        /* DIVIDERS - Optimized patterns for better visibility on both printer types */
        .divider {
            border-bottom: none;
            border-top: none;
            height: 1px;
            /* Changed to simple 1px height for consistent appearance */
            background: #000;
            /* Solid black line works better across printer types */
            margin: 6px 0;
            clear: both;
            width: 100%;
        }

        /* Alternative dotted divider style for thermal printers */
        .dotted-divider {
            border-bottom: none;
            border-top: none;
            height: 1px;
            border-top: 1px dotted #000;
            /* Dotted style works well on thermal */
            margin: 6px 0;
            clear: both;
            width: 100%;
        }

        /* TRANSACTION INFO - Optimized spacing and layout */
        .info-section {
            margin-bottom: 5px;
            width: 100%;
            overflow: hidden;
            /* Prevent overflow */
        }

        .info-row {
            display: flex;
            flex-wrap: nowrap;
            /* Prevent wrapping */
            margin: 2px 0;
            width: 100%;
            overflow: hidden;
            /* Prevent overflow */
        }

        .info-label {
            width: 65px;
            /* Reduced width */
            text-align: left;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .info-colon {
            width: 5px;
            text-align: left;
            flex-shrink: 0;
            /* Prevent shrinking */
        }

        .info-value {
            width: calc(100% - 130px);
            /* Adjusted calculation */
            text-align: left;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .info-date {
            width: 60px;
            /* Reduced width */
            text-align: right;
            font-size: 10px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            flex-shrink: 0;
            /* Prevent shrinking */
        }

        /* ITEMS SECTION - Optimized for narrow paper */
        .items-section {
            width: 100%;
            margin-bottom: 5px;
            overflow: hidden;
            /* Prevent overflow */
        }

        .item-row {
            margin-bottom: 4px;
            width: 100%;
            overflow: hidden;
            /* Prevent overflow */
        }

        .item-name {
            font-weight: bold;
            text-transform: uppercase;
            width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 10px;
            /* Smaller font size for product names */
            letter-spacing: 0.5px;
        }

        .item-detail {
            display: flex;
            flex-wrap: nowrap;
            width: 100%;
            margin-top: 1px;
            overflow: hidden;
            /* Prevent overflow */
        }

        .item-quantity {
            width: 58%;
            /* Adjusted width */
            text-align: left;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 10px;
            /* Slightly smaller */
        }

        .item-total {
            width: 42%;
            /* Adjusted width */
            text-align: right;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 10px;
            /* Slightly smaller */
        }

        /* SUMMARY SECTION - Optimized for space efficiency */
        .summary-section,
        .payment-info {
            margin-top: 5px;
            margin-bottom: 5px;
            width: 100%;
            overflow: hidden;
            /* Prevent overflow */
        }

        .summary-row {
            display: flex;
            flex-wrap: nowrap;
            margin: 2px 0;
            width: 100%;
            overflow: hidden;
            /* Prevent overflow */
        }

        .summary-label {
            width: 80px;
            /* Adjusted width */
            text-align: left;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .summary-colon {
            width: 5px;
            text-align: left;
            flex-shrink: 0;
            /* Prevent shrinking */
        }

        .summary-value {
            width: calc(100% - 85px);
            /* Adjusted calculation */
            text-align: right;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            margin-top: 5px;
            padding-top: 2px;
            padding-bottom: 2px;
            width: 100%;
            font-size: 10px;
            overflow: hidden;
            /* Prevent overflow */
        }

        /* Text utilities */
        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }

        .font-bold {
            font-weight: bold !important;
        }

        /* Size utilities */
        .mb-1 {
            margin-bottom: 3px !important;
        }

        .mb-2 {
            margin-bottom: 6px !important;
        }

        /* Tables - only used when absolutely necessary */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* Fixed table layout prevents overflow */
        }

        table.items td {
            padding: 2px 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* PRINT-SPECIFIC STYLES - Critical for proper printing */
        @media print {

            html,
            body {
                width: {{ $setting->paper_size ?? '80mm' }} !important;
                max-width: {{ $setting->paper_size ?? '80mm' }} !important;
                min-width: {{ $setting->paper_size ?? '80mm' }} !important;
                height: auto !important;
                margin: 0 !important;
                padding: 2mm 1mm !important;
                /* Reduced horizontal padding for print */
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                font-weight: bold !important;
                letter-spacing: 0.5px !important;
                /* Force letter spacing in print */
                overflow: hidden !important;
            }

            .invoice-box {
                width: {{ intval(str_replace('mm', '', $setting->paper_size ?? '80')) - 4 }}mm !important;
                /* Wider in print context */
                margin: 0 auto !important;
                overflow: hidden !important;
            }

            /* Hide print controls */
            .no-print {
                display: none !important;
            }

            /* Ensure all text is bold for better print quality */
            * {
                font-weight: bold !important;
                color: black !important;
                overflow: hidden !important;
                max-width: 100% !important;
            }

            /* Extra emphasis for important elements */
            .company-name,
            .item-name,
            .summary-row.font-bold {
                font-weight: 900 !important;
                letter-spacing: 0.7px !important;
            }

            /* Simple dividers print better */
            .divider,
            .footer-divider,
            .dotted-divider {
                border-top: 1px solid black !important;
                background: none !important;
                height: 1px !important;
            }

            /* Space for paper cutting */
            body::after {
                content: "";
                display: block;
                height: 20mm !important;
            }
        }

        /* SCREEN CONTROLS - Only visible on screen */
        .no-print {
            text-align: center;
            margin-top: 15px;
            padding: 8px;
            border-top: 1px dashed black;
            width: 100%;
        }

        .no-print button {
            padding: 8px 15px;
            margin: 0 5px;
            cursor: pointer;
            font-weight: bold;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
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

        <div class="divider"></div>

        @if (isset($is_test))
            <!-- TEST PRINT SECTION -->
            <div class="text-center mb-2">
                <div class="font-bold">TEST PRINT</div>
                <div>{{ $test_time->format('d/m/Y H:i:s') }}</div>
            </div>
            <div class="info-section">
                <div class="info-row">
                    <span class="info-label">PaperSize</span>
                    <span class="info-colon">:</span>
                    <span class="info-value">{{ $setting->paper_size }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Printer</span>
                    <span class="info-colon">:</span>
                    <span class="info-value">{{ $setting->printer_name ?? 'Default' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">AutoPrint</span>
                    <span class="info-colon">:</span>
                    <span class="info-value">{{ $setting->auto_print ? 'Yes' : 'No' }}</span>
                </div>
            </div>
        @else
            <!-- TRANSACTION INFO -->
            <div class="info-section">
                <div class="info-row">
                    <span class="info-label">No Invoice</span>
                    <span class="info-colon">:</span>
                    <span class="info-value">{{ $transaction->invoice_number }}</span>
                    <span class="info-date">{{ $transaction->created_at->format('d/m/y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal</span>
                    <span class="info-colon">:</span>
                    <span class="info-value">{{ $transaction->created_at->format('H:i:s') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Kasir</span>
                    <span class="info-colon">:</span>
                    <span class="info-value">{{ $transaction->user->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Customer</span>
                    <span class="info-colon">:</span>
                    <span class="info-value">{{ $transaction->customer->name }}</span>
                </div>
            </div>

            <div class="dotted-divider"></div>

            <!-- ITEMS -->
            <div class="items-section">
                @foreach ($transaction->items as $item)
                    <div class="item-row">
                        <div class="item-name">{{ $item->product->name }}</div>
                        <div class="item-detail">
                            <div class="item-quantity">{{ number_format($item->quantity, 2) }}
                                {{ $item->unit->name ?? 'PCS' }} x
                                {{ number_format($item->unit_price, 0, ',', '.') }}</div>
                            <div class="item-total">{{ number_format($item->subtotal, 0, ',', '.') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="dotted-divider"></div>

            <!-- SUMMARY SECTION -->
            <div class="payment-info">
                <div class="summary-row">
                    <span class="summary-label">Subtotal</span>
                    <span class="summary-colon">:</span>
                    <span class="summary-value">{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                </div>
                @if ($transaction->tax_amount > 0)
                    <div class="summary-row">
                        <span class="summary-label">Pajak</span>
                        <span class="summary-colon">:</span>
                        <span class="summary-value">{{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if ($transaction->discount_amount > 0)
                    <div class="summary-row">
                        <span class="summary-label">Diskon</span>
                        <span class="summary-colon">:</span>
                        <span
                            class="summary-value">{{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="summary-row font-bold">
                    <span class="summary-label">Total</span>
                    <span class="summary-colon">:</span>
                    <span class="summary-value">{{ number_format($transaction->final_amount, 0, ',', '.') }}</span>
                </div>
                @if ($transaction->payment_type == 'cash')
                    <div class="summary-row">
                        <span class="summary-label">Tunai</span>
                        <span class="summary-colon">:</span>
                        <span class="summary-value">{{ number_format($transaction->cash_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Kembali</span>
                        <span class="summary-colon">:</span>
                        <span
                            class="summary-value">{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                    </div>
                @else
                    <div class="summary-row">
                        <span class="summary-label">Pembayaran</span>
                        <span class="summary-colon">:</span>
                        <span class="summary-value">Transfer</span>
                    </div>
                @endif
            </div>

            <div class="dotted-divider"></div>
        @endif

        <!-- FOOTER -->
        <div class="footer">
            <div>Terima kasih atas kunjungan Anda</div>
        </div>

        <div class="dotted-divider"></div>
    </div>

    <!-- PRINT CONTROLS -->
    <div class="no-print">
        <button onclick="printInvoice()">Cetak Struk</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    <!-- Enhanced Print Scripts -->
    <script>
        // Improved print handling function
        function printInvoice() {
            // Prepare document for optimal printing
            document.body.style.width = '{{ $setting->paper_size ?? '80mm' }}';

            // Force a small delay to ensure styling is applied
            setTimeout(function() {
                window.print();
            }, 200);
        }

        // Auto-print functionality with improved timing
        @if ($setting->auto_print ?? false)
            window.onload = function() {
                // Add a longer delay before printing to ensure complete rendering
                setTimeout(function() {
                    printInvoice();

                    // Longer delay before closing to ensure print completes
                    setTimeout(function() {
                        window.close();
                    }, 1500);
                }, 500);
            };
        @endif

        // Enhanced rendering fixes
        document.addEventListener('DOMContentLoaded', function() {
            // Fix for ensuring proper width calculations
            const paperWidth = '{{ intval(str_replace('mm', '', $setting->paper_size ?? '80')) }}';
            const invoiceBox = document.querySelector('.invoice-box');

            // Apply specific fixes for narrow receipts
            if (paperWidth <= 80) {
                // Adjust font sizes for narrower receipts
                document.body.style.fontSize = '10px';

                // Make all elements honor their container width
                const elements = document.querySelectorAll('*');
                elements.forEach(function(el) {
                    el.style.maxWidth = '100%';
                    el.style.boxSizing = 'border-box';
                });
            }

            // Prepare for print event
            window.addEventListener('beforeprint', function() {
                // Ensure all text fits within the paper width
                document.querySelectorAll('.info-value, .item-name, .item-quantity, .item-total').forEach(
                    function(el) {
                        if (el.scrollWidth > el.clientWidth) {
                            // Text is overflowing, reduce its content
                            const originalText = el.textContent;
                            let text = originalText;

                            // Truncate text until it fits
                            while (el.scrollWidth > el.clientWidth && text.length > 3) {
                                text = text.slice(0, -1);
                                el.textContent = text + '...';
                            }
                        }
                    });
            });
        });
    </script>
</body>

</html>
