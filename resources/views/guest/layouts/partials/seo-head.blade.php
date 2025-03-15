@php
    use Artesaos\SEOTools\Facades\SEOTools;
@endphp

<!-- Generate all SEO tags -->
{!! SEOTools::generate() !!}

<!-- Favicon -->
<link rel="icon" type="image/png" href="{{ asset('images/logo-snack-circle.png') }}" sizes="32x32">
<link rel="icon" type="image/png" href="{{ asset('images/logo-snack-circle-192.png') }}" sizes="192x192">
<link rel="apple-touch-icon" href="{{ asset('images/logo-snack-circle.png') }}">

<!-- DNS Prefetch -->
<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="dns-prefetch" href="//fonts.gstatic.com">

<!-- Preconnect -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>

<!-- Preload Critical Assets -->
<link rel="preload" href="{{ asset('images/logo-snack-circle.png') }}" as="image">
<link rel="preload" href="{{ asset('css/app.css') }}" as="style">
<link rel="preload" href="{{ asset('js/app.js') }}" as="script">

<!-- Cache Control -->
<meta http-equiv="Cache-Control" content="public, max-age=31536000">

<!-- Preload important assets -->
<link rel="preload" href="{{ asset('images/logo-snack-circle.png') }}" as="image">

<!-- Structured data for organization -->
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": ["Organization", "LocalBusiness"],
        "name": "Cacha Store",
        "url": "{{ route('guest.home') }}",
        "logo": "{{ asset('images/logo-snack-circle.png') }}",
        "image": "{{ asset('images/logo-snack-circle.png') }}",
        "description": "Toko jajanan dan snack kekinian khas Pangandaran dengan berbagai varian rasa yang lezat",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Jl. Karapyak No.21, RT.21/RW.1, Emplak",
            "addressLocality": "Kalipucang",
            "addressRegion": "Jawa Barat",
            "postalCode": "46397",
            "addressCountry": "ID"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": "-7.6611617",
            "longitude": "108.7310816"
        },
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
        ],
        "openingHoursSpecification": [
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
                "opens": "08:00",
                "closes": "17:00"
            }
        ],
        "priceRange": "Rp5.000 - Rp100.000",
        "hasMap": "https://www.google.com/maps/place/Toko+Cacha/@-7.6611617,108.7310816,17z"
    }
</script>

<!-- Google Tag Manager -->
<script>
    (function(w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({
            'gtm.start': new Date().getTime(),
            event: 'gtm.js'
        });
        var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s),
            dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src =
            'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-KFJ3SVQX');
</script>
<!-- End Google Tag Manager -->

<!-- Google Analytics -->
{{-- <script async src="https://www.googletagmanager.com/gtag/js?id=G-6XVCHNSS0E"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'G-6XVCHNSS0E');

    // Track Core Web Vitals
    gtag('set', {
        'content_group': '{{ Request::path() }}',
        'allow_google_signals': false,
        'allow_ad_personalization_signals': false
    });

    // Custom dimension for page type
    gtag('set', 'page_type', '{{ Request::segment(1) ?: 'home' }}');
</script> --}}

<!-- SEO Performance Monitoring -->
<script>
    // Performance metrics
    if (window.performance) {
        // Navigation Timing API
        window.addEventListener('load', function() {
            setTimeout(function() {
                let timing = window.performance.timing;
                let pageLoadTime = timing.loadEventEnd - timing.navigationStart;

                // Send to Analytics
                gtag('event', 'timing_complete', {
                    'name': 'page_load',
                    'value': pageLoadTime,
                    'event_category': 'Performance'
                });

                // Core Web Vitals
                if ('getLCP' in window) {
                    getLCP(function(metric) {
                        gtag('event', 'Core Web Vitals', {
                            'event_category': 'Performance',
                            'event_label': 'LCP',
                            'value': metric.value
                        });
                    });
                }
            }, 0);
        });
    }

    // Monitor for 404 errors
    if (document.title.includes('404')) {
        gtag('event', '404_error', {
            'event_category': 'Error',
            'event_label': window.location.pathname
        });
    }
</script>

<!-- Critical CSS -->
<style>
    /* Add critical CSS here */
    .lazy-load {
        opacity: 0;
        transition: opacity .3s ease-in-out;
    }

    .lazy-load.loaded {
        opacity: 1;
    }
</style>
