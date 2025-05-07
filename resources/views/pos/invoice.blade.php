<!-- resources/views/pos/invoice.blade.php - Optimized for 78mm dot matrix printer -->
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ isset($is_test) ? 'Test Print' : 'Invoice ' . $transaction->invoice_number }}</title>

    <style>
        /* Page Settings - Precisely optimized for 80mm dot matrix printer */
        @page {
            margin: 0;
            size: {{ $setting->paper_size ?? '80mm' }} auto;
            /* Set width based on settings, defaulting to 80mm */
        }

        /* Reset and base styles with improved precision */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            /* Optimized font settings for dot matrix */
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            /* Better readability on dot matrix */
            line-height: 1.5;
            /* Increased line height for better separation */
            letter-spacing: 0.3px;
            /* Slightly increased letter spacing to prevent character overlap */
            width: calc({{ str_replace('mm', '', $setting->paper_size ?? '80') - 6 }}mm);
            /* Reduced width to ensure content stays within printable area */
            max-width: calc({{ str_replace('mm', '', $setting->paper_size ?? '80') - 6 }}mm);
            padding: 3mm 3mm;
            /* Increased and evenly distributed padding */
            color: black;
            margin: 0 auto;
            /* Center alignment */
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-weight: bold;
            /* Bold text to avoid faded/broken characters */
        }

        /* Core container - improved centering and stability */
        .invoice-box {
            width: calc({{ str_replace('mm', '', $setting->paper_size ?? '80') - 10 }}mm);
            /* Adjusted width for better centering */
            padding: 0;
            margin: 0 auto;
            position: relative;
            /* Added positioning context */
            left: 0;
            /* Ensure left alignment at start */
            transform: translateX(0);
            /* No transformation by default */
        }

        /* HEADER SECTION - improved for dot matrix */
        .header {
            text-align: center;
            padding: 3px 0 4px 0;
            /* Better padding for header */
            margin-bottom: 6px;
            /* Increased margin for better separation */
            width: 100%;
        }

        .company-name {
            font-weight: bold;
            margin-bottom: 3px;
            /* Better spacing */
            text-transform: uppercase;
            width: 100%;
            font-size: 16px;
            /* Larger font for better visibility */
            letter-spacing: 0.5px;
            /* Better character separation */
        }

        /* CLEAR DIVIDERS - Optimized for dot matrix */
        .divider {
            border-bottom: none;
            border-top: none;
            height: 1px;
            background-image: repeating-linear-gradient(to right, #000, #000 3px, transparent 3px, transparent 6px);
            /* Optimized dot pattern - shorter spacing for clearer printing */
            margin: 8px 0;
            /* Increased margin for better section separation */
            clear: both;
            width: 100%;
            position: relative;
            left: 0;
            /* Ensure proper alignment */
        }

        /* Add more prominent dividers for clearer section separation */
        .footer-divider {
            border-bottom: none;
            border-top: none;
            height: 2px;
            /* Slightly thicker for better visibility */
            background-image: repeating-linear-gradient(to right, #000, #000 4px, transparent 4px, transparent 8px);
            margin: 8px 0;
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

        /* Text utilities */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .mb-1 {
            margin-bottom: 5px;
        }

        .mb-2 {
            margin-bottom: 10px;
        }

        /* Tables */
        table {
            width: 100%;
        }

        table.items td {
            padding: 3px 0;
        }

        /* PRINT CONTROLS - Enhanced for dot matrix printing */
        @media print {

            html,
            body {
                width: {{ $setting->paper_size ?? '80mm' }};
                max-width: {{ $setting->paper_size ?? '80mm' }};
                margin: 0 auto;
                padding: 3mm 3mm;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                font-weight: bold !important;
                /* Force bold in print */
                letter-spacing: 0.3px !important;
                /* Force letter spacing in print */
            }

            .invoice-box {
                width: calc({{ str_replace('mm', '', $setting->paper_size ?? '80') - 10 }}mm);
                /* Adjusted width for better centering */
                position: relative;
                left: 50%;
                transform: translateX(-50%);
                /* Center perfectly on print */
            }

            .no-print {
                display: none;
            }

            /* Ensure all text is bold for better print quality */
            * {
                font-weight: bold !important;
                color: black !important;
            }

            /* Better contrast for important elements */
            .company-name,
            .item-name,
            .summary-row.font-bold {
                font-weight: 900 !important;
                /* Extra bold */
            }

            /* Space for paper cutting */
            body::after {
                content: "";
                display: block;
                height: 18mm;
                /* Increased height for better paper cutting space */
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
                    <span class="info-label">Paper Size</span>
                    <span class="info-colon">:</span>
                    <span class="info-value">{{ $setting->paper_size }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Printer</span>
                    <span class="info-colon">:</span>
                    <span class="info-value">{{ $setting->printer_name ?? 'Default Printer' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Auto Print</span>
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
                    <span class="info-date">{{ $transaction->created_at->format('d/m/Y') }}</span>
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

            <div class="divider"></div>

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

            <div class="divider"></div>

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

            <div class="footer-divider"></div>
        @endif

        <!-- FOOTER -->
        <div class="footer">
            <div>Terima kasih atas kunjungan Anda</div>
        </div>

        <div class="footer-divider"></div>
    </div>

    <!-- PRINT CONTROLS -->
    <div class="no-print">
        <button onclick="window.print()">Cetak Struk</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    @if ($setting->auto_print ?? false)
        <script>
            window.onload = function() {
                // Add a small delay before printing to ensure full rendering
                setTimeout(function() {
                    window.print();
                    // Add longer delay before closing to ensure print completes
                    setTimeout(function() {
                        window.close();
                    }, 1000);
                }, 300);
            };
        </script>
    @endif

    <!-- Script to fix centering issues on problematic browsers -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Force repaint to stabilize layout before printing
            document.body.style.display = 'none';
            document.body.offsetHeight; // Force reflow
            document.body.style.display = '';

            // Add print listener to ensure proper rendering
            window.addEventListener('beforeprint', function() {
                // Set explicit width one more time before printing
                document.body.style.width = '{{ $setting->paper_size ?? '80mm' }}';
            });
        });
    </script>
</body>

</html>
