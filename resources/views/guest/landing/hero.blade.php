<!-- Hero Section with Enhanced Styling -->
<section class="x-hero-section" id="home">
    <!-- Animated Background Elements -->
    <div class="x-hero-particle x-hero-particle-1"></div>
    <div class="x-hero-particle x-hero-particle-2"></div>
    <div class="x-hero-particle x-hero-particle-3"></div>
    <div class="x-hero-pattern"></div>

    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="x-hero-content" data-animation="fade-in">
                    <div class="x-hero-badge">
                        <i class="fas fa-fire-alt me-2"></i>Cemilan Kekinian Asli Pangandaran
                        <div class="x-hero-badge-glow"></div>
                    </div>

                    <h1 class="x-hero-title">
                        Rasakan Sensasi <span class="x-hero-text-gradient">Kelezatan</span>
                        <span class="x-hero-highlight">Snack Kekinian</span>
                        Yang Bikin <span class="x-hero-text-accent">Nagih!</span>
                    </h1>

                    <p class="x-hero-description">
                        Cemilan inovasi terkini dengan rasa unik, bahan berkualitas premium, dan kemasan trendy untuk
                        menemani seru-seruan kamu bareng squad! <span class="x-hero-emoji">🔥</span>
                    </p>

                    <div class="x-hero-buttons">
                        <a href="#popular" class="x-hero-button x-hero-button-primary">
                            <i class="fas fa-fire me-2"></i>Produk Terlaris
                            <span class="x-button-arrow">→</span>
                            <div class="x-button-spotlight"></div>
                        </a>
                        <a href="#about" class="x-hero-button x-hero-button-outline">
                            <i class="fas fa-info-circle me-2"></i>Tentang Kami
                        </a>
                    </div>

                    <div class="x-hero-social-proof">

                        {{-- MASIH DUMMY --}}
                        <div class="x-hero-avatars">
                            <div class="x-hero-avatar">
                                <img src="{{ asset('images/profiles/dummy_photo_1.jpg') }}" alt="Customer">
                            </div>
                            <div class="x-hero-avatar">
                                <img src="{{ asset('images/profiles/dummy_photo_5.jpg') }}" alt="Customer">
                            </div>
                            <div class="x-hero-avatar">
                                <img src="{{ asset('images/profiles/dummy_photo_3.jpg') }}" alt="Customer">
                            </div>
                            <div class="x-hero-avatar x-hero-avatar-more">
                                <span>+2K</span>
                            </div>
                        </div>

                        <div class="x-hero-rating">
                            <div class="x-hero-rating-score">
                                4.9
                                <div class="x-hero-stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                            <div class="x-hero-rating-count">dari 2,500+ review</div>
                        </div>
                    </div>

                    <!-- Added "Most Popular" Tags -->
                    <div class="x-hero-trending">
                        <div class="x-hero-trending-label">
                            <i class="fas fa-bolt"></i> Paling Dicari:
                        </div>
                        <div class="x-hero-trending-tags">
                            <a href="#" class="x-hero-tag">Makaroni Pedas</a>
                            <a href="#" class="x-hero-tag">Basreng</a>
                            <a href="#" class="x-hero-tag">Keripik Singkong</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 position-relative x-hero-image-container">
                <div class="x-hero-image-wrapper" data-animation="scale-in">
                    <img src="{{ asset('images/products/hero-image.png') }}" alt="Aneka Snack Kekinian"
                        class="x-hero-image">

                    <!-- Floating Product Elements -->
                    <div class="x-hero-floating-element x-hero-element-makaroni">
                        <div class="x-hero-element-icon">🌶️</div>
                        <div class="x-hero-element-text">Super Pedas</div>
                    </div>

                    <div class="x-hero-floating-element x-hero-element-basreng">
                        <div class="x-hero-element-icon">✨</div>
                        <div class="x-hero-element-text">Renyah Gurih</div>
                    </div>

                    <div class="x-hero-floating-element x-hero-element-keripik">
                        <div class="x-hero-element-icon">🥇</div>
                        <div class="x-hero-element-text">Best Seller</div>
                    </div>
                </div>

                @if ($biggestDiscount)
                    <div class="x-hero-discount">
                        <div class="x-hero-discount-inner">
                            <span class="x-hero-discount-value">
                                {{ number_format($biggestDiscount->value) }}{{ $biggestDiscount->type === 'percentage' ? '%' : 'Rp' }}
                            </span>
                            <span class="x-hero-discount-label">DISKON</span>
                            <span class="x-hero-discount-sublabel">HARI INI</span>
                        </div>
                    </div>
                @endif

                <!-- Authenticity Badge -->
                <div class="x-hero-authenticity">
                    <div class="x-hero-authenticity-icon">✓</div>
                    <div class="x-hero-authenticity-text">100% Original</div>
                </div>

                <div class="x-hero-shape x-hero-shape-1"></div>
                <div class="x-hero-shape x-hero-shape-2"></div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Hero Section Styling with Enhanced Theme Variables */
    .x-hero-section {
        background: var(--x-primary-gradient, linear-gradient(135deg, #FF2D20 0%, #FF8A00 100%));
        padding-top: 10rem;
        padding-bottom: 6rem;
        position: relative;
        overflow: hidden;
        color: white;
    }

    /* Animated Background Particles */
    .x-hero-particle {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
        filter: blur(20px);
        z-index: 1;
    }

    .x-hero-particle-1 {
        width: 400px;
        height: 400px;
        top: -200px;
        right: -200px;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.15) 0%, rgba(255, 215, 0, 0) 70%);
        animation: float 20s ease-in-out infinite;
    }

    .x-hero-particle-2 {
        width: 300px;
        height: 300px;
        bottom: -150px;
        left: -100px;
        background: radial-gradient(circle, rgba(255, 45, 32, 0.15) 0%, rgba(255, 45, 32, 0) 70%);
        animation: float 15s ease-in-out infinite reverse;
    }

    .x-hero-particle-3 {
        width: 200px;
        height: 200px;
        top: 30%;
        left: 20%;
        background: radial-gradient(circle, rgba(255, 138, 0, 0.1) 0%, rgba(255, 138, 0, 0) 70%);
        animation: float 12s ease-in-out 2s infinite;
    }

    .x-hero-pattern {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        z-index: 1;
    }

    /* Content Animation */
    .x-hero-content {
        position: relative;
        z-index: 2;
    }

    [data-animation="fade-in"] {
        animation: fadeIn 0.8s ease-out forwards;
    }

    [data-animation="scale-in"] {
        animation: scaleIn 0.8s var(--x-transition-timing, cubic-bezier(0.34, 1.56, 0.64, 1)) forwards;
    }

    /* Badge Styling - Enhanced */
    .x-hero-badge {
        display: inline-flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.15);
        padding: 8px 16px;
        border-radius: var(--x-button-radius, 40px);
        font-weight: 700;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
        backdrop-filter: blur(5px);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .x-hero-badge i {
        color: var(--x-accent-yellow, #FFD700);
    }

    .x-hero-badge-glow {
        position: absolute;
        top: 0;
        left: -100%;
        width: 50%;
        height: 100%;
        background: var(--x-shimmer-gradient, linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.3) 50%, rgba(255, 255, 255, 0) 100%));
        animation: shimmer 3s infinite;
    }

    /* Title Styling - Enhanced with Gradient Text */
    .x-hero-title {
        font-size: 2.75rem;
        font-weight: var(--x-title-weight, 800);
        line-height: var(--x-title-line-height, 1.2);
        margin-bottom: 1.5rem;
        letter-spacing: var(--x-letter-spacing, -0.03em);
    }

    .x-hero-text-gradient {
        background: linear-gradient(135deg, #FFFFFF 0%, #FFD700 40%, #FFD700 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-fill-color: transparent;
        font-style: italic;
        text-shadow: 0 2px 10px rgba(255, 215, 0, 0.5);
        padding-right: 5px;
        /* Tambahan padding untuk mencegah huruf 'n' terpotong */
        display: inline-block;
        /* Memastikan padding diterapkan dengan benar */
    }

    .x-hero-text-accent {
        color: var(--x-accent-yellow, #FFD700);
        position: relative;
        display: inline-block;
        transform: rotate(-2deg);
    }

    .x-hero-highlight {
        position: relative;
        display: inline-block;
    }

    .x-hero-highlight::after {
        content: '';
        position: absolute;
        bottom: 5px;
        left: 0;
        width: 100%;
        height: 8px;
        background-color: var(--x-accent-yellow, #FFD700);
        border-radius: 4px;
        z-index: -1;
        transform: rotate(-1deg);
    }

    /* Description Styling */
    .x-hero-description {
        font-size: 1.1rem;
        opacity: 0.95;
        margin-bottom: 2rem;
        max-width: 540px;
        line-height: 1.6;
    }

    .x-hero-emoji {
        display: inline-block;
        animation: shake 2.5s infinite;
        transform-origin: center;
    }

    /* Button Styling - Enhanced with Spotlight Effect */
    .x-hero-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2.5rem;
    }

    .x-hero-button {
        display: inline-flex;
        align-items: center;
        padding: 14px 28px;
        border-radius: var(--x-button-radius, 40px);
        font-weight: 700;
        font-size: 1rem;
        transition: all 0.3s var(--x-transition-timing, cubic-bezier(0.34, 1.56, 0.64, 1));
        text-decoration: none;
        position: relative;
        overflow: hidden;
    }

    .x-hero-button-primary {
        background: white;
        color: var(--x-primary-red, #FF2D20);
        box-shadow: var(--x-button-shadow, 0 6px 20px rgba(255, 45, 32, 0.3));
    }

    .x-button-spotlight {
        position: absolute;
        top: -100%;
        left: -100%;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.8) 0%, rgba(255, 215, 0, 0) 70%);
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
        z-index: 1;
    }

    .x-hero-button-primary:hover {
        transform: translateY(-4px);
        box-shadow: var(--x-button-shadow-hover, 0 10px 30px rgba(255, 45, 32, 0.4));
        color: var(--x-primary-red, #FF2D20);
    }

    .x-hero-button-primary:hover .x-button-spotlight {
        opacity: 1;
        animation: spotlight 1s infinite alternate;
    }

    .x-button-arrow {
        display: inline-block;
        margin-left: 6px;
        position: relative;
        z-index: 2;
        transition: transform 0.3s ease;
    }

    .x-hero-button-primary:hover .x-button-arrow {
        transform: translateX(6px);
    }

    .x-hero-button-outline {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(5px);
    }

    .x-hero-button-outline:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: white;
        transform: translateY(-4px);
        color: white;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    /* Social Proof Section - Enhanced */
    .x-hero-social-proof {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
    }

    .x-hero-avatars {
        display: flex;
    }

    .x-hero-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: -12px;
        position: relative;
        transition: transform 0.3s ease;
    }

    .x-hero-avatar:hover {
        transform: translateY(-5px);
        z-index: 5;
    }

    .x-hero-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 2px solid white;
        object-fit: cover;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .x-hero-avatar-more {
        background: var(--x-primary-gradient, linear-gradient(135deg, #FF2D20 0%, #FF8A00 100%));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 0.8rem;
        border: 2px solid white;
    }

    .x-hero-rating {
        margin-left: 1.5rem;
    }

    .x-hero-rating-score {
        font-weight: 700;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .x-hero-stars {
        display: inline-flex;
        color: var(--x-accent-yellow, #FFD700);
    }

    .x-hero-stars i {
        margin-right: 2px;
    }

    .x-hero-rating-count {
        font-size: 0.9rem;
        opacity: 0.8;
    }

    /* Trending Tags - New Component */
    .x-hero-trending {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .x-hero-trending-label {
        font-size: 0.9rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .x-hero-trending-label i {
        color: var(--x-accent-yellow, #FFD700);
    }

    .x-hero-trending-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .x-hero-tag {
        display: inline-flex;
        padding: 5px 12px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .x-hero-tag:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-3px);
        color: white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    /* Hero Image Container - Enhanced with Floating Elements */
    .x-hero-image-container {
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 2;
    }

    .x-hero-image-wrapper {
        position: relative;
        border-radius: var(--x-card-radius, 1.75rem);
        overflow: visible;
        transform-style: preserve-3d;
        perspective: 1000px;
    }

    .x-hero-image-wrapper:hover {
        transform: var(--x-hover-scale, scale(1.05) rotate(2deg));
        filter: var(--x-hover-filter, brightness(1.1) contrast(1.05) saturate(1.1));
    }

    .x-hero-image {
        max-width: 100%;
        height: auto;
        max-height: 500px;
        border-radius: var(--x-card-radius, 1.75rem);
        box-shadow: var(--x-card-shadow, 0 15px 30px rgba(255, 45, 32, 0.15), 0 5px 15px rgba(0, 0, 0, 0.08));
        transition: transform 0.5s var(--x-transition-timing, cubic-bezier(0.34, 1.56, 0.64, 1));
    }

    /* Floating Elements */
    .x-hero-floating-element {
        position: absolute;
        background: white;
        border-radius: 12px;
        padding: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        animation: float 4s ease-in-out infinite;
    }

    .x-hero-element-makaroni {
        top: 10%;
        left: -5%;
        animation-delay: 0.5s;
    }

    .x-hero-element-basreng {
        top: 40%;
        right: -8%;
        animation-delay: 1s;
    }

    .x-hero-element-keripik {
        bottom: 15%;
        left: 10%;
        animation-delay: 1.5s;
    }

    .x-hero-element-icon {
        font-size: 1.2rem;
    }

    .x-hero-element-text {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--x-text-primary, #1F1F1F);
    }

    /* Authenticity Badge - New Component */
    .x-hero-authenticity {
        position: absolute;
        bottom: 30px;
        right: 30px;
        background: white;
        border-radius: 12px;
        padding: 10px 15px;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        z-index: 10;
    }

    .x-hero-authenticity-icon {
        width: 24px;
        height: 24px;
        background: var(--x-primary-gradient, linear-gradient(135deg, #FF2D20 0%, #FF8A00 100%));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 0.8rem;
    }

    .x-hero-authenticity-text {
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--x-text-primary, #1F1F1F);
    }

    /* Discount Badge - Enhanced */
    .x-hero-discount {
        position: absolute;
        top: 30px;
        right: 30px;
        z-index: 10;
        animation: pulse 2s infinite;
    }

    .x-hero-discount-inner {
        width: 100px;
        height: 100px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: var(--x-primary-gradient, linear-gradient(135deg, #FF2D20 0%, #FF8A00 100%));
        color: white;
        border-radius: 50%;
        box-shadow: var(--x-card-shadow, 0 15px 30px rgba(255, 45, 32, 0.15), 0 5px 15px rgba(0, 0, 0, 0.08));
        border: 3px solid white;
        position: relative;
    }

    .x-hero-discount-inner::before {
        content: '';
        position: absolute;
        top: -6px;
        right: -6px;
        bottom: -6px;
        left: -6px;
        border: 2px dashed white;
        border-radius: 50%;
        animation: rotate 10s linear infinite;
    }

    .x-hero-discount-value {
        font-size: 1.75rem;
        font-weight: 900;
        line-height: 1;
    }

    .x-hero-discount-label {
        font-size: 0.8rem;
        font-weight: 800;
        letter-spacing: 1px;
    }

    .x-hero-discount-sublabel {
        font-size: 0.65rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        opacity: 0.9;
    }

    /* Shapes - Maintained from Original */
    .x-hero-shape {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        z-index: 1;
    }

    .x-hero-shape-1 {
        width: 300px;
        height: 300px;
        top: -150px;
        right: -150px;
        animation: float 15s ease-in-out infinite;
    }

    .x-hero-shape-2 {
        width: 200px;
        height: 200px;
        bottom: -100px;
        left: -100px;
        animation: float 12s ease-in-out infinite reverse;
    }

    /* Animations */
    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    @keyframes shimmer {
        0% {
            left: -100%;
        }

        100% {
            left: 200%;
        }
    }

    @keyframes float {
        0% {
            transform: translateY(0) rotate(0);
        }

        50% {
            transform: translateY(-10px) rotate(3deg);
        }

        100% {
            transform: translateY(0) rotate(0);
        }
    }

    @keyframes shake {
        0% {
            transform: rotate(0);
        }

        25% {
            transform: rotate(8deg);
        }

        50% {
            transform: rotate(0);
        }

        75% {
            transform: rotate(-8deg);
        }

        100% {
            transform: rotate(0);
        }
    }

    @keyframes spotlight {
        0% {
            opacity: 0.7;
            transform: scale(1);
        }

        100% {
            opacity: 0.9;
            transform: scale(1.2);
        }
    }

    @keyframes rotate {
        0% {
            transform: rotate(0);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes fadeIn {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes scaleIn {
        0% {
            opacity: 0;
            transform: scale(0.9);
        }

        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Responsive Styles - Maintained with Enhancements */
    @media (max-width: 1199.98px) {
        .x-hero-title {
            font-size: 2.25rem;
        }

        .x-hero-discount-inner {
            width: 90px;
            height: 90px;
        }

        .x-hero-discount-value {
            font-size: 1.5rem;
        }

        .x-hero-floating-element {
            display: none;
        }
    }

    @media (max-width: 991.98px) {
        .x-hero-section {
            padding-top: 7rem;
            padding-bottom: 4rem;
            text-align: center;
        }

        .x-hero-description {
            margin-left: auto;
            margin-right: auto;
        }

        .x-hero-buttons {
            justify-content: center;
        }

        .x-hero-image-container {
            margin-top: 3rem;
        }

        .x-hero-social-proof {
            justify-content: center;
        }

        .x-hero-trending {
            justify-content: center;
        }

        .x-hero-trending-label {
            width: 100%;
            justify-content: center;
            margin-bottom: 10px;
        }

        .x-hero-discount {
            top: 15px;
            right: 15px;
        }

        .x-hero-authenticity {
            bottom: 15px;
            right: 15px;
        }

        .x-hero-floating-element {
            display: none;
        }
    }

    @media (max-width: 767.98px) {
        .x-hero-title {
            font-size: 2rem;
        }

        .x-hero-discount-inner {
            width: 80px;
            height: 80px;
        }

        .x-hero-discount-value {
            font-size: 1.25rem;
        }

        .x-hero-discount-sublabel {
            font-size: 0.6rem;
        }

        .x-hero-buttons {
            flex-direction: column;
            gap: 0.75rem;
        }

        .x-hero-button {
            width: 100%;
            justify-content: center;
        }

        .x-hero-authenticity {
            padding: 8px 12px;
        }

        .x-hero-authenticity-icon {
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
        }

        .x-hero-authenticity-text {
            font-size: 0.8rem;
        }

        .x-hero-trending-tags {
            justify-content: center;
        }
    }

    @media (max-width: 575.98px) {
        .x-hero-section {
            padding-top: 6rem;
            padding-bottom: 3rem;
        }

        .x-hero-title {
            font-size: 1.75rem;
        }

        .x-hero-badge {
            font-size: 0.8rem;
            padding: 6px 12px;
        }

        .x-hero-description {
            font-size: 1rem;
        }

        .x-hero-rating-score {
            font-size: 1rem;
        }

        .x-hero-rating-count {
            font-size: 0.8rem;
        }

        .x-hero-avatar {
            width: 35px;
            height: 35px;
        }

        .x-hero-tag {
            padding: 4px 10px;
            font-size: 0.8rem;
        }
    }
</style>
