@props([
    'id' => null,
    'title' => '',
    'subtitle' => '',
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

{{-- Contoh penggunaan:
<x-section-wrapper
    id="products"
    title="Katalog Produk Kami"
    titleHighlight="Produk"
    subtitle="Temukan berbagai cemilan kekinian khas Pangandaran"
    background="light"
    pattern="true"
>
    <!-- Konten section disini -->
</x-section-wrapper>
--}}


<style>
    /*
 * Section Wrapper Styles
 * Custom component untuk Bootstrap framework
 * ---------------------------------------------
 */

    :root {
        /* Color Palette */
        --x-primary-red: #E83A30;
        --x-secondary-red: #FF5349;
        --x-dark-red: #C80000;
        --x-accent-yellow: #FFD54F;
        --x-accent-orange: #FF9800;
        --x-accent-green: #4CAF50;
        --x-accent-blue: #2196F3;
        --x-accent-purple: #9C27B0;

        /* Background & Text Colors */
        --x-bg-primary: #FFFFFF;
        --x-bg-secondary: #FFF5F2;
        --x-text-primary: #2D3748;
        --x-text-secondary: #4A5568;

        /* Gradients */
        --x-primary-gradient: linear-gradient(135deg, #E83A30 0%, #FF9800 100%);
        --x-accent-gradient: linear-gradient(135deg, #FFD54F 0%, #FF9800 60%, #E83A30 100%);
        --x-cool-gradient: linear-gradient(135deg, #2196F3 0%, #9C27B0 100%);
    }

    /* ======================================
       1. Base Section Styles
       ====================================== */
    .x-section-wrapper {
        position: relative;
        overflow: hidden;
    }

    /* Background Variants */
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

    /* ======================================
       2. Decorative Elements
       ====================================== */
    /* Particles */
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
        background: radial-gradient(circle, rgba(255, 213, 79, 0.15) 0%, rgba(255, 213, 79, 0) 70%);
    }

    .x-section-wrapper-particle-2 {
        width: 400px;
        height: 400px;
        bottom: -200px;
        left: -200px;
        background: radial-gradient(circle, rgba(232, 58, 48, 0.15) 0%, rgba(232, 58, 48, 0) 70%);
    }

    /* Patterns */
    .x-section-wrapper-pattern {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23E83A30' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        z-index: 0;
    }

    /* Pattern variations based on background */
    .x-section-wrapper-gradient .x-section-wrapper-pattern {
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23FFFFFF' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .x-section-wrapper-primary .x-section-wrapper-pattern {
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23FFFFFF' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    /* ======================================
       3. Section Header Styles
       ====================================== */
    .x-section-wrapper-header {
        position: relative;
        margin-bottom: 3rem;
        z-index: 1;
    }

    /* Title */
    .x-section-wrapper-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
        letter-spacing: -0.03em;
        line-height: 1.2;
        color: var(--x-text-primary);
    }

    /* Title variations based on background */
    .x-section-wrapper-gradient .x-section-wrapper-title,
    .x-section-wrapper-primary .x-section-wrapper-title {
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Title highlight */
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
        transition: all 0.3s ease;
    }

    /* Highlight variations based on background */
    .x-section-wrapper-gradient .x-title-wrapper-highlight::after,
    .x-section-wrapper-primary .x-title-wrapper-highlight::after {
        background-color: rgba(255, 213, 79, 0.5);
    }

    /* Hover effect for highlight */
    .x-section-wrapper:hover .x-title-wrapper-highlight::after {
        height: 10px;
        transform: rotate(-2deg);
    }

    /* Subtitle */
    .x-section-wrapper-subtitle {
        font-size: 1.1rem;
        color: var(--x-text-secondary);
        max-width: 700px;
        margin: 0 auto;
        line-height: 1.6;
    }

    /* Subtitle variations based on background */
    .x-section-wrapper-gradient .x-section-wrapper-subtitle,
    .x-section-wrapper-primary .x-section-wrapper-subtitle {
        color: rgba(255, 255, 255, 0.9);
    }

    /* ======================================
       4. Section-Specific Styles
       ====================================== */
    /* Categories section */
    #categories .x-title-wrapper-highlight::after {
        background-color: rgba(76, 175, 80, 0.2);
    }

    /* Popular section */
    #popular .x-section-wrapper-particle-1 {
        background: radial-gradient(circle, rgba(255, 152, 0, 0.15) 0%, rgba(255, 152, 0, 0) 70%);
    }

    /* Testimonials section */
    #testimonials .x-title-wrapper-highlight::after {
        background-color: rgba(33, 150, 243, 0.2);
    }

    /* Gallery section */
    #gallery .x-title-wrapper-highlight::after {
        background-color: rgba(156, 39, 176, 0.2);
    }

    /* ======================================
       5. Media Queries - Responsive Styles
       ====================================== */
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
        .x-section-wrapper-title {
            font-size: 1.5rem;
        }
    }
</style>
