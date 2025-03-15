@props([
    'id' => null,
    'title' => '',
    'subtitle' => '',
    'badge' => '',
    'badgeIcon' => null,
    'titleHighlight' => null,
    'background' => 'light', // 'light', 'primary', 'secondary', 'gradient'
    'pattern' => false,
    'particles' => false,
    'centered' => true,
    'padding' => 'normal', // 'normal', 'large', 'small'
])

@php
    $sectionClass = match ($background) {
        'primary' => 'x-section-wrapper-primary',
        'secondary' => 'x-section-wrapper-secondary',
        'gradient' => 'x-section-wrapper-gradient',
        default => 'x-section-wrapper-light',
    };

    $paddingClass = match ($padding) {
        'large' => 'py-6',
        'small' => 'py-4',
        default => 'py-5',
    };
@endphp

<section class="x-section-wrapper {{ $sectionClass }} {{ $paddingClass }}" {{ $id ? "id={$id}" : '' }}>
    @if ($particles)
        <div class="x-section-wrapper-particle x-section-wrapper-particle-1"></div>
        <div class="x-section-wrapper-particle x-section-wrapper-particle-2"></div>
    @endif

    @if ($pattern)
        <div class="x-section-wrapper-pattern"></div>
    @endif

    <div class="container py-4">
        @if ($title)
            <div class="x-section-wrapper-header {{ $centered ? 'text-center' : '' }} mb-5">
                @if ($badge)
                    <div class="x-section-wrapper-badge">
                        @if ($badgeIcon)
                            <i class="fas fa-{{ $badgeIcon }}"></i>
                        @endif
                        <span>{{ $badge }}</span>
                    </div>
                @endif

                <h2 class="x-section-wrapper-title">
                    {!! Str::replace(
                        $titleHighlight,
                        '<span class="x-title-wrapper-highlight">' . $titleHighlight . '</span>',
                        $title,
                    ) !!}
                </h2>

                @if ($subtitle)
                    <p class="x-section-wrapper-subtitle">{{ $subtitle }}</p>
                @endif
            </div>
        @endif

        {{ $slot }}
    </div>
</section>

{{-- <x-section-wrapper
    id="products"
    title="Katalog Produk Kami"
    titleHighlight="Produk"
    subtitle="Temukan berbagai cemilan kekinian khas Pangandaran"
    badge="Katalog"
    badgeIcon="store"
    background="light"
    pattern="true"
> --}}


<!-- CSS STYLES UNTUK SECTION WRAPPER -->
<style>
    :root {
        /* Main Color Palette - Enhanced for Vibrance */
        --x-primary-red: #FF2D20;
        --x-secondary-red: #FF5349;
        --x-dark-red: #C80000;
        --x-accent-yellow: #FFD700;
        --x-accent-orange: #FF8A00;
        --x-bg-primary: #FFFFFF;
        --x-bg-secondary: #FFF9F7;
        --x-text-primary: #1F1F1F;
        --x-text-secondary: #666666;

        /* Gradient - Enhanced for visual impact */
        --x-primary-gradient: linear-gradient(135deg, var(--x-primary-red) 0%, var(--x-accent-orange) 100%);
        --x-accent-gradient: linear-gradient(135deg, var(--x-accent-yellow) 0%, var(--x-accent-orange) 60%, var(--x-primary-red) 100%);
    }

    /* Base Section Styles */
    .x-section-wrapper {
        position: relative;
        overflow: hidden;
    }

    /* Section Background Variants */
    .x-section-wrapper-light {
        background-color: var(--x-bg-primary);
    }

    .x-section-wrapper-secondary {
        background-color: var(--x-bg-secondary);
    }

    .x-section-wrapper-primary {
        background-color: var(--x-primary-red);
        color: white;
    }

    .x-section-wrapper-gradient {
        background: var(--x-primary-gradient);
        color: white;
    }

    /* Decorative Elements */
    .x-section-wrapper-particle {
        position: absolute;
        border-radius: 50%;
        filter: blur(40px);
        z-index: 0;
    }

    .x-section-wrapper-particle-1 {
        width: 500px;
        height: 500px;
        top: -250px;
        right: -250px;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.1) 0%, rgba(255, 215, 0, 0) 70%);
    }

    .x-section-wrapper-particle-2 {
        width: 400px;
        height: 400px;
        bottom: -200px;
        left: -200px;
        background: radial-gradient(circle, rgba(255, 45, 32, 0.1) 0%, rgba(255, 45, 32, 0) 70%);
    }

    .x-section-wrapper-pattern {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23FF2D20' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        z-index: 0;
    }

    .x-section-wrapper-gradient .x-section-wrapper-pattern {
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23FFFFFF' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .x-section-wrapper-primary .x-section-wrapper-pattern {
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23FFFFFF' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    /* Section Header Styles */
    .x-section-wrapper-header {
        position: relative;
        margin-bottom: 3rem;
        z-index: 1;
    }

    .x-section-wrapper-badge {
        display: inline-flex;
        align-items: center;
        background: rgba(255, 45, 32, 0.08);
        padding: 8px 16px;
        border-radius: 40px;
        font-weight: 700;
        font-size: 0.9rem;
        margin-bottom: 1.2rem;
        color: var(--x-primary-red);
        gap: 8px;
    }

    .x-section-wrapper-gradient .x-section-wrapper-badge,
    .x-section-wrapper-primary .x-section-wrapper-badge {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .x-section-wrapper-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
        letter-spacing: -0.03em;
        line-height: 1.2;
        color: var(--x-text-primary);
    }

    .x-section-wrapper-gradient .x-section-wrapper-title,
    .x-section-wrapper-primary .x-section-wrapper-title {
        color: white;
    }

    .x-title-wrapper-highlight {
        position: relative;
        display: inline-block;
        z-index: 1;
    }

    .x-title-wrapper-highlight::after {
        content: '';
        position: absolute;
        bottom: 5px;
        left: 0;
        width: 100%;
        height: 8px;
        background-color: var(--x-accent-yellow);
        border-radius: 4px;
        z-index: -1;
        transform: rotate(-1deg);
    }

    .x-section-wrapper-gradient .x-title-wrapper-highlight::after,
    .x-section-wrapper-primary .x-title-wrapper-highlight::after {
        background-color: rgba(255, 255, 255, 0.3);
    }

    .x-section-wrapper-subtitle {
        font-size: 1.1rem;
        color: var(--x-text-secondary);
        max-width: 600px;
        margin: 0 auto;
    }

    .x-section-wrapper-gradient .x-section-wrapper-subtitle,
    .x-section-wrapper-primary .x-section-wrapper-subtitle {
        color: rgba(255, 255, 255, 0.9);
    }

    /* Responsive Styles */
    @media (max-width: 991.98px) {
        .x-section-wrapper-title {
            font-size: 2rem;
        }
    }

    @media (max-width: 767.98px) {
        .x-section-wrapper-title {
            font-size: 1.75rem;
        }

        .x-section-wrapper-subtitle {
            font-size: 1rem;
        }
    }

    @media (max-width: 575.98px) {
        .x-section-wrapper-badge {
            font-size: 0.8rem;
            padding: 6px 12px;
        }

        .x-section-wrapper-title {
            font-size: 1.5rem;
        }
    }
</style>
