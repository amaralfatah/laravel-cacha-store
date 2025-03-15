{{-- guest/landing/gallery.blade.php --}}

<div class="row g-4">
    @foreach ($gallery as $image)
        <div class="col-md-4 col-6">
            <div class="x-gallery-item">
                @php
                    $path = is_string($image['image_path'] ?? null) ? $image['image_path'] : $image->image_path;
                    $prefix = str_starts_with($path, 'images/') ? '' : 'storage/';
                @endphp
                <img src="{{ asset($prefix . $path) }}"
                    alt="{{ is_string($image['image_path'] ?? null) ? $image['product']['name'] : $image->product->name }}"
                    class="x-gallery-img">

                <div class="x-gallery-overlay">
                    <div class="x-gallery-content">
                        <a class="x-gallery-title"
                            href="{{ route('guest.show', is_string($image['image_path'] ?? null) ? $image['product']['slug'] : $image->product->slug) }}">
                            {{ is_string($image['image_path'] ?? null) ? $image['product']['name'] : $image->product->name }}
                        </a>
                        <p class="x-gallery-desc">
                            {{ is_string($image['image_path'] ?? null)
                                ? $image['product']['short_description']
                                : Str::limit($image->product->short_description, 50) }}
                        </p>
                        <div class="x-gallery-button">
                            <i class="fas fa-eye"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<style>
    /* Gallery Section Styling */
    .x-gallery-item {
        position: relative;
        border-radius: var(--x-card-radius);
        overflow: hidden;
        box-shadow: var(--x-card-shadow);
        cursor: pointer;
        height: 100%;
        transform-origin: center;
        transition: transform 0.4s var(--x-transition-timing),
            box-shadow 0.4s ease;
        will-change: transform, box-shadow;
    }

    .x-gallery-item:hover {
        transform: translateY(-12px) scale(1.03);
        box-shadow: var(--x-card-shadow-hover);
    }

    .x-gallery-img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        transition: transform 0.6s var(--x-transition-timing),
            filter 0.6s ease;
        will-change: transform, filter;
        transform-origin: center;
    }

    .x-gallery-item:hover .x-gallery-img {
        transform: scale(1.15) rotate(2deg);
        filter: var(--x-hover-filter);
    }

    .x-gallery-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to top,
                rgba(0, 0, 0, 0.8) 0%,
                rgba(0, 0, 0, 0.5) 40%,
                rgba(0, 0, 0, 0) 100%);
        display: flex;
        align-items: flex-end;
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .x-gallery-item:hover .x-gallery-overlay {
        opacity: 1;
    }

    .x-gallery-content {
        padding: 1.5rem;
        width: 100%;
        transform: translateY(20px);
        transition: transform 0.5s var(--x-transition-timing);
    }

    .x-gallery-item:hover .x-gallery-content {
        transform: translateY(0);
    }

    .x-gallery-title {
        display: block;
        color: white;
        font-size: 1.2rem;
        font-weight: var(--x-title-weight);
        margin-bottom: 0.5rem;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        transition: color 0.3s ease;
    }

    .x-gallery-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: var(--x-accent-yellow);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s var(--x-transition-timing);
    }

    .x-gallery-title:hover {
        color: var(--x-accent-yellow);
    }

    .x-gallery-title:hover::after {
        transform: scaleX(1);
    }

    .x-gallery-desc {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.9rem;
        margin-bottom: 1rem;
        opacity: 0;
        transform: translateY(10px);
        transition: opacity 0.3s ease 0.1s,
            transform 0.3s var(--x-transition-timing) 0.1s;
    }

    .x-gallery-item:hover .x-gallery-desc {
        opacity: 1;
        transform: translateY(0);
    }

    .x-gallery-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: var(--x-accent-yellow);
        color: var(--x-text-primary);
        border-radius: 50%;
        opacity: 0;
        transform: scale(0.5);
        transition: transform 0.4s var(--x-transition-timing) 0.2s,
            opacity 0.4s ease 0.2s,
            background 0.3s ease;
    }

    .x-gallery-item:hover .x-gallery-button {
        opacity: 1;
        transform: scale(1);
    }

    .x-gallery-button:hover {
        background: white;
        color: var(--x-primary-red);
        transform: scale(1.1) !important;
    }

    /* Social Sharing Feature */
    .x-gallery-share {
        position: absolute;
        top: 15px;
        right: 15px;
        display: flex;
        gap: 0.5rem;
        z-index: 10;
        opacity: 0;
        transform: translateY(-10px);
        transition: opacity 0.3s ease,
            transform 0.3s var(--x-transition-timing);
    }

    .x-gallery-item:hover .x-gallery-share {
        opacity: 1;
        transform: translateY(0);
    }

    .x-gallery-share-button {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        color: var(--x-primary-red);
        border-radius: 50%;
        font-size: 0.8rem;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s var(--x-transition-timing),
            background 0.3s ease;
    }

    .x-gallery-share-button:hover {
        transform: scale(1.15);
    }

    .x-gallery-share-button.instagram:hover {
        background: #C13584;
        color: white;
    }

    .x-gallery-share-button.whatsapp:hover {
        background: #25D366;
        color: white;
    }

    /* Zoom effect cursor */
    .x-gallery-item:hover {
        cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%23FF2D20" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>'), auto;
    }

    /* Staggered animation for gallery items */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #gallery .col-md-4 {
        opacity: 0;
        animation: fadeInUp 0.6s var(--x-transition-timing) forwards;
    }

    #gallery .col-md-4:nth-child(1) {
        animation-delay: 0.1s;
    }

    #gallery .col-md-4:nth-child(2) {
        animation-delay: 0.2s;
    }

    #gallery .col-md-4:nth-child(3) {
        animation-delay: 0.3s;
    }

    #gallery .col-md-4:nth-child(4) {
        animation-delay: 0.4s;
    }

    #gallery .col-md-4:nth-child(5) {
        animation-delay: 0.5s;
    }

    #gallery .col-md-4:nth-child(6) {
        animation-delay: 0.6s;
    }

    #gallery .col-md-4:nth-child(7) {
        animation-delay: 0.7s;
    }

    #gallery .col-md-4:nth-child(8) {
        animation-delay: 0.8s;
    }

    #gallery .col-md-4:nth-child(9) {
        animation-delay: 0.9s;
    }

    /* Hover effect for mobile/touch devices */
    @media (hover: none) {
        .x-gallery-overlay {
            opacity: 1;
            background: linear-gradient(to top,
                    rgba(0, 0, 0, 0.8) 0%,
                    rgba(0, 0, 0, 0.4) 50%,
                    rgba(0, 0, 0, 0) 100%);
        }

        .x-gallery-content {
            transform: translateY(0);
        }

        .x-gallery-button {
            opacity: 1;
            transform: scale(1);
        }

        .x-gallery-desc {
            opacity: 1;
            transform: translateY(0);
        }

        .x-gallery-share {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Larger image height for larger screens */
    @media (min-width: 992px) {
        .x-gallery-img {
            height: 300px;
        }
    }

    /* Mobile Optimization */
    @media (max-width: 767.98px) {
        .x-gallery-img {
            height: 200px;
        }

        .x-gallery-content {
            padding: 1rem;
        }

        .x-gallery-title {
            font-size: 1rem;
        }

        .x-gallery-desc {
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }

        .x-gallery-button {
            width: 32px;
            height: 32px;
        }
    }

    /* Landscape mobile view optimization */
    @media (max-width: 767.98px) and (orientation: landscape) {
        #gallery .row {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            padding-bottom: 1rem;
            margin-right: -15px;
            margin-left: -15px;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: var(--x-primary-red) transparent;
        }

        #gallery .col-6 {
            flex: 0 0 auto;
            width: 240px;
            padding-right: 0.5rem;
            padding-left: 0.5rem;
        }
    }

    /* Add lightbox-style image viewer functionality */
    .x-gallery-lightbox {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .x-gallery-lightbox.active {
        opacity: 1;
        visibility: visible;
    }

    .x-gallery-lightbox-content {
        position: relative;
        max-width: 90%;
        max-height: 80vh;
    }

    .x-gallery-lightbox-img {
        max-width: 100%;
        max-height: 80vh;
        border-radius: 10px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
    }

    .x-gallery-lightbox-close {
        position: absolute;
        top: -40px;
        right: 0;
        width: 36px;
        height: 36px;
        background: white;
        color: var(--x-primary-red);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s var(--x-transition-timing);
    }

    .x-gallery-lightbox-close:hover {
        transform: scale(1.1) rotate(90deg);
    }

    .x-gallery-lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        background: white;
        color: var(--x-primary-red);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s var(--x-transition-timing);
    }

    .x-gallery-lightbox-prev {
        left: -60px;
    }

    .x-gallery-lightbox-next {
        right: -60px;
    }

    .x-gallery-lightbox-nav:hover {
        transform: translateY(-50%) scale(1.1);
    }

    @media (max-width: 767.98px) {
        .x-gallery-lightbox-nav {
            width: 32px;
            height: 32px;
        }

        .x-gallery-lightbox-prev {
            left: -40px;
        }

        .x-gallery-lightbox-next {
            right: -40px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add share buttons to gallery items
        const galleryItems = document.querySelectorAll('.x-gallery-item');

        galleryItems.forEach(item => {
            // Get product name and image for sharing
            const imgSrc = item.querySelector('.x-gallery-img').src;
            const productName = item.querySelector('.x-gallery-title').textContent.trim();
            const productUrl = item.querySelector('.x-gallery-title').getAttribute('href');

            // Create share container if not exists
            if (!item.querySelector('.x-gallery-share')) {
                const shareContainer = document.createElement('div');
                shareContainer.className = 'x-gallery-share';

                // Instagram share button
                const instagramBtn = document.createElement('a');
                instagramBtn.className = 'x-gallery-share-button instagram';
                instagramBtn.innerHTML = '<i class="fab fa-instagram"></i>';
                instagramBtn.setAttribute('target', '_blank');
                instagramBtn.setAttribute('rel', 'noopener noreferrer');
                instagramBtn.href = `https://www.instagram.com/?url=${encodeURIComponent(productUrl)}`;
                instagramBtn.setAttribute('title', 'Share on Instagram');

                // WhatsApp share button
                const whatsappBtn = document.createElement('a');
                whatsappBtn.className = 'x-gallery-share-button whatsapp';
                whatsappBtn.innerHTML = '<i class="fab fa-whatsapp"></i>';
                whatsappBtn.setAttribute('target', '_blank');
                whatsappBtn.setAttribute('rel', 'noopener noreferrer');
                whatsappBtn.href =
                    `https://wa.me/?text=${encodeURIComponent(`Check out ${productName}: ${productUrl}`)}`;
                whatsappBtn.setAttribute('title', 'Share on WhatsApp');

                // Append buttons to container
                shareContainer.appendChild(instagramBtn);
                shareContainer.appendChild(whatsappBtn);

                // Add to gallery item
                item.appendChild(shareContainer);
            }

            // Add lightbox functionality
            item.addEventListener('click', function(e) {
                if (e.target.classList.contains('x-gallery-share-button') ||
                    e.target.parentElement.classList.contains('x-gallery-share-button') ||
                    e.target.classList.contains('x-gallery-title')) {
                    return; // Don't trigger lightbox on share buttons or title link clicks
                }

                // Create lightbox if it doesn't exist
                let lightbox = document.querySelector('.x-gallery-lightbox');
                if (!lightbox) {
                    lightbox = document.createElement('div');
                    lightbox.className = 'x-gallery-lightbox';

                    const lightboxContent = document.createElement('div');
                    lightboxContent.className = 'x-gallery-lightbox-content';

                    const lightboxImg = document.createElement('img');
                    lightboxImg.className = 'x-gallery-lightbox-img';

                    const closeBtn = document.createElement('div');
                    closeBtn.className = 'x-gallery-lightbox-close';
                    closeBtn.innerHTML = '<i class="fas fa-times"></i>';

                    const prevBtn = document.createElement('div');
                    prevBtn.className = 'x-gallery-lightbox-nav x-gallery-lightbox-prev';
                    prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';

                    const nextBtn = document.createElement('div');
                    nextBtn.className = 'x-gallery-lightbox-nav x-gallery-lightbox-next';
                    nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';

                    lightboxContent.appendChild(lightboxImg);
                    lightboxContent.appendChild(closeBtn);
                    lightboxContent.appendChild(prevBtn);
                    lightboxContent.appendChild(nextBtn);

                    lightbox.appendChild(lightboxContent);
                    document.body.appendChild(lightbox);

                    // Close lightbox on click
                    lightbox.addEventListener('click', function(e) {
                        if (e.target.classList.contains('x-gallery-lightbox') ||
                            e.target.classList.contains('x-gallery-lightbox-close') ||
                            e.target.parentElement.classList.contains(
                                'x-gallery-lightbox-close')) {
                            lightbox.classList.remove('active');
                        }
                    });

                    // Navigation
                    let currentIndex = 0;
                    const galleryImages = Array.from(document.querySelectorAll(
                        '.x-gallery-img'));

                    prevBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        currentIndex = (currentIndex - 1 + galleryImages.length) %
                            galleryImages.length;
                        lightboxImg.src = galleryImages[currentIndex].src;
                    });

                    nextBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        currentIndex = (currentIndex + 1) % galleryImages.length;
                        lightboxImg.src = galleryImages[currentIndex].src;
                    });
                }

                // Update lightbox content
                const lightboxImg = lightbox.querySelector('.x-gallery-lightbox-img');
                lightboxImg.src = imgSrc;

                // Set current index for navigation
                const galleryImages = Array.from(document.querySelectorAll('.x-gallery-img'));
                const currentIndex = galleryImages.findIndex(img => img.src === imgSrc);
                lightbox.dataset.currentIndex = currentIndex;

                // Show lightbox
                lightbox.classList.add('active');
            });
        });

        // Animation on scroll
        const animateOnScroll = () => {
            const galleryItems = document.querySelectorAll('#gallery .col-md-4');

            galleryItems.forEach(item => {
                const itemTop = item.getBoundingClientRect().top;
                const windowHeight = window.innerHeight;

                if (itemTop < windowHeight * 0.9) {
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }
            });
        };

        window.addEventListener('scroll', animateOnScroll);
        animateOnScroll(); // Run once on page load
    });
</script>
