<style>
    :root {
        /* Main Color Palette - Enhanced for Vibrance */
        --x-primary-red: #FF2D20;
        /* Lebih vibrant daripada merah sebelumnya */
        --x-secondary-red: #FF5349;
        /* Lebih pop & playful */
        --x-dark-red: #C80000;
        --x-accent-yellow: #FFD700;
        /* Gold tone untuk meningkatkan premium feel */
        --x-accent-orange: #FF8A00;
        /* Warna tambahan untuk variasi & energi */
        --x-bg-primary: #FFFFFF;
        --x-bg-secondary: #FFF9F7;
        /* Subtle warm tone daripada pink */
        --x-text-primary: #1F1F1F;
        /* Sedikit lebih soft dari pure black */
        --x-text-secondary: #666666;

        /* Gradient - Enhanced untuk visual impact */
        --x-primary-gradient: linear-gradient(135deg, var(--x-primary-red) 0%, var(--x-accent-orange) 100%);
        --x-accent-gradient: linear-gradient(135deg, var(--x-accent-yellow) 0%, var(--x-accent-orange) 60%, var(--x-primary-red) 100%);

        /* Shadows - More pronounced for depth */
        --x-card-shadow: 0 15px 30px rgba(255, 45, 32, 0.15), 0 5px 15px rgba(0, 0, 0, 0.08);
        --x-button-shadow: 0 6px 20px rgba(255, 45, 32, 0.3);
        --x-button-shadow-hover: 0 10px 30px rgba(255, 45, 32, 0.4);

        /* Border Radius - Slightly more rounded for friendly feel */
        --x-card-radius: 1.75rem;
        --x-button-radius: 40px;

        /* Animation - Enhanced for eye-catching effects */
        --x-transition-timing: cubic-bezier(0.34, 1.56, 0.64, 1);
        /* Springier, more playful */
        --x-hover-scale: scale(1.08) rotate(1deg);
        --x-hover-filter: brightness(1.1) contrast(1.05) saturate(1.1);
        --x-shimmer-gradient: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.3) 50%, rgba(255, 255, 255, 0) 100%);

        /* Typography - Bolder for impact */
        --x-title-weight: 800;
        --x-title-line-height: 1.2;
        --x-letter-spacing: -0.03em;
        /* Tighter letter spacing for modern look */

        /* Grid 4-column Specific */
        --x-title-size-sm: 1.2rem;
        --x-img-height-sm: 190px;
        --x-padding-sm: 1.5rem;
        --x-badge-padding-sm: 6px 12px;
        --x-badge-font-sm: 0.75rem;

        /* Grid 3-column Specific */
        --x-title-size-lg: 1.45rem;
        --x-img-height-lg: 240px;
        --x-padding-lg: 1.75rem;
        --x-badge-padding-lg: 8px 16px;
        --x-badge-font-lg: 0.85rem;

        /* Responsiveness */
        --x-mobile-breakpoint: 768px;
        --x-tablet-breakpoint: 992px;
        --x-desktop-breakpoint: 1200px;

        /* Footer Specific Colors */
        --x-footer-bg: #1A1A1A;
        --x-footer-text: rgba(255, 255, 255, 0.8);
        --x-footer-title: #FFFFFF;
        --x-footer-link: rgba(255, 255, 255, 0.7);
        --x-footer-link-hover: #FFFFFF;
        --x-footer-divider: rgba(255, 255, 255, 0.1);

        /* Hero Section Specific */
        --x-hero-badge-bg: rgba(255, 255, 255, 0.15);
        --x-hero-highlight-bg: var(--x-accent-yellow);
        --x-hero-button-text: var(--x-primary-red);
        --x-hero-rating-star: var(--x-accent-yellow);
        --x-hero-shape-opacity: 0.1;
    }

    body {
        font-family: 'Outfit', sans-serif;
        overflow-x: hidden;
    }
</style>
