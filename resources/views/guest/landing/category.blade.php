<div class="row">
    {{-- start styling --}}
    @foreach ($categories as $category)
        <div class="col-md-3 col-6 mb-4">
            <div class="x-category-card">
                <div class="x-category-icon">
                    <img src="{{ asset('images/logo-snack-circle.png') }}" alt="{{ $category->name }}" width="40">
                    <div class="x-category-glow"></div>
                </div>
                <h5 class="x-category-title">{{ $category->name }}</h5>
                <p class="x-category-count">{{ $category->products_count }} Produk</p>
                <div class="x-category-hover-effect"></div>
            </div>
        </div>
    @endforeach
    {{-- end styling --}}
</div>

<style>
    /* Category Card Styling */
    .x-category-card {
        position: relative;
        background-color: var(--x-bg-primary);
        border-radius: var(--x-card-radius);
        padding: 1.75rem 1.25rem;
        text-align: center;
        box-shadow: var(--x-card-shadow);
        overflow: hidden;
        transition: transform 0.4s var(--x-transition-timing),
            box-shadow 0.4s ease;
        cursor: pointer;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        will-change: transform, box-shadow;
    }

    .x-category-card:hover {
        transform: translateY(-12px) scale(1.03);
        box-shadow: var(--x-button-shadow-hover);
    }

    .x-category-icon {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 80px;
        height: 80px;
        margin: 0 auto 1.25rem;
        border-radius: 50%;
        background: var(--x-bg-secondary);
        z-index: 1;
        transition: transform 0.3s var(--x-transition-timing);
    }

    .x-category-card:hover .x-category-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .x-category-icon img {
        width: 45px;
        height: 45px;
        object-fit: contain;
        transition: transform 0.4s var(--x-transition-timing);
        z-index: 2;
    }

    .x-category-card:hover .x-category-icon img {
        transform: scale(1.15);
    }

    .x-category-glow {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: 50%;
        background: var(--x-primary-gradient);
        opacity: 0;
        z-index: 1;
        transition: opacity 0.4s ease, transform 0.4s var(--x-transition-timing);
        transform: scale(0.85);
    }

    .x-category-card:hover .x-category-glow {
        opacity: 0.15;
        transform: scale(1.2);
    }

    .x-category-title {
        font-size: var(--x-title-size-sm, 1.2rem);
        font-weight: var(--x-title-weight, 800);
        margin-bottom: 0.5rem;
        color: var(--x-text-primary);
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1),
            color 0.3s ease;
        position: relative;
        z-index: 2;
    }

    .x-category-card:hover .x-category-title {
        color: var(--x-primary-red);
        transform: translateY(-5px);
    }

    .x-category-count {
        font-size: 0.9rem;
        color: var(--x-text-secondary);
        margin-bottom: 0;
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) 0.05s;
        position: relative;
        z-index: 2;
    }

    .x-category-card:hover .x-category-count {
        transform: translateY(-5px);
    }

    .x-category-hover-effect {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--x-primary-gradient);
        transform: scaleX(0);
        transform-origin: center;
        transition: transform 0.3s var(--x-transition-timing);
    }

    .x-category-card:hover .x-category-hover-effect {
        transform: scaleX(1);
    }

    /* Mobile Scrollable Categories */
    @media (max-width: 767.98px) {
        .x-section-wrapper#categories .row {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            padding-bottom: 1.5rem;
            margin-right: -15px;
            margin-left: -15px;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: var(--x-primary-red) var(--x-bg-secondary);
        }

        .x-section-wrapper#categories .row::-webkit-scrollbar {
            height: 6px;
        }

        .x-section-wrapper#categories .row::-webkit-scrollbar-track {
            background: var(--x-bg-secondary);
            border-radius: 10px;
        }

        .x-section-wrapper#categories .row::-webkit-scrollbar-thumb {
            background: var(--x-primary-red);
            border-radius: 10px;
        }

        .x-section-wrapper#categories .col-6 {
            flex: 0 0 auto;
            width: 180px;
            padding-right: 0.5rem;
            padding-left: 0.5rem;
        }

        .x-category-card {
            padding: 1.25rem 1rem;
        }

        .x-category-icon {
            width: 60px;
            height: 60px;
            margin-bottom: 0.75rem;
        }

        .x-category-icon img {
            width: 30px;
            height: 30px;
        }

        .x-category-title {
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }

        .x-category-count {
            font-size: 0.8rem;
        }
    }

    /* Animation for scroll reveal */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .x-section-wrapper#categories .col-md-3 {
        opacity: 0;
        animation: fadeInUp 0.6s var(--x-transition-timing) forwards;
    }

    .x-section-wrapper#categories .col-md-3:nth-child(1) {
        animation-delay: 0.1s;
    }

    .x-section-wrapper#categories .col-md-3:nth-child(2) {
        animation-delay: 0.2s;
    }

    .x-section-wrapper#categories .col-md-3:nth-child(3) {
        animation-delay: 0.3s;
    }

    .x-section-wrapper#categories .col-md-3:nth-child(4) {
        animation-delay: 0.4s;
    }

    .x-section-wrapper#categories .col-md-3:nth-child(5) {
        animation-delay: 0.5s;
    }

    .x-section-wrapper#categories .col-md-3:nth-child(6) {
        animation-delay: 0.6s;
    }

    .x-section-wrapper#categories .col-md-3:nth-child(7) {
        animation-delay: 0.7s;
    }

    .x-section-wrapper#categories .col-md-3:nth-child(8) {
        animation-delay: 0.8s;
    }

    /* Tap effect for touch devices */
    @media (hover: none) {
        .x-category-card:active {
            transform: scale(0.97);
        }
    }
</style>
