<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bijiko Dua - Kasir Kedai Kopi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
        }

        .container {
            width: 100%;
            height: 100%;
            background-color: white;
            display: flex;
            overflow: hidden;
        }

        /* Content Area */
        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: #ffffff;
            height: 100vh;
        }

        /* Main Content */
        .main-content {
            display: flex;
            flex: 1;
            overflow: hidden;
            height: 100%;
            background-color: #ffffff;
            position: relative;
        }

        /* Vertical divider */
        .main-content:after {
            content: "";
            position: absolute;
            right: 350px;
            top: 0;
            bottom: 0;
            width: 1px;
            background-color: #e0e0e0;
        }

        /* Center Content - Redesigned */
        .center-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 30px;
            background-color: #f8f9fb;
            margin: 0;
            overflow-y: auto;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 15px;
        }

        .page-title:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(135deg, #00B886, #00965D);
            border-radius: 2px;
        }

        /* Scan Search Section - Redesigned */
        .scan-search-section {
            margin-bottom: 30px;
            padding: 0 0 25px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .scan-search-section h2 {
            font-size: 18px;
            font-weight: 600;
            color: #444;
            margin-bottom: 15px;
        }

        .scan-search-container {
            display: flex;
        }



        .manual-search {
            flex: 2;
            display: flex;
            gap: 10px;
        }

        .manual-search input {
            flex: 1;
            padding: 14px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            outline: none;
            font-size: 15px;
            transition: all 0.3s;
        }

        .manual-search input:focus {
            border-color: #00B886;
            box-shadow: 0 0 0 3px rgba(0, 184, 134, 0.1);
        }

        .search-btn {
            background-color: #00B886;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 0 20px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }

        .search-btn:hover {
            background-color: #00965D;
        }

        /* Selected Products - Redesigned */
        .selected-products {
            background-color: #ffffff;
            padding: 25px 0;
        }

        .selected-products h2 {
            font-size: 18px;
            font-weight: 600;
            color: #444;
            margin-bottom: 20px;
        }

        .product-list-header {
            display: grid;
            grid-template-columns: 3fr 2fr 1fr 1fr 1fr 0.5fr;
            gap: 10px;
            padding: 15px;
            background-color: #f8f9fb;
            border-radius: 10px;
            margin-bottom: 15px;
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }

        .product-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .product-item {
            display: grid;
            grid-template-columns: 3fr 2fr 1fr 1fr 1fr 0.5fr;
            gap: 10px;
            padding: 15px;
            border-radius: 10px;
            background-color: #fff;
            border: 1px solid #eff1f5;
            transition: all 0.3s;
            align-items: center;
            margin-bottom: 8px;
        }

        .product-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }

        .product-detail {
            display: flex;
            align-items: center;
        }

        .product-name {
            font-weight: 500;
            color: #333;
        }

        .product-options {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .option-tag {
            background-color: #f0f2f5;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 12px;
            color: #666;
        }

        .product-price, .product-total {
            font-weight: 500;
            color: #00B886;
        }

        .product-qty {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .qty-btn {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: none;
            background-color: #f0f2f5;
            color: #666;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .qty-btn:hover {
            background-color: #e0e0e0;
        }

        .qty-btn.plus:hover {
            background-color: #00B886;
            color: white;
        }

        .qty-btn.minus:hover {
            background-color: #ff6b6b;
            color: white;
        }

        .delete-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: #ff6b6b;
            transition: all 0.2s;
        }

        .delete-btn:hover {
            transform: scale(1.2);
        }

        /* Bill Details - Redesigned */
        .bill-details {
            width: 350px;
            background-color: white;
            padding: 25px 30px;
            display: flex;
            flex-direction: column;
            z-index: 5;
            margin: 0;
        }

        .bill-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #f0f2f5;
            padding-bottom: 15px;
        }

        .bill-title {
            font-size: 22px;
            font-weight: bold;
            color: #333;
        }

        .bill-id {
            font-size: 14px;
            color: #666;
            background-color: #f7f8fa;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
        }

        .customer-input {
            margin-bottom: 25px;
        }

        .customer-input h3 {
            font-size: 15px;
            margin-bottom: 10px;
            color: #444;
            font-weight: 600;
        }

        .customer-input input {
            width: 100%;
            padding: 14px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            outline: none;
            font-size: 15px;
            transition: all 0.3s;
        }

        .customer-input input:focus {
            border-color: #00B886;
            box-shadow: 0 0 0 3px rgba(0, 184, 134, 0.1);
        }

        .bill-item {
            margin-bottom: 20px;
            background-color: #f8f9fb;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #eff1f5;
            transition: all 0.3s;
        }

        .bill-item:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .bill-item-title {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .bill-item-name {
            font-weight: 600;
            color: #333;
        }

        .bill-item-price {
            font-weight: 600;
            color: #00B886;
        }

        .bill-item-details {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #666;
            margin: 8px 0;
        }

        .bill-summary {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px dashed #e0e0e0;
        }

        .bill-summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 15px;
            color: #555;
        }

        .bill-total {
            display: flex;
            justify-content: space-between;
            margin: 25px 0;
            font-weight: bold;
            font-size: 24px;
            color: #333;
            padding: 20px 0;
            border-top: 1px solid #e0e0e0;
            border-bottom: 1px solid #e0e0e0;
        }

        .bill-total-value {
            color: #00B886;
        }

        .select-options {
            margin-bottom: 20px;
        }

        .select-options h3 {
            font-size: 15px;
            margin-bottom: 10px;
            color: #444;
            font-weight: 600;
        }

        .select-input {
            width: 100%;
            padding: 16px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            outline: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
            background-color: white;
            font-size: 15px;
        }

        .select-input:hover {
            border-color: #ccc;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
        }



        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .pending-btn {
            flex: 1;
            background-color: #f8f9fb;
            color: #555;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .pending-btn:hover {
            background-color: #e8e8e8;
        }

        .process-btn {
            flex: 1.5;
            background: linear-gradient(135deg, #00B886, #00965D);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(0, 184, 134, 0.2);
        }

        .process-btn:hover {
            background: linear-gradient(135deg, #00AA7B, #008550);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 184, 134, 0.3);
        }

        .process-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 184, 134, 0.3);
        }

        .discount {
            color: #ff6b6b;
        }

        /* Add a row total section */
        .row-total {
            display: flex;
            justify-content: flex-end;
            margin-top: 30px;
            gap: 15px;
            padding: 20px;
            background-color: #f8f9fb;
            border-radius: 12px;
            align-items: center;
        }

        .row-total-text {
            font-weight: 600;
            font-size: 18px;
            color: #333;
        }

        .row-total-value {
            font-weight: 700;
            font-size: 24px;
            color: #00B886;
        }

        .checkout-btn {
            background: linear-gradient(135deg, #00B886, #00965D);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0, 184, 134, 0.2);
            margin-left: 20px;
        }

        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 184, 134, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Content Area - No Sidebar or Topbar -->
        <div class="content">
            <!-- Main Content -->
            <div class="main-content">
                <!-- Center Content - Product List -->
                <div class="center-content">
                    <h1 class="page-title">Bijiko Dua - Kasir Kedai Kopi</h1>

                    <!-- Manual Search Section -->
                    <div class="scan-search-section">
                        <h2>Entri Produk</h2>
                        <div class="scan-search-container">
                            <div class="manual-search" style="flex: 1;">
                                <input type="text" placeholder="Cari produk secara manual...">
                                <button class="search-btn">Cari</button>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Products List -->
                    <div class="selected-products">
                        <h2>Produk Terpilih</h2>
                        <div class="product-list-header">
                            <div class="product-header-item product-name">Nama Produk</div>
                            <div class="product-header-item product-options">Opsi</div>
                            <div class="product-header-item product-price">Harga</div>
                            <div class="product-header-item product-qty">Jml</div>
                            <div class="product-header-item product-total">Total</div>
                            <div class="product-header-item product-actions">Aksi</div>
                        </div>

                        <!-- Product Items -->
                        <div class="product-list">
                            <div class="product-item">
                                <div class="product-detail product-name">Frappuccino Karamel Java</div>
                                <div class="product-detail product-options">
                                    <span class="option-tag">L</span>
                                    <span class="option-tag">Es 100%</span>
                                    <span class="option-tag">Gula 60%</span>
                                </div>
                                <div class="product-detail product-price">Rp35.000</div>
                                <div class="product-detail product-qty">
                                    <button class="qty-btn minus">-</button>
                                    <span class="qty-value">2</span>
                                    <button class="qty-btn plus">+</button>
                                </div>
                                <div class="product-detail product-total">Rp70.000</div>
                                <div class="product-detail product-actions">
                                    <button class="delete-btn">🗑️</button>
                                </div>
                            </div>

                            <div class="product-item">
                                <div class="product-detail product-name">Frappuccino Jeli Kopi</div>
                                <div class="product-detail product-options">
                                    <span class="option-tag">M</span>
                                    <span class="option-tag">Es 60%</span>
                                    <span class="option-tag">Gula 30%</span>
                                </div>
                                <div class="product-detail product-price">Rp25.250</div>
                                <div class="product-detail product-qty">
                                    <button class="qty-btn minus">-</button>
                                    <span class="qty-value">1</span>
                                    <button class="qty-btn plus">+</button>
                                </div>
                                <div class="product-detail product-total">Rp25.250</div>
                                <div class="product-detail product-actions">
                                    <button class="delete-btn">🗑️</button>
                                </div>
                            </div>
                        </div>

                        <!-- Row Total -->
                        <!-- Remove Total Amount section -->
                    </div>
                </div>

                <!-- Bill Details -->
                <div class="bill-details">
                    <div class="bill-header">
                        <div class="bill-title">Detail Tagihan</div>
                        <div class="bill-id">#546234</div>
                    </div>

                    <div class="customer-input">
                        <h3>Nama Pelanggan</h3>
                        <input type="text" placeholder="Nama Pelanggan">
                    </div>



                    <div class="bill-summary">
                        <div class="bill-summary-item">
                            <span>Items</span>
                            <span>3 (Items)</span>
                        </div>

                        <div class="bill-summary-item">
                            <span>Subtotal</span>
                            <span>$95.25</span>
                        </div>

                        <div class="bill-summary-item">
                            <span>Discount</span>
                            <span class="discount">- $5.50</span>
                        </div>

                        <div class="bill-summary-item">
                            <span>Tax (10%)</span>
                            <span>$8.98</span>
                        </div>
                    </div>

                    <div class="bill-total">
                        <span>Total</span>
                        <span class="bill-total-value">$98.73</span>
                    </div>

                    <div class="select-options">
                        <h3>Pilih Pembayaran</h3>
                        <div class="select-input">
                            <span>Pilih Metode Pembayaran</span>
                            <span>❯</span>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <button class="pending-btn">Tunda Transaksi</button>
                        <button class="process-btn">Proses Transaksi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
