<div class="x-all-product-container">
    <div class="x-all-product-grid row g-4">
        @foreach ($catalogProducts as $product)
            <div class="col-lg-4 col-md-6">
                <x-guest.product-card :product="$product" :show-badges="true" />
            </div>
        @endforeach
    </div>

    <div class="x-all-product-action mt-5">
        <x-guest.button href="{{ route('guest.shop') }}"  icon="arrow-right" class="mt-4">
            Lihat Semua Produk ({{ $totalProducts }}+)
        </x-guest.button>
    </div>
</div>

<style>
    /* All Products Section Styling */

    /* Main container styles */
    .x-all-product-container {
        position: relative;
    }

    /* Grid layout adjustments for 3-column layout */
    .x-all-product-grid .col-lg-4 {
        transition: opacity 0.5s var(--x-transition-timing),
        transform 0.5s var(--x-transition-timing);
    }

    /* Section action styles */
    .x-all-product-action {
        text-align: center;
        margin-top: 3rem;
    }

    /* Animation for 'Show More' button */
    @keyframes bounceButton {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    .x-all-product-action .x-button {
        animation: bounceButton 3s infinite;
    }

    .x-all-product-action .x-button:hover {
        animation: none;
    }

    /* Mobile scrollable products for 3-column layout */
    @media (max-width: 767.98px) {
        .x-all-product-grid {
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

        .x-all-product-grid::-webkit-scrollbar {
            height: 6px;
        }

        .x-all-product-grid::-webkit-scrollbar-track {
            background: var(--x-bg-secondary);
            border-radius: 10px;
        }

        .x-all-product-grid::-webkit-scrollbar-thumb {
            background: var(--x-primary-red);
            border-radius: 10px;
        }

        .x-all-product-grid .col-md-6 {
            flex: 0 0 auto;
            width: 85%;
            min-width: 250px;
            max-width: 320px;
            padding-right: 0.75rem;
            padding-left: 0.75rem;
        }
    }

    /* Animation for scroll reveal - slightly different timing for the 3-column layout */
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

    .x-all-product-grid .col-lg-4 {
        opacity: 0;
        animation: fadeInUp 0.6s var(--x-transition-timing) forwards;
    }

    /* Staggered animation delays */
    .x-all-product-grid .col-lg-4:nth-child(1) {
        animation-delay: 0.1s;
    }

    .x-all-product-grid .col-lg-4:nth-child(2) {
        animation-delay: 0.2s;
    }

    .x-all-product-grid .col-lg-4:nth-child(3) {
        animation-delay: 0.3s;
    }

    .x-all-product-grid .col-lg-4:nth-child(4) {
        animation-delay: 0.4s;
    }

    .x-all-product-grid .col-lg-4:nth-child(5) {
        animation-delay: 0.5s;
    }

    .x-all-product-grid .col-lg-4:nth-child(6) {
        animation-delay: 0.6s;
    }

    .x-all-product-grid .col-lg-4:nth-child(7) {
        animation-delay: 0.7s;
    }

    .x-all-product-grid .col-lg-4:nth-child(8) {
        animation-delay: 0.8s;
    }

    .x-all-product-grid .col-lg-4:nth-child(9) {
        animation-delay: 0.9s;
    }

    .x-all-product-action {
        opacity: 0;
        animation: fadeInUp 0.6s var(--x-transition-timing) forwards;
        animation-delay: 1s;
    }

    /* Enhanced card styling for the 3-column layout */
    .x-all-product-grid .x-product-card {
        position: relative;
        overflow: hidden;
        border-radius: var(--x-card-radius);
        background-color: var(--x-bg-primary);
        box-shadow: var(--x-card-shadow);
        transition: transform 0.4s var(--x-transition-timing),
        box-shadow 0.4s ease;
        height: 100%;
        will-change: transform, box-shadow;
    }

    .x-all-product-grid .x-product-card:hover {
        transform: translateY(-12px) scale(1.03);
        box-shadow: var(--x-card-shadow-hover);
    }

    /* Adjustments for the larger product cards in 3-column layout */
    .x-all-product-grid .x-product-image-container {
        height: var(--x-img-height-lg);
    }

    .x-all-product-grid .x-product-title {
        font-size: var(--x-title-size-lg);
    }

    .x-all-product-grid .x-product-badge {
        padding: var(--x-badge-padding-lg);
        font-size: var(--x-badge-font-lg);
    }

    /* Total products counter */
    .x-all-product-total-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--x-accent-yellow);
        color: var(--x-text-primary);
        font-weight: 700;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.9rem;
        margin-left: 0.2rem;
        transition: transform 0.3s var(--x-transition-timing);
    }

    .x-button:hover .x-all-product-total-count {
        transform: scale(1.15);
    }

    /* Filter and sort options for all products */
    .x-all-product-filters {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .x-all-product-filter-group {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .x-all-product-filter-button {
        padding: 0.5rem 1rem;
        border-radius: var(--x-button-radius);
        background: var(--x-bg-primary);
        color: var(--x-text-secondary);
        border: 1px solid rgba(0, 0, 0, 0.1);
        font-size: 0.9rem;
        transition: all 0.3s var(--x-transition-timing);
        cursor: pointer;
    }

    .x-all-product-filter-button:hover {
        background: var(--x-bg-secondary);
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .x-all-product-filter-button.active {
        background: var(--x-primary-red);
        color: white;
        border-color: var(--x-primary-red);
    }

    /* Empty state message */
    .x-all-product-empty {
        text-align: center;
        padding: 3rem;
        background: var(--x-bg-secondary);
        border-radius: var(--x-card-radius);
        margin: 2rem 0;
    }

    .x-all-product-empty-icon {
        font-size: 3rem;
        color: var(--x-text-secondary);
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .x-all-product-empty-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: var(--x-text-primary);
    }

    .x-all-product-empty-text {
        color: var(--x-text-secondary);
        margin-bottom: 1.5rem;
    }

    /* Loading state */
    .x-all-product-loading {
        position: relative;
        min-height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @keyframes shimmer {
        0% {
            background-position: -468px 0;
        }

        100% {
            background-position: 468px 0;
        }
    }

    .x-all-product-skeleton {
        background: var(--x-bg-secondary);
        border-radius: var(--x-card-radius);
        overflow: hidden;
        height: 100%;
        position: relative;
    }

    .x-all-product-skeleton::after {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.4) 50%, rgba(255, 255, 255, 0) 100%);
        animation: shimmer 1.5s infinite;
        transform: translateX(-100%);
    }

    .x-all-product-skeleton-image {
        height: var(--x-img-height-lg);
        background: rgba(0, 0, 0, 0.05);
        margin-bottom: 1rem;
    }

    .x-all-product-skeleton-title {
        height: 1.5rem;
        width: 80%;
        background: rgba(0, 0, 0, 0.05);
        margin-bottom: 0.5rem;
        border-radius: 4px;
    }

    .x-all-product-skeleton-price {
        height: 1.2rem;
        width: 40%;
        background: rgba(0, 0, 0, 0.05);
        margin-bottom: 1rem;
        border-radius: 4px;
    }

    .x-all-product-skeleton-button {
        height: 2.5rem;
        width: 60%;
        background: rgba(0, 0, 0, 0.05);
        border-radius: var(--x-button-radius);
    }

    /* Mobile adjustments */
    @media (max-width: 575.98px) {
        .x-all-product-filters {
            flex-direction: column;
            align-items: stretch;
        }

        .x-all-product-filter-group {
            justify-content: center;
        }
    }
</style>
