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

@if(app()->environment('local'))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@else
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
@endif

<style>
    .transaction-ticker {
        background: #696cff;
        height: 35px;
        overflow: hidden;
        position: relative;
        color: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .ticker-content {
        display: flex;
        align-items: center;
        height: 100%;
        padding: 0 15px;
        font-size: 0.875rem;
    }

    .ticker-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 0 10px;
        white-space: nowrap;
    }

    .ticker-item i {
        font-size: 1rem;
    }

    .ticker-divider {
        width: 1px;
        height: 15px;
        background-color: rgba(255, 255, 255, 0.3);
        margin: 0 5px;
    }

    #today_total {
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .ticker-content {
            animation: ticker 20s linear infinite;
        }

        @keyframes ticker {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
    }
</style>


