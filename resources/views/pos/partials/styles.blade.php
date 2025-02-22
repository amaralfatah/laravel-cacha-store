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

@include('layouts.partials.vite')


<style>
    /* Color Variables */
    :root {
        --primary: #696cff;
        --primary-dark: #5f65f4;
        --secondary: #8592a3;
        --success: #71dd37;
        --info: #03c3ec;
        --warning: #ffab00;
        --danger: #ff3e1d;
        --dark: #233446;
        --gray: #697a8d;
        --gray-light: #a1acb8;
        --border-color: #d9dee3;
        --bg-light: #f5f5f9;
    }

    /* Base Layout */
    body {
        background: var(--bg-light);
    }

    .pos-container {
        padding: 0.5rem;
    }

    .content-wrapper {
        padding-top: 45px;
    }

    .container-fluid {
        padding-top: 1rem;
    }

    /* Sticky Ticker */
    .transaction-ticker {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1030;
        background: linear-gradient(45deg, var(--primary), var(--primary-dark));
        height: 45px;
        box-shadow: 0 2px 8px rgba(105, 108, 255, 0.15);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .ticker-content {
        height: 100%;
        padding: 0 1rem;
        font-size: 0.9rem;
        letter-spacing: 0.2px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .ticker-item {
        padding: 0 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
        color: white;
        font-weight: 500;
    }

    .ticker-item i {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .ticker-divider {
        height: 20px;
        width: 1px;
        background: rgba(255, 255, 255, 0.2);
        margin: 0 0.5rem;
    }



    /* Form Elements */
    .form-control, .form-select {
        border-radius: 6px;
        border: 1.5px solid var(--border-color);
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0.25rem rgba(105, 108, 255, 0.1);
    }

    .form-label {
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--gray);
        margin-bottom: 0.5rem;
    }

    .form-control-lg, .form-select-lg {
        min-height: 48px;
    }

    .input-group-text {
        min-width: 50px;
        justify-content: center;
        background-color: transparent;
        border-color: var(--border-color);
        color: var(--gray);
    }

    /* Table Styling */
    .cart-table {
        margin-top: 0.5rem;
    }

    .table thead th {
        background: var(--bg-light);
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--gray);
        padding: 0.75rem 1rem;
        border-bottom: 2px solid #f0f2f4;
    }

    .table tbody td {
        padding: 1rem 1.25rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color);
        color: var(--dark);
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    /* Payment Section */
    .payment-section {
        background: #fff;
        border-radius: 8px;
        padding: 1.5rem;
    }

    .total-amount {
        color: var(--primary);
        font-size: 2rem;
        font-weight: 600;
    }

    .amount-detail {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        color: var(--gray);
    }

    #pos_final_amount {
        color: var(--primary);
        font-size: 1.75rem;
        font-weight: 700;
    }

    #pos_change {
        color: var(--success);
        font-weight: 600;
    }

    /* Form Control Plaintext */
    .form-control-plaintext {
        font-family: 'Public Sans', sans-serif;
        padding: 0;
    }

    .form-control-plaintext:read-only {
        font-weight: 500;
    }

    /* Payment Total Highlight */
    .card-body.bg-light.border-bottom {
        background: var(--primary) !important;
        padding: 1.5rem;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }

    .text-muted.small.mb-1 {
        color: rgba(255, 255, 255, 0.8) !important;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    #pos_final_amount {
        color: #fff !important;
        font-size: 2.25rem;
        font-weight: 700;
        margin: 0;
        padding: 0;
        line-height: 1.2;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Enhanced Amount Details */
    .amount-detail {
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .amount-detail:last-child {
        border-bottom: none;
    }

    .amount-detail span:first-child {
        color: var(--gray);
        font-weight: 500;
    }

    .amount-detail input.form-control-plaintext {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark);
    }


    /* Responsive */
    @media (max-width: 768px) {
        .pos-container {
            padding: 0;
        }


        .container-fluid {
            padding: 1rem;
        }

        .modal-dialog {
            margin: 0.5rem;
        }

        .ticker-item.d-none {
            display: none !important;
        }

        .ticker-content {
            animation: ticker 20s linear infinite;
            justify-content: flex-start;
            width: max-content;
        }

        @keyframes ticker {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
    }
</style>




