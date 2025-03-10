<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cacha Snack - Cemilan Kekinian Asli Pangandaran</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-snack-circle.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/cigo-snack-circle.png') }}">

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
<!-- Navigation -->
@include('guest.layouts.partials.navbar')

@yield('content')

<!-- Footer -->
@include('guest.layouts.partials.footer')

<!-- Scroll to Top -->
<div class="scroll-to-top" id="scrollToTop">
    <i class="fas fa-arrow-up"></i>
</div>

<!-- Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<!-- Main JS -->

@include('guest.layouts.partials.scripts')

@stack('scripts')

</body>
</html>
