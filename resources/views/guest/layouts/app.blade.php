<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Head Section -->
    @include('guest.layouts.partials.seo-head')

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    @include('layouts.partials.vite')

    @include('guest.layouts.partials.styles')

    @stack('styles')
</head>

<body>
    <!-- SEO Body Section -->
    @include('guest.layouts.partials.seo-body')

    <!-- Navigation -->
    @include('guest.layouts.partials.navbar')

    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    @include('guest.layouts.partials.footer')

    <!-- Scroll to Top -->
    {{-- <div class="scroll-to-top" id="scrollToTop">
        <i class="fas fa-arrow-up"></i>
    </div> --}}

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <!-- Main JS -->
    @include('guest.layouts.partials.scripts')

    @stack('scripts')
</body>

</html>
