<!-- resources/views/pos/invoice.blade.php -->
<!DOCTYPE html>
<html>

<head>
    <title>Invoice #{{ $transaction->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }

        .invoice-box {
            max-width: 80mm;
            margin: auto;
            padding: 20px;
            border: 1px solid #eee;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
        }

        .info {
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .totals {
            margin-top: 20px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
        }

        @media print {
            body {
                padding: 0;
            }

            .invoice-box {
                border: none;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="header">
            <div class="company-name">{{ $company['name'] }}</div>
            <div>{{ $company['address'] }}</div>
            <div>{{ $company['phone'] }}</div>
        </div>

        <div class="info">
            <div class="info-row">
                <span>No. Invoice:</span>
                <span>#{{ $transaction->invoice_number }}</span>
            </div>
            <div class="info-row">
                <span>Tanggal:</span>
                <span>{{ $transaction->invoice_date->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span>Kasir:</span>
                <span>{{ $transaction->user->name }}</span>
            </div>
            <div class="info-row">
                <span>Customer:</span>
                <span>{{ $transaction->customer->name }}</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->items as $item)
                    <tr>
                        <td>
                            {{ $item->product->name }}
                            @if ($item->product->discount)
                                <br>
                                <small>Disc: {{ $item->discount }}</small>
                            @endif
                        </td>
                        <td>{{ number_format($item->quantity, 0) }} {{ $item->unit->name }}</td>
                        <td>{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td>{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>
            @if ($transaction->tax_amount > 0)
                <div class="total-row">
                    <span>Pajak:</span>
                    <span>{{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            @if ($transaction->discount_amount > 0)
                <div class="total-row">
                    <span>Diskon:</span>
                    <span>{{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="total-row" style="font-weight: bold;">
                <span>Total:</span>
                <span>{{ number_format($transaction->final_amount, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span>Pembayaran:</span>
                <span>{{ strtoupper($transaction->payment_type) }}</span>
            </div>
            @if ($transaction->reference_number)
                <div class="total-row">
                    <span>No. Ref:</span>
                    <span>{{ $transaction->reference_number }}</span>
                </div>
            @endif
        </div>

        <div class="footer">
            <p>Terima kasih telah berbelanja</p>
            <p>Barang yang sudah dibeli tidak dapat dikembalikan</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Print Invoice</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    <script>
        // Auto print saat halaman dimuat
        window.onload = function() {
            // Delay print untuk memastikan style sudah dimuat
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>

</html>
