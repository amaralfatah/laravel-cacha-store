<div class="row g-4">

    {{-- MASIH DUMMY --}}
    <!-- Testimonial 1 -->
    <div class="col-md-4">
        <div class="x-testimonial-card">
            <div class="x-testimonial-badge">Verified Buyer</div>
            <div class="x-testimonial-content">
                <div class="x-testimonial-quote-icon">
                    <i class="fas fa-quote-left"></i>
                </div>
                <p class="x-testimonial-text">"Makaroni pedasnya juara banget! Level 3-nya bener-bener bikin
                    keringetan tapi nagih. Packaging-nya juga kekinian banget, cocok buat anak muda. Pengiriman
                    cepat dan customer service-nya ramah."</p>
                <div class="x-testimonial-product">
                    <span class="x-testimonial-product-icon">🌶️</span>
                    <span class="x-testimonial-product-name">Makaroni Pedas Level 3</span>
                </div>
            </div>
            <div class="x-testimonial-footer">
                <div class="x-testimonial-profile">
                    <div class="x-testimonial-avatar">
                        <img src="{{ asset('images/profiles/dummy_photo_1.jpg') }}" alt="Testimoni">
                    </div>
                    <div class="x-testimonial-info">
                        <h5 class="x-testimonial-name">Riska Amelia</h5>
                        <div class="x-testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                <div class="x-testimonial-meta">
                    <div class="x-testimonial-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Surabaya, Jawa Timur</span>
                    </div>
                    <div class="x-testimonial-time">
                        <i class="far fa-clock"></i>
                        <span>2 hari yang lalu</span>
                    </div>
                </div>
            </div>
            <div class="x-testimonial-hover-effect"></div>
        </div>
    </div>

    <!-- Testimonial 2 -->
    <div class="col-md-4">
        <div class="x-testimonial-card">
            <div class="x-testimonial-badge">Repeat Customer</div>
            <div class="x-testimonial-content">
                <div class="x-testimonial-quote-icon">
                    <i class="fas fa-quote-left"></i>
                </div>
                <p class="x-testimonial-text">"Telur gabus kejunya enak banget, teksturnya pas dan rasanya
                    gurih. Cocok banget buat cemilan nonton Netflix. Udah repeat order 3 kali dan selalu
                    konsisten kualitasnya. Recommended!"</p>
                <div class="x-testimonial-product">
                    <span class="x-testimonial-product-icon">🧀</span>
                    <span class="x-testimonial-product-name">Telur Gabus Keju</span>
                </div>
            </div>
            <div class="x-testimonial-footer">
                <div class="x-testimonial-profile">
                    <div class="x-testimonial-avatar">
                        <img src="{{ asset('images/profiles/dummy_photo_3.jpg') }}" alt="Testimoni">
                    </div>
                    <div class="x-testimonial-info">
                        <h5 class="x-testimonial-name">Budi Santoso</h5>
                        <div class="x-testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="x-testimonial-meta">
                    <div class="x-testimonial-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Jakarta Selatan</span>
                    </div>
                    <div class="x-testimonial-time">
                        <i class="far fa-clock"></i>
                        <span>1 minggu yang lalu</span>
                    </div>
                </div>
            </div>
            <div class="x-testimonial-hover-effect"></div>
        </div>
    </div>

    <!-- Testimonial 3 -->
    <div class="col-md-4">
        <div class="x-testimonial-card">
            <div class="x-testimonial-badge">New Customer</div>
            <div class="x-testimonial-content">
                <div class="x-testimonial-quote-icon">
                    <i class="fas fa-quote-left"></i>
                </div>
                <p class="x-testimonial-text">"Cilok garingnya jadi cemilan favorit di kantor. Rasanya otentik
                    banget, berasa lagi jajan di Pangandaran. Porsinya juga pas dan harganya terjangkau. Pasti
                    bakal jadi langganan nih!"</p>
                <div class="x-testimonial-product">
                    <span class="x-testimonial-product-icon">🥟</span>
                    <span class="x-testimonial-product-name">Cilok Garing Original</span>
                </div>
            </div>
            <div class="x-testimonial-footer">
                <div class="x-testimonial-profile">
                    <div class="x-testimonial-avatar">
                        <img src="{{ asset('images/profiles/dummy_photo_5.jpg') }}" alt="Testimoni">
                    </div>
                    <div class="x-testimonial-info">
                        <h5 class="x-testimonial-name">Rizki Fadilah</h5>
                        <div class="x-testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                <div class="x-testimonial-meta">
                    <div class="x-testimonial-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Bandung, Jawa Barat</span>
                    </div>
                    <div class="x-testimonial-time">
                        <i class="far fa-clock"></i>
                        <span>3 hari yang lalu</span>
                    </div>
                </div>
            </div>
            <div class="x-testimonial-hover-effect"></div>
        </div>
    </div>
</div>

<div class="x-section-action mt-5">
    <x-guest.button href="#" type="outline" icon="arrow-right" theme="light" class="mt-4">
        Lihat Semua Review
    </x-guest.button>
</div>


<style>
    /* Testimonials Section */
    .x-testimonials-section {
        background-color: var(--x-bg-secondary);
        position: relative;
        overflow: hidden;
        z-index: 1;
    }

    /* Decorative Elements */
    .x-testimonials-particle {
        position: absolute;
        border-radius: 50%;
        filter: blur(40px);
        z-index: -1;
    }

    .x-testimonials-particle-1 {
        width: 500px;
        height: 500px;
        top: -250px;
        right: -250px;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.1) 0%, rgba(255, 215, 0, 0) 70%);
    }

    .x-testimonials-particle-2 {
        width: 400px;
        height: 400px;
        bottom: -200px;
        left: -200px;
        background: radial-gradient(circle, rgba(255, 45, 32, 0.1) 0%, rgba(255, 45, 32, 0) 70%);
    }

    .x-testimonials-pattern {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23FF2D20' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        z-index: -1;
    }

    /* Section Header Styles */
    .x-section-header {
        position: relative;
        margin-bottom: 3rem;
    }

    .x-section-badge {
        display: inline-flex;
        align-items: center;
        background: rgba(255, 45, 32, 0.08);
        padding: 8px 16px;
        border-radius: var(--x-button-radius);
        font-weight: 700;
        font-size: 0.9rem;
        margin-bottom: 1.2rem;
        color: var(--x-primary-red);
        gap: 8px;
    }

    .x-section-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
        letter-spacing: -0.03em;
        line-height: 1.2;
        color: var(--x-text-primary);
    }

    .x-title-highlight {
        position: relative;
        display: inline-block;
        z-index: 1;
    }

    .x-title-highlight::after {
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

    .x-section-subtitle {
        font-size: 1.1rem;
        color: var(--x-text-secondary);
        max-width: 600px;
        margin: 0 auto;
    }

    /* Testimonial Card Styles */
    .x-testimonial-card {
        background: white;
        border-radius: var(--x-card-radius);
        padding: 2rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
        transition: all 0.4s var(--x-transition-timing);
        height: 100%;
        display: flex;
        flex-direction: column;
        z-index: 1;
    }

    .x-testimonial-card:hover {
        transform: translateY(-15px);
        box-shadow: var(--x-card-shadow);
    }

    /* Badge */
    .x-testimonial-badge {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        background: var(--x-accent-gradient);
        color: white;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 20px;
        box-shadow: 0 3px 10px rgba(255, 138, 0, 0.3);
        animation: pulse 2.5s infinite;
    }

    /* Content Area */
    .x-testimonial-content {
        position: relative;
        margin-bottom: 1.5rem;
        flex: 1;
    }

    .x-testimonial-quote-icon {
        color: var(--x-accent-yellow);
        font-size: 1.5rem;
        margin-bottom: 1rem;
        opacity: 0.7;
    }

    .x-testimonial-text {
        font-size: 1rem;
        line-height: 1.6;
        color: var(--x-text-primary);
        margin-bottom: 1.5rem;
    }

    .x-testimonial-product {
        display: inline-flex;
        align-items: center;
        background: rgba(255, 45, 32, 0.05);
        padding: 8px 12px;
        border-radius: 12px;
        margin-top: 1rem;
        border: 1px dashed rgba(255, 45, 32, 0.2);
    }

    .x-testimonial-product-icon {
        font-size: 1.2rem;
        margin-right: 8px;
    }

    .x-testimonial-product-name {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--x-text-primary);
    }

    /* Footer Area */
    .x-testimonial-footer {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding-top: 1.5rem;
    }

    .x-testimonial-profile {
        display: flex;
        align-items: center;
    }

    .x-testimonial-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 1rem;
        border: 2px solid var(--x-accent-yellow);
        padding: 2px;
        background: white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .x-testimonial-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .x-testimonial-info {
        flex: 1;
    }

    .x-testimonial-name {
        font-weight: 700;
        font-size: 1.1rem;
        margin: 0 0 0.25rem;
        color: var(--x-text-primary);
    }

    .x-testimonial-rating {
        color: var(--x-accent-yellow);
        font-size: 0.9rem;
    }

    .x-testimonial-meta {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        font-size: 0.8rem;
        color: var(--x-text-secondary);
    }

    .x-testimonial-location,
    .x-testimonial-time {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* Hover Effect */
    .x-testimonial-hover-effect {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--x-primary-gradient);
        transform: scaleX(0);
        transition: transform 0.4s ease;
        transform-origin: left;
    }

    .x-testimonial-card:hover .x-testimonial-hover-effect {
        transform: scaleX(1);
    }

    /* Button Styles */
    .x-section-action {
        text-align: center;
        margin-top: 3rem;
    }

    .x-button {
        display: inline-flex;
        align-items: center;
        padding: 14px 28px;
        border-radius: var(--x-button-radius);
        font-weight: 700;
        font-size: 1rem;
        transition: all 0.3s var(--x-transition-timing);
        text-decoration: none;
        gap: 10px;
        position: relative;
        overflow: hidden;
    }

    .x-button-primary {
        background: var(--x-primary-gradient);
        color: white;
        box-shadow: var(--x-button-shadow);
    }

    .x-button-primary:hover {
        transform: translateY(-5px);
        box-shadow: var(--x-button-shadow-hover);
        color: white;
    }

    .x-button-icon {
        transition: transform 0.3s ease;
    }

    .x-button:hover .x-button-icon {
        transform: translateX(5px);
    }

    /* Social Proof Counter */
    .x-testimonial-counter {
        text-align: center;
        margin-top: 3rem;
        padding: 1.5rem;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        border-radius: var(--x-card-radius);
        border: 1px solid rgba(255, 45, 32, 0.1);
        display: inline-block;
        position: relative;
        left: 50%;
        transform: translateX(-50%);
    }

    .x-testimonial-counter-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--x-primary-red);
        line-height: 1.2;
        margin-bottom: 0.5rem;
    }

    .x-testimonial-counter-label {
        font-size: 1rem;
        font-weight: 600;
        color: var(--x-text-secondary);
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

    /* Responsive Styles */
    @media (max-width: 991.98px) {
        .x-section-title {
            font-size: 2rem;
        }

        .x-testimonial-card {
            padding: 1.75rem;
        }
    }

    @media (max-width: 767.98px) {
        .x-section-title {
            font-size: 1.75rem;
        }

        .x-section-subtitle {
            font-size: 1rem;
        }

        .x-testimonial-counter-value {
            font-size: 2rem;
        }
    }

    @media (max-width: 575.98px) {
        .x-section-badge {
            font-size: 0.8rem;
            padding: 6px 12px;
        }

        .x-section-title {
            font-size: 1.5rem;
        }

        .x-testimonial-card {
            padding: 1.5rem;
        }

        .x-testimonial-badge {
            top: 1rem;
            right: 1rem;
            font-size: 0.7rem;
            padding: 4px 8px;
        }

        .x-testimonial-avatar {
            width: 40px;
            height: 40px;
        }

        .x-testimonial-name {
            font-size: 1rem;
        }

        .x-testimonial-meta {
            flex-direction: column;
            gap: 8px;
        }
    }
</style>
