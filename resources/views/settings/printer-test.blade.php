<!DOCTYPE html>
<html>
<head>
    <title>Test Print</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @page {
            margin: 0;
            size: {{ $setting->paper_size ?? '78mm' }} auto;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            font-size: {{ (int) $setting->paper_size < 70 ? '10px' : '12px' }};
        }

        .print-box {
            width: 100%;
            max-width: {{ $setting->paper_size ?? '78mm' }};
            margin: 0 auto;
            padding: 10px 5px;
        }

        .text-center {
            text-align: center;
        }

        .header {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #000;
        }

        .content {
            margin: 15px 0;
        }

        .footer {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #000;
            font-size: 10px;
            text-align: center;
        }

        table {
            width: 100%;
            margin: 10px 0;
        }

        td {
            padding: 3px 0;
        }

        .test-item {
            padding: 5px 0;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="print-box">
    <div class="header text-center">
        <div style="font-weight: bold; font-size: 14px;">{{ $store->name }}</div>
        <div>{{ $store->address }}</div>
        <div>{{ $store->phone }}</div>
    </div>

    <div class="content">
        <div class="text-center" style="font-weight: bold; margin-bottom: 10px;">
            TEST PRINT
        </div>

        <div class="test-item">
            <table>
                <tr>
                    <td style="width: 40%;">Tanggal Test</td>
                    <td>: {{ $test_time->format('d/m/Y H:i:s') }}</td>
                </tr>
                <tr>
                    <td>Ukuran Kertas</td>
                    <td>: {{ $setting->paper_size }}</td>
                </tr>
                <tr>
                    <td>Nama Printer</td>
                    <td>: {{ $setting->printer_name ?: 'Default Printer' }}</td>
                </tr>
            </table>
        </div>

        <div class="test-item">
            <div style="margin-bottom: 5px;">Test Karakter:</div>
            <div>ABCDEFGHIJKLMNOPQRSTUVWXYZ</div>
            <div>abcdefghijklmnopqrstuvwxyz</div>
            <div>1234567890</div>
        </div>

        <div class="test-item">
            <div style="margin-bottom: 5px;">Test Alignment:</div>
            <div style="text-align: left;">Rata Kiri</div>
            <div style="text-align: center;">Rata Tengah</div>
            <div style="text-align: right;">Rata Kanan</div>
        </div>

        <div class="test-item">
            <div style="margin-bottom: 5px;">Test Format:</div>
            <div style="font-weight: bold;">Tebal</div>
            <div style="font-style: italic;">Miring</div>
            <div style="text-decoration: underline;">Garis Bawah</div>
        </div>

        <div class="test-item">
            <div style="margin-bottom: 5px;">Test Garis:</div>
            <div style="border-top: 1px solid #000; margin: 5px 0;">Garis Solid</div>
            <div style="border-top: 1px dashed #000; margin: 5px 0;">Garis Putus-putus</div>
        </div>
    </div>

    <div class="footer">
        <div>--- Test Print Selesai ---</div>
        <div>{{ $test_time->format('Y-m-d H:i:s') }}</div>
    </div>
</div>

<div class="no-print" style="text-align: center; margin: 20px;">
    <button onclick="window.print()">Print Test</button>
    <button onclick="window.close()">Tutup</button>
</div>

<script>
    window.onload = function() {
        // Delay print untuk memastikan style sudah dimuat
        setTimeout(function() {
            window.print();
        }, 500);
    };
</script>
</body>
</html>
