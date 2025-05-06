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
            size: {{ request('size') == '57' ? '57mm' : '78mm' }} auto;
        }

        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Courier New", Courier, monospace;
            font-size: {{ request('size') == '57' ? '9px' : '10px' }};
            line-height: 1.2;
            width: {{ request('size') == '57' ? '57mm' : '78mm' }};
            max-width: 100%;
            padding: 0;
            color: black;
        }

        /* Core container */
        .invoice-box {
            width: 100%;
            padding: 2px;
        }

        /* HEADER SECTION */
        .header {
            text-align: center;
            padding: 3px 0;
            border-bottom: 1px solid black;
            margin-bottom: 5px;
        }

        .company-name {
            font-weight: bold;
            font-size: {{ request('size') == '57' ? '11px' : '12px' }};
        }

        /* CLEAR DIVIDERS */
        .divider {
            border-bottom: 1px solid black;
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
        }

        .info-label {
            font-weight: bold;
        }

        /* ITEMS TABLE - Minimal borders for clarity */
        .items-table {
            width: 100%;
            margin: 5px 0;
            border-collapse: collapse;
        }

        .items-table th {
            text-align: left;
            padding: 2px;
            border-bottom: 1px solid black;
            font-weight: bold;
        }

        .items-table td {
            padding: 2px;
            vertical-align: top;
        }

        .item-name {
            font-weight: bold;
        }

        .item-detail {
            padding-left: 5px;
        }

        /* SUMMARY SECTION */
        .summary-section {
            margin-top: 5px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }

        /* GRAND TOTAL - Very clear */
        .grand-total {
            font-weight: bold;
            font-size: {{ request('size') == '57' ? '10px' : '11px' }};
            border-top: 1px solid black;
            border-bottom: 1px solid black;
            padding: 3px 0;
            margin: 5px 0;
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
            border-top: 1px solid black;
            padding-top: 5px;
        }

        .footer-text {
            font-weight: bold;
        }

        /* PRINT CONTROLS */
        @media print {

            html,
            body {
                width: {{ request('size') == '57' ? '57mm' : '78mm' }};
                margin: 0;
                padding: 0;
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
            margin-top: 20px;
            padding: 10px;
            border-top: 1px dashed black;
        }

        .no-print button {
            padding: 10px;
            margin: 0 5px;
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

        <!-- TRANSACTION INFO -->
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">INVOICE:</span>
                <span>#{{ $transaction->invoice_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">TANGGAL:</span>
                <span>{{ $transaction->invoice_date->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">KASIR:</span>
                <span>{{ $transaction->user->name }}</span>
            </div>

            @if ($transaction->customer && $transaction->customer_id != 1)
                <div class="info-row">
                    <span class="info-label">PELANGGAN:</span>
                    <span>{{ $transaction->customer->name }}</span>
                </div>
            @endif
        </div>

        <div class="divider"></div>

        <!-- ITEMS TABLE -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>ITEM</th>
                    <th style="text-align: right">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->items as $item)
                    <tr>
                        <td style="width: 65%">
                            <div class="item-name">{{ $item->product->name }}</div>
                            <div class="item-detail">
                                {{ number_format($item->quantity, 0) }} {{ $item->unit->name ?? 'pcs' }}
                                x
                                {{ number_format($item->unit_price, 0, ',', '.') }}
                            </div>
                        </td>
                        <td style="width: 35%; text-align: right">
                            {{ number_format($item->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- SUMMARY SECTION -->
        <div class="summary-section">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>

            @if ($transaction->discount_amount > 0)
                <div class="summary-row">
                    <span>Diskon:</span>
                    <span>-{{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif

            @if ($transaction->tax_amount > 0)
                <div class="summary-row">
                    <span>Pajak (10%):</span>
                    <span>{{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
                </div>
            @endif

            <!-- GRAND TOTAL -->
            <div class="grand-total">
                <div class="summary-row">
                    <span>TOTAL BAYAR:</span>
                    <span>{{ number_format($transaction->final_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- PAYMENT INFO -->
            <div class="payment-method">
                {{ strtoupper($transaction->payment_type) }}
            </div>

            @if ($transaction->payment_type === 'cash')
                <div class="summary-row">
                    <span>Tunai:</span>
                    <span>{{ number_format($transaction->cash_amount, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span>Kembalian:</span>
                    <span>{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                </div>
            @endif

            @if ($transaction->reference_number)
                <div class="summary-row">
                    <span>No. Ref:</span>
                    <span>{{ $transaction->reference_number }}</span>
                </div>
            @endif
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <div class="footer-text">TERIMA KASIH</div>
            <div>Barang yang sudah dibeli tidak dapat dikembalikan</div>
            <div style="font-size: 7px; margin-top: 5px">
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
