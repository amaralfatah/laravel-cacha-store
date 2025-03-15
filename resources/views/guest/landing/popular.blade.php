<div class="row g-4">
    @foreach ($bestSellers as $product)
        <div class="col-lg-3 col-md-6">
            <x-guest.product-card :product="$product" button-size="small" :show-badges="true" />
        </div>
    @endforeach
</div>

<div class="x-popular-section-action mt-5">
    <x-guest.button href="#products" type="outline" icon="arrow-right" theme="light" class="mt-4">
        Lihat Semua Produk
    </x-guest.button>
</div>

<style>
    /* Popular Products Section Styling */

    /* Mobile Scrollable Products */
    @media (max-width: 767.98px) {
        #popular .row {
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

        #popular .row::-webkit-scrollbar {
            height: 6px;
        }

        #popular .row::-webkit-scrollbar-track {
            background: var(--x-bg-secondary);
            border-radius: 10px;
        }

        #popular .row::-webkit-scrollbar-thumb {
            background: var(--x-primary-red);
            border-radius: 10px;
        }

        #popular .col-md-6 {
            flex: 0 0 auto;
            width: 75%;
            min-width: 220px;
            max-width: 280px;
            padding-right: 0.75rem;
            padding-left: 0.75rem;
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

    #popular .col-lg-3 {
        opacity: 0;
        animation: fadeInUp 0.6s var(--x-transition-timing) forwards;
    }

    #popular .col-lg-3:nth-child(1) {
        animation-delay: 0.1s;
    }

    #popular .col-lg-3:nth-child(2) {
        animation-delay: 0.2s;
    }

    #popular .col-lg-3:nth-child(3) {
        animation-delay: 0.3s;
    }

    #popular .col-lg-3:nth-child(4) {
        animation-delay: 0.4s;
    }

    #popular .col-lg-3:nth-child(5) {
        animation-delay: 0.5s;
    }

    #popular .col-lg-3:nth-child(6) {
        animation-delay: 0.6s;
    }

    #popular .col-lg-3:nth-child(7) {
        animation-delay: 0.7s;
    }

    #popular .col-lg-3:nth-child(8) {
        animation-delay: 0.8s;
    }

    #popular .x-popular-section-action {
        opacity: 0;
        animation: fadeInUp 0.6s var(--x-transition-timing) forwards;
        animation-delay: 0.9s;
        display: flex;
        justify-content: center;
    }
</style>
