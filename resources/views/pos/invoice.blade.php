<!-- resources/views/pos/invoice.blade.php - Optimized for POS Thermal Printers -->
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
            /* Font yang lebih mirip aplikasi kasir lama - lebih monospace dan lebih gelap */
            font-family: consolas, monospace;
            font-size: 10px;
            /* Diubah menjadi 10px untuk semua ukuran */
            line-height: 1.2;
            width: {{ request('size') == '57' ? '57mm' : '65mm' }};
            max-width: 100%;
            padding: 0;
            color: black;
            margin: 0 auto;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-weight: 700;
            letter-spacing: 0;
        }

        /* Core container */
        .invoice-box {
            width: 100%;
            padding: 1px;
        }

        /* HEADER SECTION */
        .header {
            text-align: center;
            padding: 2px 0 5px 0;
            margin-bottom: 5px;
        }

        .company-name {
            font-weight: bold;
            font-size: 13px;
            /* Diubah menjadi 13px untuk konsistensi */
            letter-spacing: 0;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        /* CLEAR DIVIDERS */
        .divider {
            border-bottom: 1px dashed black;
            margin: 5px 0;
            clear: both;
        }

        /* TRANSACTION INFO */
        .info-section {
            margin-bottom: 5px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
            overflow: hidden;
            white-space: nowrap;
        }

        .info-label {
            font-weight: bold;
            letter-spacing: 0;
            font-size: 10px;
            /* Diubah menjadi 10px */
            text-transform: uppercase;
        }

        /* ITEMS TABLE - Traditional format with no borders */
        .items-table {
            width: 100%;
            margin: 5px 0;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .items-table th {
            text-align: left;
            padding: 2px 1px;
            font-weight: bold;
            font-size: 10px;
            border-bottom: 1px dashed black;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 2px 1px;
            vertical-align: top;
            word-break: break-word;
            font-size: 10px;
            /* Diubah menjadi 10px */
        }

        /* Adjusted width proportions */
        .items-table th:first-child,
        .items-table td:first-child {
            width: 70%;
        }

        .items-table th:last-child,
        .items-table td:last-child {
            width: 30%;
            text-align: right;
        }

        .item-name {
            font-weight: bold;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: 0;
            font-size: 10px;
            /* Diubah menjadi 10px */
        }

        .item-detail {
            padding-left: 3px;
            font-size: 10px;
            /* Diubah menjadi 10px */
            font-weight: normal;
        }

        /* SUMMARY SECTION */
        .summary-section {
            margin-top: 5px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
            padding-right: 1px;
        }

        /* GRAND TOTAL - Very clear */
        .grand-total {
            font-weight: bold;
            font-size: 11px;
            /* Diubah menjadi 11px untuk konsistensi */
            border-top: 1px dashed black;
            border-bottom: 1px dashed black;
            padding: 3px 0;
            margin: 5px 0;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        /* PAYMENT METHOD */
        .payment-method {
            text-align: center;
            font-weight: bold;
            margin: 5px 0;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            margin-top: 10px;
            border-top: 1px dashed black;
            padding-top: 5px;
        }

        .footer-text {
            font-weight: bold;
            font-size: 10px;
            letter-spacing: 0;
            text-transform: uppercase;
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

            /* Improve print quality */
            * {
                text-rendering: optimizeLegibility;
                -webkit-font-smoothing: antialiased;
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
            border-top: 1px dashed black;
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
        <!-- HEADER - Simple centered logo and text -->
        <div class="header">
            <div class="company-name">{{ $company['name'] }}</div>
            <div>{{ $company['address'] }}</div>
            <div>Telp: {{ $company['phone'] }}</div>
        </div>

        <!-- TRANSACTION INFO - Standard format -->
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">STRUK</span>
                <span>#{{ $transaction->invoice_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">TANGGAL</span>
                <span>{{ $transaction->invoice_date->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">KASIR</span>
                <span>{{ $transaction->user->name }}</span>
            </div>

            @if ($transaction->customer && $transaction->customer_id != 1)
                <div class="info-row">
                    <span class="info-label">PELANGGAN</span>
                    <span>{{ $transaction->customer->name }}</span>
                </div>
            @endif
        </div>

        <div class="divider"></div>

        <!-- ITEMS TABLE - Simplified traditional format -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>ITEM</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->items as $item)
                    <tr>
                        <td>
                            <div class="item-name">{{ $item->product->name }}</div>
                            <div class="item-detail">
                                {{ number_format($item->quantity, 0) }} {{ $item->unit->name ?? 'pcs' }}
                                x
                                {{ number_format($item->unit_price, 0, ',', '.') }}
                            </div>
                        </td>
                        <td>
                            {{ number_format($item->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- SUMMARY SECTION - Traditional format with right-aligned values -->
        <div class="summary-section">
            <div class="summary-row">
                <span>Subtotal</span>
                <span>{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>

            @if ($transaction->discount_amount > 0)
                <div class="summary-row">
                    <span>Diskon</span>
                    <span>-{{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif

            @if ($transaction->tax_amount > 0)
                <div class="summary-row">
                    <span>Pajak (10%)</span>
                    <span>{{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
                </div>
            @endif

            <!-- GRAND TOTAL - Emphasized with dashed borders -->
            <div class="grand-total">
                <div class="summary-row">
                    <span>TOTAL BAYAR</span>
                    <span>{{ number_format($transaction->final_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- PAYMENT INFO -->
            <div class="payment-method">
                {{ strtoupper($transaction->payment_type) }}
            </div>

            @if ($transaction->payment_type === 'cash')
                <div class="summary-row">
                    <span>Tunai</span>
                    <span>{{ number_format($transaction->cash_amount, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span>Kembalian</span>
                    <span>{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                </div>
            @endif

            @if ($transaction->reference_number)
                <div class="summary-row">
                    <span>No. Ref</span>
                    <span>{{ $transaction->reference_number }}</span>
                </div>
            @endif
        </div>

        <!-- FOOTER - Traditional with thankful message -->
        <div class="footer">
            <div class="footer-text">TERIMA KASIH</div>
            <div>Barang yang sudah dibeli tidak dapat dikembalikan</div>
            <div style="font-size: 7px; margin-top: 3px">
                {{ $transaction->store_id }}/{{ $transaction->id }}/{{ now()->format('YmdHi') }}
            </div>
        </div>
    </div>

    <!-- PRINT CONTROLS - Not visible when printed -->
    <div class="no-print">
        <button onclick="window.print()">Cetak Struk</button>
        <button onclick="window.close()">Tutup</button>
    </div>
</body>

</html>
