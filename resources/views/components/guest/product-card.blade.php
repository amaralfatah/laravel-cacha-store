<!-- resources/views/components/product-card.blade.php -->
@props(['product', 'showBadges' => true, 'buttonSize' => 'normal', 'showDiscount' => true])

<div class="x-product-card-wrapper">
    <div @class([
        'x-product-card',
        'x-product-card--small' => $buttonSize === 'small',
    ])>
        <div class="x-product-card__image-container">
            <a href="{{ route('guest.show', $product->slug) }}" class="x-product-card__image-link">
                <img src="{{ $product->productImages->where('is_primary', true)->first()
                    ? asset('storage/' . $product->productImages->where('is_primary', true)->first()->image_path)
                    : asset('images/placeholder.png') }}"
                    alt="{{ $product->name }}" class="x-product-card__image">
            </a>

            <div class="x-product-card__badges">
                @if ($showDiscount && $product->discount)
                    <span @class([
                        'x-product-card__badge',
                        'x-product-card__badge--discount',
                        'x-product-card__badge--small' => $buttonSize === 'small',
                    ])>
                        -{{ $product->discount->value }}{{ $product->discount->type === 'percentage' ? '%' : 'Rp' }}
                    </span>
                @endif

                @if ($showBadges)
                    @php
                        $defaultUnit = $product->productUnits->where('is_default', true)->first();
                        $stock = $defaultUnit ? $defaultUnit->stock : 0;
                        $createdDate = $product->created_at;
                        $isNew = $createdDate && $createdDate->diffInDays(now()) <= 30;
                    @endphp

                    @if ($product->total_sold >= 100)
                        <span @class([
                            'x-product-card__badge',
                            'x-product-card__badge--bestseller',
                            'x-product-card__badge--small' => $buttonSize === 'small',
                        ])>
                            <i class="fas fa-fire-alt x-product-card__badge-icon"></i>
                            Terjual {{ $product->total_sold }}+
                        </span>
                    @elseif($stock <= 10 && $stock > 0)
                        <span @class([
                            'x-product-card__badge',
                            'x-product-card__badge--limited',
                            'x-product-card__badge--small' => $buttonSize === 'small',
                        ])>
                            <i class="fas fa-exclamation-circle x-product-card__badge-icon"></i>
                            Stok Tersisa {{ $stock }}
                        </span>
                    @elseif($isNew)
                        <span @class([
                            'x-product-card__badge',
                            'x-product-card__badge--new',
                            'x-product-card__badge--small' => $buttonSize === 'small',
                        ])>
                            <i class="fas fa-star x-product-card__badge-icon"></i>
                            Produk Baru
                        </span>
                    @endif
                @endif
            </div>
        </div>

        <div class="x-product-card__body">
            <div class="x-product-card__header">
                <span class="x-product-card__category">
                    {{ $product->category->name }}
                </span>
                <x-guest.rating-stars :total="$product->total_sold ?? 0" class="x-product-card__rating" />
            </div>

            <h3 class="x-product-card__title">{{ $product->name }}</h3>

            <p class="x-product-card__description">
                {{ Str::limit($product->short_description, 60) }}
            </p>

            <div class="x-product-card__footer">
                <x-guest.product-price :product="$product" class="x-product-card__price" />

                <a href="{{ $product->url }}" @class([
                    'x-product-card__btn',
                    'x-product-card__btn--small' => $buttonSize === 'small',
                ])
                    aria-label="{{ $buttonSize === 'small' ? 'Tambahkan ke keranjang' : 'Beli produk' }}">
                    <i class="fas fa-cart-plus x-product-card__btn-icon"></i>
                    @if ($buttonSize === 'normal')
                        <span class="x-product-card__btn-text">Beli</span>
                    @endif
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        /* Main colors - Updated to match section-wrapper colors */
        --x-product-card-red-primary: #E83A30;
        --x-product-card-red-secondary: #FF5349;
        --x-product-card-red-dark: #C80000;
        --x-product-card-yellow-accent: #FFD54F;
        --x-product-card-orange-accent: #FF9800;
        --x-product-card-green-accent: #4CAF50;
        --x-product-card-blue-accent: #2196F3;
        --x-product-card-purple-accent: #9C27B0;
        --x-product-card-bg-primary: #FFFFFF;
        --x-product-card-bg-secondary: #FFF5F2;
        --x-product-card-text-primary: #2D3748;
        --x-product-card-text-secondary: #4A5568;

        /* Measurements */
        --x-product-card-border-radius: 1.5rem;
        --x-product-card-btn-border-radius: 30px;
        --x-product-card-badge-border-radius: 30px;

        /* Shadows */
        --x-product-card-shadow: 0 10px 20px rgba(232, 58, 48, 0.15);
        --x-product-card-btn-shadow: 0 4px 15px rgba(232, 58, 48, 0.3);
        --x-product-card-btn-shadow-hover: 0 8px 25px rgba(232, 58, 48, 0.4);

        /* Animations */
        --x-product-card-transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);

        /* Grid 3-column config */
        --x-product-card-title-size: 1.35rem;
        --x-product-card-image-height: 220px;
        --x-product-card-padding: 1.5rem;
        --x-product-card-badge-padding: 6px 12px;
        --x-product-card-badge-font-size: 0.8rem;

        /* Grid 4-column config */
        --x-product-card-title-size-small: 1.15rem;
        --x-product-card-image-height-small: 180px;
        --x-product-card-padding-small: 1.25rem;
        --x-product-card-badge-padding-small: 5px 10px;
        --x-product-card-badge-font-size-small: 0.7rem;
    }

    .x-product-card-wrapper {
        display: block;
        position: relative;
        width: 100%;
        height: 100%;
        padding: 0.5rem;
    }

    .x-product-card {
        position: relative;
        display: flex;
        flex-direction: column;
        height: 100%;
        background-color: var(--x-product-card-bg-primary);
        border-radius: var(--x-product-card-border-radius);
        box-shadow: var(--x-product-card-shadow);
        overflow: hidden;
        transition: var(--x-product-card-transition);
    }

    .x-product-card:hover {
        transform: translateY(-8px) scale(1.01);
        box-shadow: 0 15px 30px rgba(232, 58, 48, 0.2);
    }

    /* Image container */
    .x-product-card__image-container {
        position: relative;
        width: 100%;
        height: var(--x-product-card-image-height);
        overflow: hidden;
    }

    .x-product-card--small .x-product-card__image-container {
        height: var(--x-product-card-image-height-small);
    }

    .x-product-card__image-link {
        display: block;
        width: 100%;
        height: 100%;
        overflow: hidden;
        position: relative;
    }

    .x-product-card__image-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(232, 58, 48, 0) 0%, rgba(232, 58, 48, 0.2) 100%);
        opacity: 0;
        z-index: 1;
        transition: opacity 0.4s ease;
    }

    .x-product-card:hover .x-product-card__image-link::before {
        opacity: 1;
    }

    .x-product-card__image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: var(--x-product-card-transition);
    }

    .x-product-card:hover .x-product-card__image {
        transform: scale(1.08) rotate(1deg);
        filter: brightness(1.05) contrast(1.05);
    }

    /* Badges */
    .x-product-card__badges {
        position: absolute;
        top: 1rem;
        left: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        z-index: 1;
    }

    .x-product-card__badge {
        display: inline-flex;
        align-items: center;
        padding: var(--x-product-card-badge-padding);
        font-size: var(--x-product-card-badge-font-size);
        font-weight: 700;
        color: white;
        border-radius: var(--x-product-card-badge-border-radius);
        animation: badgePulse 2s infinite;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    .x-product-card__badge--small {
        padding: var(--x-product-card-badge-padding-small);
        font-size: var(--x-product-card-badge-font-size-small);
    }

    .x-product-card__badge--discount {
        background: linear-gradient(135deg, var(--x-product-card-red-primary) 0%, var(--x-product-card-red-secondary) 100%);
    }

    .x-product-card__badge--bestseller {
        background: linear-gradient(135deg, var(--x-product-card-orange-accent) 0%, var(--x-product-card-red-primary) 100%);
    }

    .x-product-card__badge--limited {
        background: linear-gradient(135deg, var(--x-product-card-orange-accent) 0%, var(--x-product-card-yellow-accent) 100%);
        color: #2D3748;
    }

    .x-product-card__badge--new {
        background: linear-gradient(135deg, var(--x-product-card-green-accent) 0%, #30B94D 100%);
    }

    .x-product-card__badge-icon {
        margin-right: 0.3rem;
    }

    /* Card body */
    .x-product-card__body {
        display: flex;
        flex-direction: column;
        padding: var(--x-product-card-padding);
        flex: 1;
    }

    .x-product-card--small .x-product-card__body {
        padding: var(--x-product-card-padding-small);
    }

    /* Card header */
    .x-product-card__header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .x-product-card__category {
        display: inline-block;
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--x-product-card-red-primary);
        background-color: var(--x-product-card-bg-secondary);
        border-radius: 30px;
        position: relative;
        overflow: hidden;
    }

    .x-product-card__category::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0) 100%);
        animation: shimmer 2s infinite;
    }

    /* Card title */
    .x-product-card__title {
        font-size: var(--x-product-card-title-size);
        font-weight: 700;
        color: var(--x-product-card-text-primary);
        margin-bottom: 0.75rem;
        position: relative;
        display: inline-block;
        transition: color 0.3s ease;
    }

    .x-product-card--small .x-product-card__title {
        font-size: var(--x-product-card-title-size-small);
    }

    .x-product-card__title::after {
        content: '';
        position: absolute;
        bottom: -3px;
        right: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--x-product-card-orange-accent), var(--x-product-card-red-primary));
        transition: width 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .x-product-card:hover .x-product-card__title {
        color: var(--x-product-card-red-primary);
    }

    .x-product-card:hover .x-product-card__title::after {
        width: 100%;
        left: 0;
        right: auto;
    }

    /* Card description */
    .x-product-card__description {
        font-size: 0.9rem;
        color: var(--x-product-card-text-secondary);
        margin-bottom: 1.25rem;
        flex-grow: 1;
    }

    /* Card footer */
    .x-product-card__footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
    }

    /* Price component styling */
    .x-product-card__price {
        font-weight: 700;
    }

    /* Button styling */
    .x-product-card__btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, var(--x-product-card-red-primary) 0%, var(--x-product-card-red-secondary) 100%);
        color: white;
        font-weight: 700;
        font-size: 0.95rem;
        border-radius: var(--x-product-card-btn-border-radius);
        transition: var(--x-product-card-transition);
        box-shadow: var(--x-product-card-btn-shadow);
        text-decoration: none;
        position: relative;
        overflow: hidden;
    }

    .x-product-card__btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0) 100%);
        transition: all 0.6s ease;
    }

    .x-product-card__btn--small {
        padding: 0.6rem 0.75rem;
        min-width: unset;
    }

    .x-product-card__btn:hover {
        background: linear-gradient(135deg, var(--x-product-card-red-dark) 0%, var(--x-product-card-red-primary) 100%);
        box-shadow: var(--x-product-card-btn-shadow-hover);
        color: white;
        transform: translateY(-3px);
    }

    .x-product-card__btn:hover::before {
        left: 100%;
    }

    .x-product-card__btn-icon {
        font-size: 1rem;
        transition: transform 0.3s ease;
    }

    .x-product-card__btn:hover .x-product-card__btn-icon {
        transform: translateX(4px);
    }

    .x-product-card__btn-text {
        margin-left: 0.5rem;
    }

    /* Animations */
    @keyframes badgePulse {
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
            left: 100%;
        }
    }

    /* Responsive Breakpoints */
    @media (max-width: 767.98px) {
        .x-product-card__image-container {
            height: 200px;
        }

        .x-product-card__body {
            padding: 1.25rem;
        }

        .x-product-card__title {
            font-size: 1.2rem;
        }
    }

    @media (min-width: 768px) and (max-width: 991.98px) {
        .x-product-card__image-container {
            height: 180px;
        }

        .x-product-card--small .x-product-card__image-container {
            height: 160px;
        }
    }

    @media (min-width: 992px) and (max-width: 1199.98px) {
        .x-product-card__title {
            font-size: 1.2rem;
        }

        .x-product-card--small .x-product-card__title {
            font-size: 1.1rem;
        }
    }
</style>
