<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $transaction->invoice_number }}</title>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0;
            size: {{ request('size') == '57' ? '57mm' : '78mm' }} auto;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: {{ request('size') == '57' ? '11px' : '13px' }};
            width: 100%;
            max-width: {{ request('size') == '57' ? '57mm' : '78mm' }};
            padding-right: 5mm; /* Menambahkan padding kanan */
        }
        .invoice-box {
            padding: 5px;
            width: 100%;
        }
        .header, .footer {
            text-align: center;
            font-weight: bold;
        }
        .divider {
            border-top: 1px dashed black;
            margin: 5px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: {{ request('size') == '57' ? '10px' : '12px' }};
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: {{ request('size') == '57' ? '11px' : '13px' }};
            font-weight: bold;
        }
        .grand-total {
            display: flex;
            justify-content: space-between;
            font-size: {{ request('size') == '57' ? '12px' : '14px' }};
            font-weight: bold;
            border-top: 2px solid black;
            border-bottom: 2px solid black;
            padding: 4px 0;
        }
        /* Tambahkan titik di kanan agar tidak terpotong */
        .right-padding {
            text-align: right;
            font-size: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="invoice-box">
    <div class="header">
        {{ $company['name'] }} <br>
        {{ $company['address'] }} <br>
        Tel: {{ $company['phone'] }}
    </div>
    <hr class="divider">
    <div class="info-row">
        <span>No. Invoice:</span>
        <span>#{{ $transaction->invoice_number }}</span>
    </div>
    <div class="info-row">
        <span>Tanggal:</span>
        <span>{{ $transaction->invoice_date->format('d/m/Y') }}</span>
    </div>
    <div class="info-row">
        <span>Kasir:</span>
        <span>{{ $transaction->user->name }}</span>
    </div>
    <hr class="divider">
    <table style="width: 100%;">
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
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td>{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <hr class="divider">
    <div class="total-row">
        <span>Subtotal:</span>
        <span>{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
    </div>
    @if($transaction->tax_amount > 0)
        <div class="total-row">
            <span>Pajak ({{ $transaction->tax_percentage ?? 11 }}%):</span>
            <span>{{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
        </div>
    @endif
    @if($transaction->discount_amount > 0)
        <div class="total-row">
            <span>Diskon:</span>
            <span>{{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
        </div>
    @endif
    <div class="grand-total">
        <span>TOTAL</span>
        <span>Rp {{ number_format($transaction->final_amount, 0, ',', '.') }}</span>
    </div>
    <div class="total-row">
        <span>Tunai:</span>
        <span>{{ number_format($transaction->cash_amount, 0, ',', '.') }}</span>
    </div>
    <div class="total-row">
        <span>Kembalian:</span>
        <span>{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
    </div>
    <div class="right-padding">...........................</div> <!-- Menambahkan spasi kanan -->
    <hr class="divider">
    <div class="footer">
        TERIMA KASIH TELAH BERBELANJA <br>
        Barang yang sudah dibeli tidak dapat dikembalikan
    </div>
</div>
<script>
    window.onload = function() {
        setTimeout(function() {
            window.print();
        }, 1200);
    };
</script>
</body>
</html>
