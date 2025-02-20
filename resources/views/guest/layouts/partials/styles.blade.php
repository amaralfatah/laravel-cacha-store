
<style>
    :root {
        --primary: #FF3B30;
        --primary-light: #FFE8E7;
        --secondary: #FFC107;
        --dark: #212529;
        --light: #F8F9FA;
    }

    body {
        font-family: 'Outfit', sans-serif;
        overflow-x: hidden;
    }

    .text-primary-cacha {
        color: var(--primary);
    }

    .bg-primary-cacha {
        background-color: var(--primary);
    }

    .bg-primary-light {
        background-color: var(--primary-light);
    }

    .btn-primary-cacha {
        background-color: var(--primary);
        border-color: var(--primary);
        color: white;
        border-radius: 30px;
        padding: 10px 24px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-primary-cacha:hover {
        background-color: #E42D22;
        border-color: #E42D22;
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(255, 59, 48, 0.3);
    }

    .btn-outline-primary-cacha {
        background-color: transparent;
        border: 2px solid var(--primary);
        color: var(--primary);
        border-radius: 30px;
        padding: 9px 24px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-outline-primary-cacha:hover {
        background-color: var(--primary);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(255, 59, 48, 0.2);
    }

    .hero-section {
        background: linear-gradient(135deg, #FF3B30 0%, #FF6B61 100%);
        padding-top: 8rem;
        padding-bottom: 6rem;
        position: relative;
        overflow: hidden;
        color: white;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        top: -150px;
        right: -150px;
    }

    .hero-section::after {
        content: '';
        position: absolute;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        bottom: -100px;
        left: -100px;
    }

    .navbar {
        transition: all 0.4s;
        padding: 15px 0;
    }

    .navbar.scrolled {
        background-color: white !important;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        padding: 10px 0;
    }

    .navbar-brand {
        font-size: 1.5rem;
        font-weight: 800;
    }

    .nav-link {
        font-weight: 500;
        margin: 0 5px;
        position: relative;
        transition: all 0.3s;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        background-color: var(--primary);
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        transition: width 0.3s;
    }

    .nav-link:hover::after {
        width: 80%;
    }

    .category-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background-color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        transition: all 0.3s;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .category-card:hover .category-icon {
        transform: translateY(-10px);
        box-shadow: 0 10px 25px rgba(255, 59, 48, 0.15);
    }

    .product-card {
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s;
        height: 100%;
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .product-image {
        overflow: hidden;
        position: relative;
    }

    .product-image img {
        transition: transform 0.5s;
        height: 200px;
        object-fit: cover;
    }

    .product-card:hover .product-image img {
        transform: scale(1.05);
    }

    .product-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary);
    }

    .product-price-old {
        font-size: 0.9rem;
        font-weight: normal;
        text-decoration: line-through;
        color: #6c757d;
        margin-left: 5px;
    }

    .badge-product {
        position: absolute;
        top: 15px;
        right: 15px;
        border-radius: 50px;
        padding: 8px 15px;
        font-weight: 600;
        z-index: 2;
    }

    .rating-stars {
        color: var(--secondary);
    }

    .section-title {
        position: relative;
        margin-bottom: 50px;
    }

    .section-title h2 {
        font-weight: 700;
        margin-bottom: 15px;
        display: inline-block;
        position: relative;
    }

    .section-title h2:after {
        content: '';
        position: absolute;
        width: 50%;
        height: 4px;
        background-color: var(--primary);
        bottom: -10px;
        left: 25%;
        border-radius: 2px;
    }

    .testimonial-card {
        border-radius: 20px;
        padding: 30px;
        transition: all 0.3s;
        height: 100%;
    }

    .testimonial-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .testimonial-img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--primary);
    }

    .benefit-icon {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background-color: var(--primary-light);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        transition: all 0.3s;
    }

    .benefit-card:hover .benefit-icon {
        transform: rotateY(180deg);
        background-color: var(--primary);
        color: white;
    }

    .gallery-item {
        border-radius: 15px;
        overflow: hidden;
        position: relative;
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .gallery-img {
        transition: all 0.5s;
        height: 250px;
        object-fit: cover;
        width: 100%;
    }

    .gallery-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.7) 100%);
        display: flex;
        align-items: flex-end;
        padding: 20px;
        opacity: 0;
        transition: all 0.3s;
    }

    .gallery-item:hover .gallery-img {
        transform: scale(1.1);
    }

    .gallery-item:hover .gallery-overlay {
        opacity: 1;
    }

    .floating-shape {
        position: absolute;
        z-index: -1;
        opacity: 0.1;
    }

    .counter-item {
        text-align: center;
        padding: 20px;
        border-radius: 15px;
        background-color: white;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
    }

    .counter-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    }

    .counter-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 5px;
    }

    .counter-label {
        font-size: 1rem;
        color: var(--dark);
        font-weight: 500;
    }

    .logo-container {
        border-radius: 50%;
        background-color: white;
        padding: 5px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .cta-section {
        background: linear-gradient(135deg, #FF3B30 0%, #FF6B61 100%);
        padding: 70px 0;
        position: relative;
        overflow: hidden;
    }

    .cta-section::before {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        top: -150px;
        right: -150px;
    }

    .footer {
        padding: 80px 0 30px;
        background-color: #212529;
        color: rgba(255, 255, 255, 0.8);
    }

    .footer-title {
        color: white;
        font-weight: 700;
        margin-bottom: 25px;
        position: relative;
    }

    .footer-title:after {
        content: '';
        position: absolute;
        width: 50px;
        height: 3px;
        background-color: var(--primary);
        bottom: -10px;
        left: 0;
    }

    .footer-links li {
        margin-bottom: 15px;
    }

    .footer-links a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: all 0.3s;
    }

    .footer-links a:hover {
        color: var(--primary);
        padding-left: 5px;
    }

    .social-icon {
        width: 40px;
        height: 40px;
        background-color: #212529;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        transition: all 0.3s;
    }

    .social-icon:hover {
        background-color: var(--primary);
        transform: translateY(-5px);
    }

    .scroll-to-top {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background-color: var(--primary);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s;
        z-index: 99;
    }

    .scroll-to-top.active {
        opacity: 1;
        visibility: visible;
    }

    .scroll-to-top:hover {
        background-color: #E42D22;
        transform: translateY(-5px);
    }

    @media (max-width: 991.98px) {
        .navbar-collapse {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .hero-section {
            padding-top: 7rem;
            padding-bottom: 4rem;
            text-align: center;
        }

        .hero-image {
            margin-top: 40px;
        }

        .section-title {
            margin-bottom: 30px;
        }
    }
</style>
