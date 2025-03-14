@php
    use Artesaos\SEOTools\Facades\SEOTools;
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Generate all SEO tags -->
    {!! SEOTools::generate() !!}

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-snack-circle.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-snack-circle.png') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    @include('layouts.partials.vite')

    @include('guest.layouts.partials.styles')

    <!-- Preload important assets -->
    <link rel="preload" href="{{ asset('images/logo-snack-circle.png') }}" as="image">

    <!-- Structured data for organization -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "Cacha Store",
            "url": "{{ route('guest.home') }}",
            "logo": "{{ asset('images/logo-snack-circle.png') }}",
            "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "+6281234567890",
                "contactType": "customer service",
                "areaServed": "ID",
                "availableLanguage": "Indonesian"
            },
            "sameAs": [
                "https://facebook.com/cachastore",
                "https://instagram.com/cachastore",
                "https://twitter.com/cachastore"
            ]
        }
    </script>

    @stack('styles')

</head>

<body>
    <!-- Navigation -->
    @include('guest.layouts.partials.navbar')

    <main>
        @yield('content')
    </main>

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

    <!-- Structured data for breadcrumbs if available -->
    @if (isset($breadcrumbs))
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                @foreach($breadcrumbs as $index => $breadcrumb)
                {
                    "@type": "ListItem",
                    "position": {{ $index + 1 }},
                    "name": "{{ $breadcrumb['name'] }}",
                    "item": "{{ $breadcrumb['url'] }}"
                }@if(!$loop->last),@endif
                @endforeach
            ]
        }
    </script>
    @endif

</body>

</html>
