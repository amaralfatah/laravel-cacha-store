<div class="row g-4">
    <!-- Benefit 1 -->
    <div class="col-md-6 col-lg-3">
        <div class="x-benefits-card">
            <div class="x-benefits-card-inner">
                <div class="x-benefits-icon-wrapper">
                    <i class="x-benefits-icon">🔥</i>
                </div>
                <div class="x-benefits-badge">Premium</div>
                <h5 class="x-benefits-card-title">Kualitas Terbaik</h5>
                <p class="x-benefits-card-text">Kami seleksi bahan-bahan lokal terbaik untuk menciptakan cita rasa autentik yang bikin nagih!</p>
                <button class="x-benefits-button" data-button-size="normal">Cobain!</button>
            </div>
        </div>
    </div>

    <!-- Benefit 2 -->
    <div class="col-md-6 col-lg-3">
        <div class="x-benefits-card">
            <div class="x-benefits-card-inner">
                <div class="x-benefits-icon-wrapper">
                    <i class="x-benefits-icon">⚡</i>
                </div>
                <div class="x-benefits-badge">Express</div>
                <h5 class="x-benefits-card-title">Pengiriman Kilat</h5>
                <p class="x-benefits-card-text">Dari dapur langsung ke rumahmu dalam waktu singkat. Snack fresh, masih anget!</p>
                <button class="x-benefits-button" data-button-size="normal">Cobain!</button>
            </div>
        </div>
    </div>

    <!-- Benefit 3 -->
    <div class="col-md-6 col-lg-3">
        <div class="x-benefits-card">
            <div class="x-benefits-card-inner">
                <div class="x-benefits-icon-wrapper">
                    <i class="x-benefits-icon">🌿</i>
                </div>
                <div class="x-benefits-badge">Eco-Friendly</div>
                <h5 class="x-benefits-card-title">Ramah Lingkungan</h5>
                <p class="x-benefits-card-text">Kemasan biodegradable yang stylish dan tidak bikin bumi makin panas. Go green!</p>
                <button class="x-benefits-button" data-button-size="normal">Cobain!</button>
            </div>
        </div>
    </div>

    <!-- Benefit 4 -->
    <div class="col-md-6 col-lg-3">
        <div class="x-benefits-card">
            <div class="x-benefits-card-inner">
                <div class="x-benefits-icon-wrapper">
                    <i class="x-benefits-icon">✨</i>
                </div>
                <div class="x-benefits-badge">100% Natural</div>
                <h5 class="x-benefits-card-title">Tanpa Pengawet</h5>
                <p class="x-benefits-card-text">No MSG, no artificial stuff. Just good old authentic Indonesian flavors yang bikin nostalgia!</p>
                <button class="x-benefits-button" data-button-size="normal">Cobain!</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Benefits Section Styles - Using the provided CSS variables */
    .x-benefits {
        background-color: var(--x-bg-secondary);
        position: relative;
        overflow: hidden;
    }

    /* Section Header Styles */
    .x-benefits-title {
        font-weight: var(--x-title-weight);
        font-size: 2.5rem;
        line-height: var(--x-title-line-height);
        letter-spacing: var(--x-letter-spacing);
        margin-bottom: 1rem;
        background: var(--x-primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        display: inline-block;
        position: relative;
    }

    .x-benefits-subtitle {
        color: var(--x-text-secondary);
        font-size: 1.2rem;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Card Styles */
    .x-benefits-card {
        height: 100%;
        position: relative;
        perspective: 1000px;
    }

    .x-benefits-card-inner {
        background-color: var(--x-bg-primary);
        border-radius: var(--x-card-radius);
        box-shadow: var(--x-card-shadow);
        padding: var(--x-padding-sm);
        height: 100%;
        position: relative;
        overflow: hidden;
        transition: transform 0.4s var(--x-transition-timing),
        box-shadow 0.4s var(--x-transition-timing);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .x-benefits-card:hover .x-benefits-card-inner {
        transform: translateY(-12px) var(--x-hover-scale);
        box-shadow: var(--x-button-shadow-hover);
        filter: var(--x-hover-filter);
    }

    /* Card Badge */
    .x-benefits-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: var(--x-accent-gradient);
        color: white;
        padding: var(--x-badge-padding-sm);
        border-radius: var(--x-button-radius);
        font-size: var(--x-badge-font-sm);
        font-weight: 700;
        transform: rotate(3deg);
        transition: transform 0.3s var(--x-transition-timing);
        z-index: 1;
    }

    .x-benefits-card:hover .x-benefits-badge {
        transform: rotate(-2deg) scale(1.05);
        animation: badgePulse 2s infinite;
    }

    /* Icon Styles */
    .x-benefits-icon-wrapper {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: var(--x-primary-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: var(--x-button-shadow);
        transition: transform 0.3s var(--x-transition-timing);
    }

    .x-benefits-icon {
        font-size: 2rem;
        transition: transform 0.3s var(--x-transition-timing);
    }

    .x-benefits-card:hover .x-benefits-icon-wrapper {
        transform: scale(1.1) rotate(5deg);
    }

    .x-benefits-card:hover .x-benefits-icon {
        transform: scale(1.2);
    }

    /* Card Content Styles */
    .x-benefits-card-title {
        font-weight: var(--x-title-weight);
        font-size: var(--x-title-size-sm);
        margin-bottom: 0.75rem;
        color: var(--x-text-primary);
        position: relative;
        transition: transform 0.3s ease 0.05s;
    }

    .x-benefits-card-text {
        color: var(--x-text-secondary);
        margin-bottom: 1.5rem;
        transition: transform 0.3s ease 0.1s;
        flex-grow: 1;
    }

    /* Button Styles */
    .x-benefits-button {
        background: var(--x-primary-gradient);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: var(--x-button-radius);
        font-weight: 700;
        box-shadow: var(--x-button-shadow);
        transition: transform 0.3s var(--x-transition-timing),
        box-shadow 0.3s var(--x-transition-timing),
        background-position 0.3s ease;
        background-size: 200% auto;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .x-benefits-button::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: var(--x-shimmer-gradient);
        opacity: 0;
        transform: rotate(30deg);
        transition: opacity 0.3s;
        pointer-events: none;
    }

    .x-benefits-button:hover {
        transform: translateY(-3px);
        box-shadow: var(--x-button-shadow-hover);
        background-position: right center;
    }

    .x-benefits-button:hover::before {
        opacity: 1;
        animation: shimmer 1.5s infinite;
    }

    .x-benefits-button:active {
        transform: scale(0.96);
    }

    /* Button Size Variations */
    .x-benefits-button[data-button-size="small"] {
        padding: 0.5rem;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
    }

    .x-benefits-button[data-button-size="featured"] {
        padding: 0.85rem 2rem;
        font-size: 1.1rem;
    }

    /* Social Proof Elements */
    .x-benefits-social-proof {
        display: flex;
        justify-content: center;
    }

    .x-benefits-live-counter {
        display: flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 30px;
        padding: 0.6rem 1.2rem;
        box-shadow: var(--x-card-shadow);
        animation: floating 3s infinite alternate;
    }

    .x-benefits-counter-icon {
        font-size: 1.25rem;
        margin-right: 0.5rem;
    }

    .x-benefits-counter-text {
        font-weight: 600;
        color: var(--x-text-primary);
    }

    /* Animations */
    @keyframes shimmer {
        0% {
            transform: translateX(-100%) rotate(30deg);
        }
        100% {
            transform: translateX(100%) rotate(30deg);
        }
    }

    @keyframes floating {
        0% {
            transform: translateY(0);
        }
        100% {
            transform: translateY(-10px);
        }
    }

    @keyframes badgePulse {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(255, 45, 32, 0.4);
        }
        50% {
            box-shadow: 0 0 0 5px rgba(255, 45, 32, 0);
        }
    }

    /* Scroll Animation Helper */
    .x-benefits [data-aos] {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }

    .x-benefits [data-aos].aos-animate {
        opacity: 1;
        transform: translateY(0);
    }

    /* Responsive Styles */
    @media (max-width: var(--x-mobile-breakpoint)) {
        .x-benefits-title {
            font-size: 2rem;
        }

        .x-benefits-subtitle {
            font-size: 1rem;
        }

        /* Enable horizontal scroll for mobile */
        .x-benefits .row {
            flex-wrap: nowrap;
            overflow-x: auto;
            padding-bottom: 1rem;
            scroll-snap-type: x mandatory;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE/Edge */
        }

        .x-benefits .row::-webkit-scrollbar {
            display: none; /* Chrome/Safari/Opera */
        }

        .x-benefits .col-md-6 {
            min-width: 85%;
            scroll-snap-align: center;
        }
    }

    @media (min-width: var(--x-mobile-breakpoint)) and (max-width: var(--x-tablet-breakpoint)) {
        .x-benefits-card-inner {
            padding: var(--x-padding-sm);
        }
    }

    @media (min-width: var(--x-tablet-breakpoint)) {
        .x-benefits-card-inner {
            padding: var(--x-padding-lg);
        }

        .x-benefits-badge {
            padding: var(--x-badge-padding-lg);
            font-size: var(--x-badge-font-lg);
        }

        .x-benefits-card-title {
            font-size: var(--x-title-size-lg);
        }
    }
</style>

