<!-- resources/views/pos/invoice.blade.php - Optimized for both thermal and dot matrix printers at 76mm -->
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>{{ isset($is_test) ? 'Test Print' : 'Invoice ' . $transaction->invoice_number }}</title>

    <style>
        /* Reset & Page Settings */
        @page {
            margin: 0;
            size: 76mm auto;
            /* Fixed to 76mm as requested */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box !important;
        }

        /* Base Styles - Simple and Effective */
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            font-weight: bold;
            line-height: 1.2;
            width: 72mm;
            /* 76mm - 4mm margin */
            max-width: 72mm;
            padding: 2mm 2mm;
            color: black;
            margin: 0 auto;
            text-align: left;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Main Container */
        .invoice-box {
            width: 68mm;
            margin: 0 auto;
            padding: 0;
        }

        /* Text Alignment Utilities */
        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }

        /* Headers */
        .company-name {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 2px;
        }

        .company-address {
            text-align: center;
            margin-bottom: 2px;
        }

        .company-phone {
            text-align: center;
            margin-bottom: 5px;
        }

        /* Divider - Simple dashed style like in the example */
        .divider {
            border-top: 1px dashed #000;
            height: 1px;
            width: 100%;
            margin: 5px 0;
        }

        /* Invoice Details Row - Three column format with colon */
        .detail-row {
            display: flex;
            margin: 2px 0;
        }

        .detail-label {
            width: 60px;
            text-align: left;
        }

        .detail-colon {
            width: 5px;
            text-align: left;
        }

        .detail-value {
            flex: 1;
            text-align: left;
        }

        .detail-date {
            width: 60px;
            text-align: right;
            font-size: 11px;
        }

        /* Products */
        .product-name {
            font-weight: bold;
            margin-top: 2px;
        }

        .product-price-row {
            display: flex;
            justify-content: space-between;
        }

        .product-quantity {
            flex: 1;
            text-align: left;
        }

        .product-total {
            text-align: right;
        }

        .product-discount {
            padding-left: 10px;
            display: flex;
            justify-content: space-between;
        }

        /* Total Section */
        .total-row {
            display: flex;
            margin: 2px 0;
            width: 100%;
        }

        .total-label {
            width: 88px;
            /* Increased width for consistent alignment */
            text-align: left;
        }

        .total-colon {
            width: 8px;
            text-align: left;
        }

        .total-value {
            width: calc(100% - 96px);
            /* Adjusted to accommodate label and colon */
            text-align: right;
        }

        /* Footer - improved to prevent cutoff */
        .footer {
            text-align: center;
            margin-top: 5px;
            margin-bottom: 5px;
            font-size: 11px;
            line-height: 1.3;
            width: 100%;
            /* Critical for preventing cutoff */
            overflow: hidden;
            word-wrap: break-word;
        }

        /* Print Specific Styles */
        @media print {

            html,
            body {
                width: 76mm !important;
                max-width: 76mm !important;
                margin: 0 auto !important;
                padding: 1mm 2mm !important;
                font-weight: bold !important;
            }

            .invoice-box {
                width: 72mm !important;
            }

            .no-print {
                display: none !important;
            }

            /* Force black text for better printing */
            * {
                font-weight: bold !important;
                color: black !important;
            }

            /* Add space for cutting */
            body::after {
                content: "";
                display: block;
                height: 15mm !important;
            }
        }

        /* Print Controls - Only shown on screen */
        .no-print {
            text-align: center;
            margin-top: 10px;
            padding: 5px 0;
            border-top: 1px dashed black;
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
        <!-- HEADER - Simple centered format -->
        <div class="company-name">{{ $company['name'] }}</div>
        <div class="company-address">{{ $company['address'] }}</div>
        <div class="company-phone">Telp/WA {{ $company['phone'] }}</div>

        <div class="divider"></div>

        @if (isset($is_test))
            <!-- TEST PRINT -->
            <div class="text-center">
                <div>TEST PRINT</div>
                <div>{{ $test_time->format('d/m/Y H:i:s') }}</div>
            </div>

            <div class="detail-row">
                <span class="detail-label">Paper Size</span>
                <span class="detail-value">{{ $setting->paper_size }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Printer</span>
                <span class="detail-value">{{ $setting->printer_name ?? 'Default' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Auto Print</span>
                <span class="detail-value">{{ $setting->auto_print ? 'Yes' : 'No' }}</span>
            </div>
        @else
            <!-- INVOICE HEADER - Matches the thermal example image format -->
            <div class="detail-row">
                <span class="detail-label">No</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $transaction->invoice_number }}</span>
                <span class="detail-date">{{ $transaction->created_at->format('d/m/y') }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Kasir</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $transaction->user->name }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Pel.</span>
                <span class="detail-colon">:</span>
                <span class="detail-value">{{ $transaction->payment_type == 'cash' ? 'Tunai' : 'Transfer' }}</span>
            </div>

            <div class="divider"></div>

            <!-- PRODUCTS - Exact format with consistent formatting -->
            @foreach ($transaction->items as $item)
                <div class="product-name">{{ strtoupper($item->product->name) }}</div>
                <div class="product-price-row">
                    <div class="product-quantity">{{ number_format($item->quantity, 2) }}
                        {{ $item->unit->name ?? 'PCS' }} x {{ number_format($item->unit_price, 0, ',', '.') }} :</div>
                    <div class="product-total">{{ number_format($item->subtotal, 0, ',', '.') }}</div>
                </div>

                @if ($item->discount > 0)
                    <div class="product-price-row">
                        <div class="product-quantity" style="padding-left: 20px;">Potongan :</div>
                        <div class="product-total">-{{ number_format($item->discount * $item->quantity, 0, ',', '.') }}
                        </div>
                    </div>
                @endif
            @endforeach

            <div class="divider"></div>

            <!-- TOTALS - Exactly matches the new thermal receipt example -->
            <div class="total-row">
                <span class="total-label">Total Jenis</span>
                <span class="total-colon">:</span>
                <span class="total-value">{{ $transaction->items->count() }}</span>
            </div>

            <div class="total-row">
                <span class="total-label">Total Item</span>
                <span class="total-colon">:</span>
                <span class="total-value">{{ number_format($transaction->items->sum('quantity'), 2) }}</span>
            </div>

            <div class="total-row">
                <span class="total-label">Total Jual</span>
                <span class="total-colon">:</span>
                <span class="total-value">{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>

            <div class="divider"></div>

            <!-- PAYMENT INFO - Exactly matches the new thermal receipt example -->
            <div class="total-row">
                <span class="total-label">Total</span>
                <span class="total-colon">:</span>
                <span class="total-value">{{ number_format($transaction->final_amount, 0, ',', '.') }}</span>
            </div>

            @if ($transaction->payment_type == 'cash')
                <div class="total-row">
                    <span class="total-label">Tunai</span>
                    <span class="total-colon">:</span>
                    <span class="total-value">{{ number_format($transaction->cash_amount, 0, ',', '.') }}</span>
                </div>

                <div class="total-row">
                    <span class="total-label">Kembali</span>
                    <span class="total-colon">:</span>
                    <span class="total-value">{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                </div>
            @else
                <div class="total-row">
                    <span class="total-label">Pembayaran</span>
                    <span class="total-colon">:</span>
                    <span class="total-value">Transfer</span>
                </div>
            @endif

            <div class="divider"></div>
        @endif

        <!-- FOOTER - Multi-line like in the thermal example -->
        <div class="footer">
            <div>Terima kasih telah belanja di toko CACHA</div>
            <div>---Kami tunggu kedatangannya kembali---</div>
        </div>

        <div class="divider"></div>
    </div>

    <!-- PRINT CONTROLS -->
    <div class="no-print">
        <button onclick="printInvoice()">Cetak Struk</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    <!-- Super simple print script -->
    <script>
        function printInvoice() {
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
