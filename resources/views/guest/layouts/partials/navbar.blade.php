<!-- Navbar Component using Button Component -->
<nav class="x-navbar-container navbar navbar-expand-lg fixed-top">
    <div class="container">
        <!-- Brand Logo and Name -->
        <a class="x-navbar-brand d-flex align-items-center" href="{{route('guest.home')}}#home">
            <div class="x-navbar-logo-container me-2">
                <img src="{{ asset('images/logo-snack-circle.png') }}" alt="Snack Indonesia Logo" height="44">
            </div>
            <span class="x-navbar-brand-text">Snack Indonesia</span>
        </a>

        <!-- Mobile Toggle Button -->
        <button class="x-navbar-toggler navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="x-navbar-link nav-link" href="{{route('guest.home')}}#home">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="x-navbar-link nav-link" href="{{route('guest.home')}}#popular">Terlaris</a>
                </li>
                <li class="nav-item">
                    <a class="x-navbar-link nav-link" href="{{route('guest.home')}}#products">Produk</a>
                </li>
                <li class="nav-item">
                    <a class="x-navbar-link nav-link" href="{{route('guest.home')}}#testimonials">Testimoni</a>
                </li>
                <li class="nav-item">
                    <a class="x-navbar-link nav-link" href="{{route('guest.home')}}#about">Tentang Kami</a>
                </li>
                <li class="nav-item">
                    <a class="x-navbar-link nav-link" href="{{route('guest.home')}}#contact">Kontak</a>
                </li>
                <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                    <x-guest.button href="#marketplace" icon="shopping-cart">
                        Beli Sekarang
                    </x-guest.button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Corresponding CSS for Navbar (Button styles are now in the button component) -->
<style>
    /* Navbar Styles based on Snack Indonesia Theme */
    .x-navbar-container {
        background-color: var(--x-bg-primary);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        padding: 0.75rem 0;
        transition: all 0.3s var(--x-transition-timing);
    }

    .x-navbar-brand {
        text-decoration: none;
        transition: transform 0.3s var(--x-transition-timing);
    }

    .x-navbar-brand:hover {
        transform: translateY(-2px);
    }

    .x-navbar-brand-text {
        font-weight: var(--x-title-weight);
        background: var(--x-primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        color: transparent;
        font-size: 1.4rem;
    }

    .x-navbar-logo-container {
        position: relative;
        overflow: hidden;
        border-radius: 50%;
    }

    .x-navbar-logo-container::after {
        content: '';
        position: absolute;
        top: -100%;
        left: -100%;
        width: 300%;
        height: 300%;
        background: var(--x-shimmer-gradient);
        animation: shimmer 2s infinite;
        pointer-events: none;
    }

    .x-navbar-link {
        color: var(--x-text-primary);
        font-weight: 600;
        padding: 0.5rem 1rem;
        position: relative;
        transition: color 0.3s ease;
    }

    .x-navbar-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: 0;
        right: 0;
        background: var(--x-primary-gradient);
        transition: width 0.3s ease, right 0.3s ease, left 0.3s ease;
    }

    .x-navbar-link:hover {
        color: var(--x-primary-red);
    }

    .x-navbar-link:hover::after {
        width: 100%;
        right: auto;
        left: 0;
    }

    .x-navbar-toggler {
        border: none;
        background: transparent;
        box-shadow: none;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .x-navbar-toggler:focus {
        box-shadow: none;
    }

    .x-navbar-toggler:hover {
        transform: var(--x-hover-scale);
    }

    /* Animation keyframes */
    @keyframes shimmer {
        0% {
            transform: translateX(-100%) skewX(-15deg);
        }

        100% {
            transform: translateX(100%) skewX(-15deg);
        }
    }

    /* Responsive styles */
    @media (max-width: var(--x-mobile-breakpoint)) {
        .x-navbar-brand-text {
            font-size: 1.2rem;
        }
    }

    @media (min-width: var(--x-mobile-breakpoint)) and (max-width: var(--x-tablet-breakpoint)) {
        .x-navbar-container {
            padding: 0.6rem 0;
        }
    }

    @media (min-width: var(--x-tablet-breakpoint)) {
        .x-navbar-link:hover::after {
            width: 80%;
            left: 10%;
        }
    }
</style>
