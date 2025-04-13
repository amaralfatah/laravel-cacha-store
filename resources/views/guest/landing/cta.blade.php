<!-- Snack Indonesia CTA Section -->
<section class="x-cta-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <!-- Headline -->
                <h2 class="display-5 fw-bold text-white mb-4" data-animation="fade-up">
                    Dapatkan Diskon 25% untuk Pembelian Pertama!
                </h2>

                <!-- Promo text -->
                <p class="lead mb-4 text-white" data-animation="fade-up">
                    Gunakan kode promo
                    <span class="x-cta-promo-code bg-white px-3 py-1 rounded-pill fw-bold mx-2 text-danger">
                        SNACKATTACK
                    </span>
                    saat checkout
                </p>

                <!-- CTA Buttons -->
                <div class="d-flex flex-wrap justify-content-center gap-3 mb-4" data-animation="fade-up">
                    <!-- Main CTA Button -->
                    <a href="https://shopee.co.id/plakatisme"
                        class="btn btn-light btn-lg fw-bold rounded-pill px-4 py-3 shadow-sm text-danger position-relative overflow-hidden">
                        <i class="fas fa-shopping-cart me-2"></i>Cobain Sekarang!
                    </a>

                    <!-- Secondary CTA Button -->
                    <a href="https://wa.me/6281323061827"
                        class="btn btn-outline-light btn-lg fw-bold rounded-pill px-4 py-3">
                        <i class="fab fa-whatsapp me-2"></i>Pesan via WhatsApp
                    </a>
                </div>

                <!-- Live counters -->
                <div class="d-flex flex-wrap justify-content-center gap-3 mb-4">
                    <div class="bg-white px-3 py-2 rounded-pill text-danger shadow-sm">
                        <i class="fas fa-eye me-2"></i><span id="visitor-counter">120+</span> orang lagi lihat
                    </div>
                    <div class="bg-white px-3 py-2 rounded-pill text-danger shadow-sm">
                        <i class="fas fa-clock me-2"></i>Promo berakhir dalam <span id="countdown-timer">02:45:33</span>
                    </div>
                </div>

                {{-- MASIH DUMMY --}}
                <!-- Marketplace links -->
                <div class="mt-5" data-animation="fade-up">
                    <p class="text-white mb-3">Atau belanja melalui marketplace favorit kamu:</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#" class="bg-white p-2 rounded-pill shadow-sm">
                            <img src="{{ asset('images/logo-snack-circle.png') }}" alt="Tokopedia" width="100"
                                height="30" class="img-fluid">
                        </a>
                        <a href="#" class="bg-white p-2 rounded-pill shadow-sm">
                            <img src="{{ asset('images/logo-snack-circle.png') }}" alt="Shopee" width="100"
                                height="30" class="img-fluid">
                        </a>
                        <a href="#" class="bg-white p-2 rounded-pill shadow-sm">
                            <img src="{{ asset('images/logo-snack-circle.png') }}" alt="Bukalapak" width="100"
                                height="30" class="img-fluid">
                        </a>
                        <a href="#" class="bg-white p-2 rounded-pill shadow-sm">
                            <img src="{{ asset('images/logo-snack-circle.png') }}" alt="Lazada" width="100"
                                height="30" class="img-fluid">
                        </a>
                    </div>
                </div>

                <!-- Social Sharing -->
                <div class="mt-4" data-animation="fade-up">
                    <p class="text-white-50 small mb-2">Bagikan ke teman kamu:</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="#" class="btn btn-sm btn-light bg-opacity-25 rounded-circle">
                            <i class="fab fa-instagram text-danger"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-light bg-opacity-25 rounded-circle">
                            <i class="fab fa-tiktok text-danger"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-light bg-opacity-25 rounded-circle">
                            <i class="fab fa-twitter text-danger"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Minimal custom CSS -->
<style>
    /* For animation effects */
    [data-animation="fade-up"] {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.5s ease, transform 0.5s ease;
    }

    .reveal {
        opacity: 1;
        transform: translateY(0);
    }

    /* For promo code shimmer effect - only kept essential custom styling */
    .x-cta-promo-code {
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .x-cta-promo-code:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 6px 20px rgba(255, 45, 32, 0.3);
    }
</style>

<!-- Simplified JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Scroll reveal animation
        const revealElements = document.querySelectorAll('[data-animation="fade-up"]');

        function checkReveal() {
            revealElements.forEach(element => {
                const rect = element.getBoundingClientRect();
                if (rect.top <= window.innerHeight * 0.8) {
                    element.classList.add('reveal');
                }
            });
        }

        // Initial check
        checkReveal();

        // Check on scroll
        window.addEventListener('scroll', checkReveal);

        // Countdown timer
        const timerElement = document.getElementById('countdown-timer');
        if (timerElement) {
            let hours = 2;
            let minutes = 45;
            let seconds = 33;

            setInterval(() => {
                seconds--;
                if (seconds < 0) {
                    seconds = 59;
                    minutes--;
                    if (minutes < 0) {
                        minutes = 59;
                        hours--;
                        if (hours < 0) {
                            hours = 0;
                            minutes = 0;
                            seconds = 0;
                        }
                    }
                }

                timerElement.textContent =
                    `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }, 1000);
        }

        // Counter animation
        const counterElement = document.getElementById('visitor-counter');
        let currentCount = 120;

        if (counterElement) {
            setInterval(() => {
                // Randomly increase counter
                if (Math.random() > 0.7) {
                    currentCount += Math.floor(Math.random() * 3) + 1;
                    counterElement.textContent = `${currentCount}+`;
                }
            }, 5000);
        }
    });
</script>
