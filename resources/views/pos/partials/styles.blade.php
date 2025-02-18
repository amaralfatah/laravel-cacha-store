<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="{{ asset('sneat/assets/img/favicon/favicon.ico') }}" />

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet"
/>

<!-- Icons. Uncomment required icon fonts -->
<link rel="stylesheet" href="{{ asset('sneat/assets/vendor/fonts/boxicons.css') }}" />

<!-- Core CSS -->
<link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
<link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
<link rel="stylesheet" href="{{ asset('sneat/assets/css/demo.css') }}" />

<!-- Vendors CSS -->
<link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

<link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/apex-charts/apex-charts.css') }}" />

<!-- Page CSS -->

<!-- Helpers -->
<script src="{{ asset('sneat/assets/vendor/js/helpers.js') }}"></script>

<!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
<!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
<script src="{{ asset('sneat/assets/js/config.js') }}"></script>

@vite(['resources/css/app.css', 'resources/js/app.js'])


<style>
    .pos-container {
        background: #f5f5f9;
        min-height: 100vh;
    }
    .product-search-container {
        position: relative;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    .cart-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    .payment-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        padding: 1.5rem;
        position: sticky;
        top: 1rem;
    }
    .action-buttons {
        gap: 0.5rem;
    }
    .cart-table {
        margin-top: 1rem;
    }
    .cart-table th {
        background: #f8f9fa;
        padding: 0.75rem;
    }
    .cart-table td {
        vertical-align: middle;
        padding: 0.75rem;
    }
    .form-control, .form-select {
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
    }
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #566a7f;
    }
    .btn {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 500;
    }
    .btn-icon {
        padding: 0.5rem;
        line-height: 1;
    }
    #pos_product_list {
        border-radius: 6px;
        padding: 0.5rem 0;
    }
    .product-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #eee;
        transition: background-color 0.2s;
    }
    .product-item:hover, .product-item.active {
        background-color: #f8f9fa;
    }
    .amount-field {
        font-size: 1.25rem;
        font-weight: 600;
        text-align: right;
        background-color: #f8f9fa;
    }
    #pos_final_amount {
        font-size: 1.5rem;
        color: #696cff;
        background-color: #eef1ff;
    }
</style>
