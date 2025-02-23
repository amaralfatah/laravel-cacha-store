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
    /* Essential Transaction Ticker Styles */
    .transaction-ticker {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1030;
        background: linear-gradient(45deg, #696cff, #5f65f4);
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

    /* Responsive Animation */
    @media (max-width: 768px) {
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

    /* Minimal Layout Adjustments */
    .content-wrapper {
        padding-top: 45px;
    }
</style>




