<!-- resources/views/pos/invoice.blade.php - Styled like CACHA receipt with aligned colons -->
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
            size: {{ request('size') == '57' ? '57mm' : '68mm' }} auto;
        }

        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            /* Font mirip struk CACHA */
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            line-height: 1.2;
            width: {{ request('size') == '57' ? '57mm' : '65mm' }};
            max-width: 100%;
            padding: 0;
            color: black;
            margin: 0 auto;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-weight: normal;
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
            padding: 2px 0 5px 0;
            margin-bottom: 5px;
        }

        .company-name {
            font-weight: bold;
            font-size: 12px;
            letter-spacing: 0;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        /* CLEAR DIVIDERS - Dotted line like CACHA */
        .divider {
            border-bottom: 1px dotted black;
            margin: 5px 0;
            clear: both;
            width: 100%;
            overflow: hidden;
        }

        /* TRANSACTION INFO - Similar to CACHA */
        .info-section {
            margin-bottom: 5px;
        }

        .info-row {
            display: flex;
            margin: 2px 0;
            white-space: nowrap;
        }

        .info-label {
            width: 80px;
            text-align: left;
            font-weight: normal;
            letter-spacing: 0;
            font-size: 10px;
        }

        .info-colon {
            width: 15px;
            text-align: left;
        }

        .info-value {
            flex: 1;
            text-align: left;
        }

        .info-date {
            text-align: right;
            margin-left: auto;
        }

        /* ITEMS SECTION */
        .items-section {
            width: 100%;
        }

        .item-row {
            margin-bottom: 2px;
        }

        .item-name {
            font-weight: bold;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: 0;
            font-size: 10px;
            text-transform: uppercase;
        }

        .item-detail {
            display: flex;
            justify-content: space-between;
            padding-left: 3px;
            font-size: 10px;
            font-weight: normal;
        }

        .item-quantity {
            width: 65%;
            text-align: left;
        }

        .item-total {
            width: 35%;
            text-align: right;
        }

        .item-discount {
            display: flex;
            justify-content: space-between;
            padding-left: 30px;
            font-size: 10px;
            font-weight: normal;
        }

        .item-discount-label {
            width: 65%;
            text-align: left;
        }

        .item-discount-value {
            width: 35%;
            text-align: right;
        }

        /* SUMMARY SECTION */
        .summary-section {
            margin-top: 5px;
        }

        .summary-row {
            display: flex;
            margin: 2px 0;
        }

        .summary-label {
            width: 130px;
            text-align: left;
        }

        .summary-colon {
            width: 15px;
            text-align: left;
        }

        .summary-value {
            flex: 1;
            text-align: right;
        }

        /* PAYMENT INFO */
        .payment-info {
            margin-top: 5px;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            margin-top: 10px;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        .footer-divider {
            border-bottom: 1px dotted black;
            margin: 5px 0;
            clear: both;
            width: 100%;
            overflow: hidden;
        }

        /* PRINT CONTROLS */
        @media print {
            html,
            body {
                width: {{ request('size') == '57' ? '57mm' : '65mm' }};
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

        <!-- ITEMS - Each on a separate row like CACHA -->
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

        <!-- SUMMARY SECTION - Like CACHA with aligned colons -->
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

        <!-- PAYMENT INFO - Like CACHA with aligned colons -->
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

        <!-- FOOTER - Like CACHA -->
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
