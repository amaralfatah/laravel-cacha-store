<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="{{ asset('sneat/assets/img/favicon/favicon.ico') }}" />

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />

<!-- Icons. Uncomment required icon fonts -->
<link rel="stylesheet" href="{{ asset('sneat/assets/vendor/fonts/boxicons.css') }}" />

<!-- Core CSS -->
<link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
<link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/theme-default.css') }}"
    class="template-customizer-theme-css" />
<link rel="stylesheet" href="{{ asset('sneat/assets/css/demo.css') }}" />

<!-- Vendors CSS -->
{{-- <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" /> --}}

{{-- <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/apex-charts/apex-charts.css') }}" /> --}}

<!-- Page CSS -->

<!-- Helpers -->
<script src="{{ asset('sneat/assets/vendor/js/helpers.js') }}"></script>

<!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
<!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
<script src="{{ asset('sneat/assets/js/config.js') }}"></script>

@include('layouts.partials.vite')

<style>
    /* Old School Design - Optimized for older displays */
    body,
    html {
        height: 100%;
        overflow: hidden;
        color: #000000;
        font-family: Arial, Helvetica, sans-serif;
    }

    /* Layout containers */
    .layout-page {
        height: 100vh;
        overflow: hidden;
    }

    .content-wrapper {
        height: 100vh;
        overflow: hidden;
    }

    /* Main columns */
    .pos-main-column {
        height: 100vh;
        overflow-y: auto;
        background-color: #f0f0f0;
        border-right: 2px solid #000000;
    }

    .pos-bill-column {
        height: 100vh;
        overflow-y: auto;
        background-color: #f0f0f0;
    }

    /* Grid containers */
    .grid-container {
        display: grid;
        gap: 0.5rem;
        padding: 0.5rem;
    }

    .main-grid {
        grid-template-rows: auto auto 1fr;
    }

    /* Bill area */
    .bill-grid {
        display: flex;
        flex-direction: column;
        background-color: #f0f0f0;
        border-left: 3px solid #000000;
        padding: 0.75rem;
        height: 100%;
    }

    /* Sections with thick borders */
    .grid-section {
        border: 2px solid #000000;
        background: #ffffff;
        border-radius: 0;
        box-shadow: 3px 3px 0 #808080;
        margin-bottom: 0.75rem;
    }

    .grid-section-header {
        background-color: #d0d0d0;
        padding: 0.5rem;
        border-bottom: 2px solid #000000;
        font-weight: bold;
        color: #000080;
    }

    .grid-section-body {
        padding: 0.75rem;
    }

    /* Bill header */
    .bill-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 0.5rem;
        margin-bottom: 0.75rem;
        border-bottom: 2px solid #000000;
    }

    .bill-title {
        font-weight: bold;
        font-size: 1.1rem;
        color: #000080;
    }

    /* Summary section */
    .bill-summary {
        background-color: #e0e0e0;
        border: 2px solid #000000;
        border-radius: 0;
        padding: 0.5rem;
        margin-bottom: 0.75rem;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.25rem 0;
        border-bottom: 1px solid #000000;
    }

    .summary-row:last-child {
        border-bottom: none;
    }

    .summary-label {
        color: #000000;
        font-weight: normal;
    }

    .summary-value {
        font-weight: bold;
        text-align: right;
        min-width: 90px;
    }

    /* Total section */
    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem;
        margin: 0.5rem 0;
        border: 2px solid #000000;
        background-color: #d0d0d0;
        border-radius: 0;
        font-weight: bold;
    }

    /* Form elements */
    .form-group {
        margin-bottom: 0.75rem;
    }

    .form-label {
        margin-bottom: 0.25rem;
        font-weight: bold;
        color: #000000;
    }

    /* Input styling */
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
    }

    .total-input {
        color: #000080;
        font-size: 1.1rem;
        font-weight: bold;
    }

    .discount {
        color: #880000;
        font-weight: bold;
    }

    /* Change container */
    .change-container {
        background-color: #d0e0ff;
        border: 2px solid #000080;
        border-radius: 0;
        padding: 0.5rem;
    }

    /* Page header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 0.5rem;
        margin-bottom: 0.75rem;
        border-bottom: 3px solid #000000;
        background-color: #d0d0d0;
        padding: 0.5rem;
    }

    /* Action buttons */
    .action-buttons {
        margin-top: auto;
        padding-top: 0.75rem;
        border-top: 2px solid #000000;
    }

    /* Styling for buttons - old school look */
    .btn {
        border: 2px solid #000000;
        border-radius: 0;
        font-weight: bold;
        box-shadow: 3px 3px 0 #808080;
    }

    .btn:active {
        box-shadow: 1px 1px 0 #808080;
        transform: translate(2px, 2px);
    }

    .btn-primary {
        background-color: #000080;
        color: #ffffff;
    }

    .btn-warning {
        background-color: #ff8c00;
        color: #000000;
    }

    .btn-danger {
        background-color: #cc0000;
        color: #ffffff;
    }

    .btn-outline-secondary {
        background-color: #f0f0f0;
        color: #000000;
        border: 2px solid #000000;
    }

    /* Form controls in old style */
    .form-control,
    .form-select {
        border: 2px solid #000000;
        border-radius: 0;
        background-color: #ffffff;
        color: #000000;
        box-shadow: inset 1px 1px 2px #808080;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #000080;
        box-shadow: inset 1px 1px 2px #808080, 0 0 0 2px #d0e0ff;
    }

    .input-group-text {
        border: 2px solid #000000;
        border-right: none;
        background-color: #d0d0d0;
        color: #000000;
        border-radius: 0;
    }

    /* Table styling with borders */
    .table {
        margin-bottom: 0;
        border: 2px solid #000000;
        border-collapse: collapse;
    }

    .table thead {
        background-color: #d0d0d0;
    }

    .table th {
        border: 1px solid #000000;
        border-bottom: 2px solid #000000;
        font-weight: bold;
        color: #000000;
        background-color: #d0d0d0;
    }

    .table td {
        border: 1px solid #000000;
        background-color: #ffffff;
    }

    .table-light td {
        background-color: #f0f0f0;
    }

    .table-active td {
        background-color: #d0e0ff !important;
    }

    /* Empty cart styling */
    .empty-cart {
        text-align: center;
        padding: 2rem 1rem;
        border: 2px dashed #000000;
        margin: 0.75rem;
        background-color: #ffffff;
    }

    /* Badge styling */
    .badge {
        border: 1px solid #000000;
        border-radius: 0;
        font-weight: bold;
    }

    .badge-primary,
    .bg-primary {
        background-color: #000080 !important;
        color: #ffffff;
    }

    .bg-label-secondary {
        background-color: #d0d0d0 !important;
        color: #000000;
        border: 1px solid #000000;
    }

    /* Responsive adjustments */
    @media (max-width: 991.98px) {

        .pos-main-column,
        .pos-bill-column {
            height: auto;
            overflow-y: auto;
        }

        .bill-grid {
            height: auto;
            border-left: none;
            border-top: 3px solid #000000;
            margin-top: 0.75rem;
        }
    }
</style>
