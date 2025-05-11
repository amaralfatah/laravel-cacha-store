<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="{{ asset('sneat/assets/img/favicon/favicon.ico') }}" />

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Tahoma:wght@400;700&display=swap" rel="stylesheet" />

<!-- Icons -->
<link rel="stylesheet" href="{{ asset('sneat/assets/vendor/fonts/boxicons.css') }}" />

<!-- Core CSS -->
<link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
<link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/theme-default.css') }}"
      class="template-customizer-theme-css" />
<link rel="stylesheet" href="{{ asset('sneat/assets/css/demo.css') }}" />

<!-- Helpers -->
<script src="{{ asset('sneat/assets/vendor/js/helpers.js') }}"></script>
<script src="{{ asset('sneat/assets/js/config.js') }}"></script>

<!-- Vite (untuk development) -->
@include('layouts.partials.vite')

<!-- POS Specific CSS - Windows XP/7 Style -->
<style>
    /* Layout & Base Styles */
    body,
    html {
        height: 100%;
        overflow: hidden;
        color: #000000;
        font-family: Tahoma, Arial, sans-serif;
        background-color: #e7eef6;
    }

    .layout-page {
        height: 100vh;
        overflow: hidden;
    }

    .content-wrapper {
        height: 100vh;
        overflow: hidden;
    }

    /* Windows XP/7-style Main Columns */
    .pos-main-column {
        height: 100vh;
        overflow-y: auto;
        background-color: #ece9d8;
        border-right: 1px solid #a9a9a9;
    }

    .pos-bill-column {
        height: 100vh;
        overflow-y: auto;
        background-color: #ece9d8;
    }

    /* Grid Layout */
    .grid-container {
        display: grid;
        gap: 6px;
        padding: 8px;
    }

    .main-grid {
        grid-template-rows: auto auto 1fr;
    }

    /* Bill Column Layout */
    .bill-grid {
        display: flex;
        flex-direction: column;
        background-color: #ece9d8;
        border-left: 1px solid #a9a9a9;
        padding: 8px;
        height: 100%;
    }

    /* Section Containers - Windows Classic Style */
    .grid-section {
        border: 2px solid #919b9c;
        background: #fdfdfd;
        border-radius: 0;
        box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.2);
        margin-bottom: 8px;
    }

    .grid-section-header {
        background: linear-gradient(to bottom, #4f6acc 0%, #2a3c8e 100%);
        padding: 4px 8px;
        border-bottom: 1px solid #919b9c;
        font-weight: bold;
        color: #ffffff;
    }

    .grid-section-body {
        padding: 10px;
    }

    /* Bill Header */
    .bill-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 6px;
        margin-bottom: 8px;
        border-bottom: 1px solid #919b9c;
    }

    .bill-title {
        font-weight: bold;
        font-size: 1.1rem;
        color: #1b5697;
    }

    /* Bill Summary Section */
    .bill-summary {
        background-color: #f5f5f5;
        border: 1px solid #919b9c;
        border-radius: 0;
        padding: 6px;
        margin-bottom: 8px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 3px 0;
        border-bottom: 1px solid #d9d9d9;
    }

    .summary-row:last-child {
        border-bottom: none;
    }

    .summary-label {
        color: #222222;
        font-weight: normal;
    }

    .summary-value {
        font-weight: bold;
        text-align: right;
        min-width: 90px;
    }

    /* Total Row - Windows Style */
    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px;
        margin: 6px 0;
        border: 2px solid #919b9c;
        background: linear-gradient(to bottom, #e1e6f6 0%, #c9d4f6 100%);
        border-radius: 0;
        font-weight: bold;
    }

    /* Form Elements */
    .form-group {
        margin-bottom: 8px;
    }

    .form-label {
        margin-bottom: 3px;
        font-weight: bold;
        color: #222222;
    }

    /* Input Fields - Windows Classic Style */
    .value-input,
    .total-input,
    .change-input {
        border: none;
        background: transparent;
        text-align: right;
        padding: 0;
        margin: 0;
        width: 100%;
        font-weight: bold;
        color: #000000;
        font-family: Tahoma, Arial, sans-serif;
    }

    .total-input {
        color: #1b5697;
        font-size: 1.1rem;
        font-weight: bold;
    }

    .discount {
        color: #990000;
        font-weight: bold;
    }

    /* Change Container */
    .change-container {
        background-color: #e8f1ff;
        border: 1px solid #4f6acc;
        border-radius: 0;
        padding: 6px;
    }

    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 10px;
        margin-bottom: 8px;
        border-bottom: 1px solid #919b9c;
        background: linear-gradient(to bottom, #d4d4d4 0%, #b4b4b4 100%);
    }

    /* Action Buttons */
    .action-buttons {
        margin-top: auto;
        padding-top: 8px;
        border-top: 1px solid #919b9c;
    }

    /* Button Styling - Windows Classic Style */
    .btn {
        border: 2px solid #919b9c;
        border-radius: 0;
        font-weight: bold;
        box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        font-family: Tahoma, Arial, sans-serif;
        text-shadow: none;
    }

    .btn:active {
        box-shadow: inset 2px 2px 3px rgba(0, 0, 0, 0.2);
        position: relative;
        top: 1px;
        left: 1px;
    }

    .btn-primary {
        background: linear-gradient(to bottom, #4f6acc 0%, #2a3c8e 100%);
        color: #ffffff;
        border-color: #25378c;
    }

    .btn-warning {
        background: linear-gradient(to bottom, #ffb258 0%, #ff9621 100%);
        color: #000000;
        border-color: #e48922;
    }

    .btn-danger {
        background: linear-gradient(to bottom, #f46c6c 0%, #d93636 100%);
        color: #ffffff;
        border-color: #a52a2a;
    }

    .btn-outline-secondary {
        background: linear-gradient(to bottom, #f9f9f9 0%, #e0e0e0 100%);
        color: #000000;
        border-color: #919b9c;
    }

    /* Form Controls - Windows Classic Style */
    .form-control,
    .form-select {
        border: 2px solid #919b9c;
        border-radius: 0;
        background-color: #ffffff;
        color: #000000;
        box-shadow: inset 1px 1px 2px rgba(0, 0, 0, 0.2);
        font-family: Tahoma, Arial, sans-serif;
        padding: 4px 8px;
        height: 30px;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #4f6acc;
        box-shadow: inset 1px 1px 2px rgba(0, 0, 0, 0.2), 0 0 2px #b7c9ea;
    }

    .input-group-text {
        border: 2px solid #919b9c;
        border-right: none;
        background: linear-gradient(to bottom, #f9f9f9 0%, #e0e0e0 100%);
        color: #000000;
        border-radius: 0;
    }

    /* Table Styling - Windows Classic */
    .table {
        margin-bottom: 0;
        border: 2px solid #919b9c;
        border-collapse: collapse;
    }

    .table thead {
        background: linear-gradient(to bottom, #d1d7e6 0%, #b0b9d5 100%);
    }

    .table th {
        border: 1px solid #919b9c;
        border-bottom: 2px solid #919b9c;
        font-weight: bold;
        color: #000000;
        padding: 4px 6px;
    }

    .table td {
        border: 1px solid #919b9c;
        background-color: #ffffff;
        padding: 4px 6px;
    }

    .table-light td {
        background-color: #f5f5f5;
    }

    .table-active td {
        background-color: #cce8ff !important;
    }

    /* Empty Cart */
    .empty-cart {
        text-align: center;
        padding: 15px;
        border: 1px dashed #919b9c;
        margin: 8px;
        background-color: #fcfcfc;
    }

    /* Badges */
    .badge {
        border: 1px solid #000000;
        border-radius: 0;
        font-weight: bold;
        font-family: Tahoma, Arial, sans-serif;
        font-size: 11px;
        padding: 2px 6px;
    }

    .badge-primary,
    .bg-primary {
        background-color: #2a3c8e !important;
        color: #ffffff;
    }

    .bg-label-secondary {
        background-color: #d1d7e6 !important;
        color: #000000;
        border: 1px solid #919b9c;
    }

    /* Product Search Dropdown */
    .product-item {
        padding: 6px 8px;
        cursor: pointer;
        border-bottom: 1px solid #d9d9d9;
    }

    .product-item:hover {
        background-color: #e8f1ff;
    }

    .product-item.active {
        background-color: #cce8ff;
    }

    #pos_product_list {
        position: absolute;
        width: 100%;
        max-height: 300px;
        overflow-y: auto;
        background: white;
        border: 2px solid #919b9c;
        border-radius: 0;
        z-index: 1000;
        display: none;
        box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.2);
    }

    /* Select2 Override */
    .select2-container--default .select2-selection--single {
        border: 2px solid #919b9c !important;
        border-radius: 0 !important;
        height: 30px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
    }

    .select2-dropdown {
        border: 2px solid #919b9c !important;
        border-radius: 0 !important;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #4f6acc !important;
    }

    /* Scrollbars - Windows Classic */
    ::-webkit-scrollbar {
        width: 16px;
        height: 16px;
    }

    ::-webkit-scrollbar-track {
        background-color: #f0f0f0;
        border: 1px solid #d9d9d9;
    }

    ::-webkit-scrollbar-thumb {
        background: linear-gradient(to right, #d1d7e6 0%, #b0b9d5 100%);
        border: 1px solid #919b9c;
    }

    ::-webkit-scrollbar-button {
        background: linear-gradient(to bottom, #f9f9f9 0%, #e0e0e0 100%);
        border: 1px solid #919b9c;
        height: 16px;
        width: 16px;
    }

    /* Responsive Adjustments */
    @media (max-width: 991.98px) {
        .pos-main-column,
        .pos-bill-column {
            height: auto;
            overflow-y: auto;
        }

        .bill-grid {
            height: auto;
            border-left: none;
            border-top: 1px solid #919b9c;
            margin-top: 8px;
        }
    }
</style>


<!-- Tambahkan di resources/views/pos/partials/styles.blade.php -->
<style>
    /* Perbaikan untuk Form Controls - Windows 7 Style */
    .form-control,
    .form-select {
        border: 1px solid #7b9ebd !important;
        border-radius: 2px !important;
        background-color: #ffffff !important;
        color: #000000 !important;
        box-shadow: inset 1px 1px 3px rgba(0, 0, 0, 0.15) !important;
        font-family: Tahoma, Arial, sans-serif !important;
        height: 28px !important;
        padding: 3px 5px !important;
        font-size: 12px !important;
    }

    /* Select Dropdown - Windows 7 Style */
    .form-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3E%3Cpath fill='%23343a40' d='M4 7l4 4 4-4H4z'/%3E%3C/svg%3E") !important;
        background-position: right 5px center !important;
        background-size: 16px 12px !important;
        padding-right: 24px !important;
    }

    /* Dropdown Items */
    option {
        font-family: Tahoma, Arial, sans-serif !important;
        font-size: 12px !important;
    }

    /* Input Group Addons */
    .input-group-text {
        border: 1px solid #7b9ebd !important;
        background: linear-gradient(to bottom, #f3f6fb 0%, #e1e6f6 100%) !important;
        color: #000000 !important;
        font-size: 12px !important;
        height: 28px !important;
        padding: 3px 8px !important;
    }

    /* Fix for Search Icon in Dropdown */
    .select2-selection__arrow {
        background: linear-gradient(to bottom, #f3f6fb 0%, #e1e6f6 100%) !important;
        border-left: 1px solid #7b9ebd !important;
        width: 20px !important;
    }

    /* Fix untuk Button dengan Icon */
    .btn {
        font-size: 12px !important;
        padding: 3px 10px !important;
        height: 28px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border: 1px solid #7b9ebd !important;
        background: linear-gradient(to bottom, #f3f6fb 0%, #e1e6f6 100%) !important;
        box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2) !important;
    }

    .btn i, .btn .bx {
        font-size: 16px !important;
        margin-right: 5px !important;
    }

    .btn-primary {
        background: linear-gradient(to bottom, #7ba7e1 0%, #4a85d8 100%) !important;
        border-color: #3563a0 !important;
        color: white !important;
    }

    .btn-warning {
        background: linear-gradient(to bottom, #ffd373 0%, #febc4a 100%) !important;
        border-color: #d69e31 !important;
        color: black !important;
    }

    .btn-danger {
        background: linear-gradient(to bottom, #ec6d62 0%, #dc4734 100%) !important;
        border-color: #b33228 !important;
        color: white !important;
    }

    /* Header dan Footer */
    .page-header,
    .grid-section-header {
        background: linear-gradient(to bottom, #e5eefd 0%, #c4d5f3 100%) !important;
        border: 1px solid #7b9ebd !important;
        padding: 5px 8px !important;
        font-family: Tahoma, Arial, sans-serif !important;
        font-size: 12px !important;
        font-weight: bold !important;
        color: #15428b !important;
    }

    /* Tab Focus Styling */
    .form-control:focus,
    .form-select:focus {
        border-color: #4b92f7 !important;
        box-shadow: 0 0 3px #a6c8ff !important;
        outline: none !important;
    }

    /* Badge styling untuk tombol shortcut F2, F3, F8 */
    .badge.bg-label-secondary {
        background-color: #f0f0f0 !important;
        color: #000000 !important;
        border: 1px solid #7b9ebd !important;
        font-size: 10px !important;
        padding: 1px 4px !important;
        font-family: Tahoma, Arial, sans-serif !important;
        border-radius: 2px !important;
    }

    /* Styling untuk empty cart message */
    .empty-cart {
        border: 1px dashed #7b9ebd !important;
        background-color: #f5f9fe !important;
        padding: 20px 15px !important;
        text-align: center !important;
        margin: 10px !important;
    }

    .empty-cart .bx {
        color: #7ba7e1 !important;
        font-size: 32px !important;
        margin-bottom: 10px !important;
    }

    .empty-cart h5 {
        font-family: Tahoma, Arial, sans-serif !important;
        font-size: 14px !important;
        color: #15428b !important;
    }

    .empty-cart p {
        font-family: Tahoma, Arial, sans-serif !important;
        font-size: 12px !important;
        color: #333333 !important;
    }

    /* Tabel dengan style Windows Explorer */
    .table {
        border: 1px solid #7b9ebd !important;
    }

    .table th {
        background: linear-gradient(to bottom, #e5eefd 0%, #c4d5f3 100%) !important;
        font-size: 12px !important;
        color: #15428b !important;
        padding: 4px 6px !important;
        border: 1px solid #7b9ebd !important;
    }

    .table td {
        font-size: 12px !important;
        padding: 3px 6px !important;
        border: 1px solid #d8d8d8 !important;
    }

    /* Window Title - Main Header */
    .bill-title, h4.fw-bold {
        font-family: Tahoma, Arial, sans-serif !important;
        font-size: 13px !important;
        color: #15428b !important;
        font-weight: bold !important;
    }

    /* Bill Grid Panel */
    .bill-grid {
        border: 1px solid #7b9ebd !important;
        background-color: #f5f9fe !important;
    }

    /* Layout Adjustments */
    .pos-main-column {
        background-color: #f0f0f0 !important;
        border-right: 1px solid #7b9ebd !important;
    }

    /* Select2 Overrides */
    .select2-container--default .select2-selection--single {
        border: 1px solid #7b9ebd !important;
        height: 28px !important;
        border-radius: 2px !important;
        background: white !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px !important;
        font-family: Tahoma, Arial, sans-serif !important;
        font-size: 12px !important;
        padding-left: 8px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 26px !important;
    }

    .select2-dropdown {
        border: 1px solid #7b9ebd !important;
        border-radius: 0 !important;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2) !important;
    }

    /* Scanning area - focused field style */
    #pos_barcode {
        background-color: #fffce8 !important; /* Light yellow background */
        font-family: 'Courier New', monospace !important; /* Monospace font for barcode */
        font-weight: bold !important;
    }

    #pos_barcode:focus {
        background-color: #fff8d8 !important;
        border-color: #4b92f7 !important;
        box-shadow: 0 0 5px #a6c8ff !important;
    }
</style>


<style>
    /* Fix khusus untuk Select2 agar simetris dengan style Windows era 2009-2014 */
    .select2-container--default .select2-selection--single {
        height: 28px !important;
        border: 1px solid #7b9ebd !important;
        border-radius: 2px !important;
        box-shadow: inset 1px 1px 2px rgba(0, 0, 0, 0.1) !important;
        background-color: #ffffff !important;
        display: flex !important;
        align-items: center !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #333333 !important;
        font-family: Tahoma, Arial, sans-serif !important;
        font-size: 12px !important;
        line-height: 1 !important;
        padding-left: 8px !important;
        padding-right: 24px !important;
        display: flex !important;
        align-items: center !important;
        height: 100% !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 26px !important;
        width: 22px !important;
        position: absolute !important;
        right: 1px !important;
        top: 1px !important;
        border-left: 1px solid #b8c8da !important;
        background: linear-gradient(to bottom, #f3f6fb 0%, #e1e6f6 100%) !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #666 transparent transparent transparent !important;
        border-width: 5px 4px 0 4px !important;
        margin-left: -4px !important;
        margin-top: -2px !important;
    }

    /* Dropdown Panel Style */
    .select2-container--default .select2-dropdown {
        border: 1px solid #7b9ebd !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
        margin-top: 1px !important;
        font-family: Tahoma, Arial, sans-serif !important;
        font-size: 12px !important;
    }

    .select2-container--default .select2-search--dropdown {
        padding: 4px !important;
        background-color: #f5f9ff !important;
        border-bottom: 1px solid #d9e5f2 !important;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #7b9ebd !important;
        border-radius: 2px !important;
        padding: 3px 5px !important;
        box-shadow: inset 1px 1px 2px rgba(0, 0, 0, 0.1) !important;
        font-family: Tahoma, Arial, sans-serif !important;
        font-size: 12px !important;
        height: 24px !important;
    }

    .select2-container--default .select2-results__option {
        padding: 4px 8px !important;
        font-size: 12px !important;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3399ff !important;
        color: white !important;
    }

    /* Fix untuk placeholder agar tampil simetris */
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #999 !important;
    }

    /* Fix tampilan waktu disabled */
    .select2-container--default.select2-container--disabled .select2-selection--single {
        background-color: #f2f2f2 !important;
        cursor: default !important;
    }

</style>
