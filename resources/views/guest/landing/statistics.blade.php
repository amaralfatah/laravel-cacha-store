<section class="x-statistics-section" id="stats">
    <!-- Decorative Elements -->
    <div class="x-statistics-particle x-statistics-particle-1"></div>
    <div class="x-statistics-particle x-statistics-particle-2"></div>
    <div class="x-statistics-pattern"></div>

    <div class="container py-5">
        <div class="x-statistics-header text-center mb-5">

            <div class="row g-4">
                <div class="col-md-3 col-6">
                    <div class="x-statistics-counter-item">
                        <div class="x-statistics-counter-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="x-statistics-counter-value" data-value="{{ $statistics['product_variants'] }}">
                            {{ $statistics['product_variants'] }}+</div>
                        <div class="x-statistics-counter-label">Varian Produk</div>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="x-statistics-counter-item">
                        <div class="x-statistics-counter-icon">
                            <i class="fas fa-smile"></i>
                        </div>
                        <div class="x-statistics-counter-value" data-value="{{ $statistics['satisfied_customers'] }}">
                            {{ $statistics['satisfied_customers'] }}K+</div>
                        <div class="x-statistics-counter-label">Pelanggan Puas</div>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="x-statistics-counter-item">
                        <div class="x-statistics-counter-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="x-statistics-counter-value" data-value="{{ $statistics['total_cities'] }}">
                            {{ $statistics['total_cities'] }}+</div>
                        <div class="x-statistics-counter-label">Kota Terjangkau</div>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="x-statistics-counter-item">
                        <div class="x-statistics-counter-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="x-statistics-counter-value" data-value="{{ $statistics['marketplace_rating'] }}">
                            {{ $statistics['marketplace_rating'] }}</div>
                        <div class="x-statistics-counter-label">Rating Marketplace</div>
                    </div>
                </div>
            </div>
        </div>
</section>

<!-- CSS STYLES FOR STATISTICS SECTION -->
<style>
    /* Base Section Styles */
    .x-statistics-section {
        position: relative;
        overflow: hidden;
        /* background: var(--x-primary-gradient); */
        color: white;
        padding: var(--x-padding-lg, 3rem) 0;
    }

    /* Decorative Elements */
    .x-statistics-particle {
        position: absolute;
        border-radius: 50%;
        filter: blur(40px);
        z-index: 0;
    }

    .x-statistics-particle-1 {
        width: 500px;
        height: 500px;
        top: -250px;
        right: -250px;
        /* background: radial-gradient(circle, rgba(255, 215, 0, 0.3) 0%, rgba(255, 215, 0, 0) 70%); */
    }

    .x-statistics-particle-2 {
        width: 400px;
        height: 400px;
        bottom: -200px;
        left: -200px;
        /* background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0) 70%); */
    }

    .x-statistics-pattern {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23FFFFFF' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        z-index: 0;
    }

    /* Section Header Styles */
    .x-statistics-header {
        position: relative;
        margin-bottom: 3rem;
        z-index: 1;
    }

    .x-statistics-badge {
        display: inline-flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 40px;
        font-weight: 700;
        font-size: 0.9rem;
        margin-bottom: 1.2rem;
        color: white;
        gap: 8px;
    }

    .x-statistics-title {
        font-size: 2.5rem;
        font-weight: var(--x-title-weight, 800);
        margin-bottom: 1rem;
        letter-spacing: var(--x-letter-spacing, -0.03em);
        line-height: var(--x-title-line-height, 1.2);
        color: white;
    }

    .x-statistics-title-highlight {
        position: relative;
        display: inline-block;
        z-index: 1;
    }

    .x-statistics-title-highlight::after {
        content: '';
        position: absolute;
        bottom: 5px;
        left: 0;
        width: 100%;
        height: 8px;
        background-color: rgba(255, 255, 255, 0.3);
        border-radius: 4px;
        z-index: -1;
        transform: rotate(-1deg);
    }

    .x-statistics-subtitle {
        font-size: 1.1rem;
        color: rgba(255, 255, 255, 0.9);
        max-width: 600px;
        margin: 0 auto;
    }

    /* Counter Item Styles */
    .x-statistics-counter-item {
        text-align: center;
        position: relative;
        z-index: 1;
        background: rgba(255, 255, 255, 0.1);
        border-radius: var(--x-card-radius, 1.75rem);
        padding: 2rem 1rem;
        backdrop-filter: blur(5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s var(--x-transition-timing, cubic-bezier(0.34, 1.56, 0.64, 1)),
            box-shadow 0.3s ease;
    }

    .x-statistics-counter-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .x-statistics-counter-icon {
        margin-bottom: 1.2rem;
        font-size: 2.5rem;
        height: 60px;
        width: 60px;
        line-height: 60px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        color: white;
    }

    .x-statistics-counter-value {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        background: var(--x-accent-yellow);
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        display: inline-block;
    }

    .x-statistics-counter-label {
        font-size: 1.1rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.9);
    }

    /* Responsive Styles */
    @media (max-width: 991.98px) {
        .x-statistics-title {
            font-size: 2rem;
        }

        .x-statistics-counter-item {
            padding: 1.5rem 1rem;
        }

        .x-statistics-counter-value {
            font-size: 2.5rem;
        }
    }

    @media (max-width: 767.98px) {
        .x-statistics-title {
            font-size: 1.75rem;
        }

        .x-statistics-subtitle {
            font-size: 1rem;
        }

        .x-statistics-counter-icon {
            font-size: 2rem;
            height: 50px;
            width: 50px;
            line-height: 50px;
        }

        .x-statistics-counter-value {
            font-size: 2rem;
        }

        .x-statistics-counter-label {
            font-size: 0.9rem;
        }
    }

    @media (max-width: 575.98px) {
        .x-statistics-badge {
            font-size: 0.8rem;
            padding: 6px 12px;
        }

        .x-statistics-title {
            font-size: 1.5rem;
        }

        .x-statistics-counter-value {
            font-size: 1.5rem;
        }

        .x-statistics-counter-label {
            font-size: 0.8rem;
        }
    }
</style>

<!-- Counter Animation Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const counters = document.querySelectorAll('.x-statistics-counter-value');

        const options = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    const target = parseFloat(counter.getAttribute('data-value'));
                    let current = 0;
                    const increment = target / 50; // Adjust for speed
                    const hasK = counter.textContent.includes('K');
                    const hasPlusSign = counter.textContent.includes('+');

                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            counter.textContent = target % 1 !== 0 ? target.toFixed(1) :
                                target;
                            if (hasK) counter.textContent += 'K';
                            if (hasPlusSign) counter.textContent += '+';
                            clearInterval(timer);
                        } else {
                            counter.textContent = Math.ceil(current);
                            if (hasK) counter.textContent += 'K';
                            if (hasPlusSign) counter.textContent += '+';
                        }
                    }, 30);

                    observer.unobserve(counter);
                }
            });
        }, options);

        counters.forEach(counter => {
            observer.observe(counter);
        });
    });
</script>
