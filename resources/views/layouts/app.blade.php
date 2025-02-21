<!DOCTYPE html>

<!-- =========================================================
* Sneat - Bootstrap 5 HTML Admin Template - Pro | v1.0.0
==============================================================

* Product Page: https://themeselection.com/products/sneat-bootstrap-html-admin-template/
* Created by: ThemeSelection
* License: You must have a valid license purchased in order to legally use the theme for your project.
* Copyright ThemeSelection (https://themeselection.com)

=========================================================
 -->
<!-- beautify ignore:start -->
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('sneat/assets/') }}"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>
        @php
            $segments = request()->segments();
            $title = !empty($segments) ? ucwords(str_replace(['-', '_'], ' ', end($segments))) : '';
        @endphp
        {{ $title ? $title . ' | ' : '' }}Toko Cacha
    </title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-cacha.png') }}" />

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
      @elseif(file_exists(public_path('build/manifest.json')))
          @php
              $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
          @endphp
          @foreach(['resources/css/app.css', 'resources/js/app.js'] as $key)
              @if(isset($manifest[$key]))
                  @if(Str::endsWith($key, '.css'))
                      <link rel="stylesheet" href="{{ asset('build/'.$manifest[$key]['file']) }}">
                  @elseif(Str::endsWith($key, '.js'))
                      <script src="{{ asset('build/'.$manifest[$key]['file']) }}" defer></script>
                  @endif
              @endif
          @endforeach
      @else
          <link rel="stylesheet" href="{{ asset('css/app.css') }}">
          <script src="{{ asset('js/app.js') }}" defer></script>
      @endif


      <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
{{--      <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">--}}

    @stack('styles')

    <style>
      .hover-bg-light:hover {
          background-color: rgba(0,0,0,.05);
      }

      .search-results {
          box-shadow: 0 0.25rem 1rem rgba(161, 172, 184, 0.45);
      }
      </style>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
        @include('layouts.partials.sidebar')
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->

            @include('layouts.partials.navbar')

          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
{{--                <x-breadcrumb />--}}
                @yield('content')
            </div>
            <!-- / Content -->

            <!-- Footer -->
            @include('layouts.partials.footer')
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    {{-- <div class="buy-now">
      <a
        href="https://themeselection.com/products/sneat-bootstrap-html-admin-template/"
        target="_blank"
        class="btn btn-danger btn-buy-now"
        >Lauch POS</a
      >
    </div> --}}

    @include('components.toast')

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('sneat/assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('sneat/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('sneat/assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('sneat/assets/js/dashboards-analytics.js') }}"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    @stack('scripts')

{{--    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>--}}
{{--    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>--}}
{{--    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>--}}
{{--    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
{{--    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>--}}
{{--    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>--}}

    <script>
        $(document).ready(function() {
            let searchTimeout;
            const searchInput = $('.search-input');
            const searchResults = $('.search-results');

            searchInput.on('keyup', function() {
                clearTimeout(searchTimeout);
                const query = $(this).val();

                if (query.length < 2) {
                    searchResults.hide();
                    return;
                }

                // Loading indicator
                searchResults.html(
                    '<div class="text-center py-2"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></div>'
                    ).show();

                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: '{{ route('search') }}',
                        data: {
                            q: query
                        },
                        success: function(response) {
                            let html = '';

                            // Products
                            if (response.products.length > 0) {
                                html += '<div class="mb-2">';
                                html +=
                                    '<small class="text-muted px-2">Products</small>';
                                response.products.forEach(function(item) {
                                    html += `
                                      <a href="${item.url}" class="d-flex align-items-center px-2 py-1 text-dark text-decoration-none hover-bg-light">
                                          <i class='bx bx-package me-2'></i>
                                          <div>
                                              <div>${item.title}</div>
                                              <small class="text-muted">${item.subtitle}</small>
                                          </div>
                                      </a>
                                  `;
                                });
                                html += '</div>';
                            }

                            // Customers
                            if (response.customers.length > 0) {
                                html += '<div class="mb-2">';
                                html +=
                                    '<small class="text-muted px-2">Customers</small>';
                                response.customers.forEach(function(item) {
                                    html += `
                                      <a href="${item.url}" class="d-flex align-items-center px-2 py-1 text-dark text-decoration-none hover-bg-light">
                                          <i class='bx bx-user me-2'></i>
                                          <div>
                                              <div>${item.title}</div>
                                              <small class="text-muted">${item.subtitle}</small>
                                          </div>
                                      </a>
                                  `;
                                });
                                html += '</div>';
                            }

                            // Suppliers
                            if (response.suppliers.length > 0) {
                                html += '<div class="mb-2">';
                                html +=
                                    '<small class="text-muted px-2">Suppliers</small>';
                                response.suppliers.forEach(function(item) {
                                    html += `
                                      <a href="${item.url}" class="d-flex align-items-center px-2 py-1 text-dark text-decoration-none hover-bg-light">
                                          <i class='bx bx-store me-2'></i>
                                          <div>
                                              <div>${item.title}</div>
                                              <small class="text-muted">${item.subtitle}</small>
                                          </div>
                                      </a>
                                  `;
                                });
                                html += '</div>';
                            }

                            // Transactions
                            if (response.transactions.length > 0) {
                                html += '<div class="mb-2">';
                                html +=
                                    '<small class="text-muted px-2">Transactions</small>';
                                response.transactions.forEach(function(item) {
                                    html += `
                                      <a href="${item.url}" class="d-flex align-items-center px-2 py-1 text-dark text-decoration-none hover-bg-light">
                                          <i class='bx bx-receipt me-2'></i>
                                          <div>
                                              <div>${item.title}</div>
                                              <small class="text-muted">${item.subtitle}</small>
                                          </div>
                                      </a>
                                  `;
                                });
                                html += '</div>';
                            }

                            if (html === '') {
                                html =
                                    '<div class="text-center py-2 text-muted">No results found</div>';
                            }

                            searchResults.html(html).show();
                        },
                        error: function() {
                            searchResults.html(
                                '<div class="text-center py-2 text-danger">Error occurred</div>'
                                ).show();
                        }
                    });
                }, 300);
            });

            // Close search results when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.nav-item').length) {
                    searchResults.hide();
                }
            });

            // Prevent form submission on enter
            searchInput.on('keypress', function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                }
            });
        });
    </script>


  </body>
</html>
