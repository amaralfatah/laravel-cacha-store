@props([
    'href' => '#',
    'type' => 'primary', // primary, outline, secondary
    'size' => 'normal', // small, normal, large
    'icon' => null,
    'iconPosition' => 'left', // left, right
    'animation' => 'medium', // subtle, medium, playful
    'class' => '',
    'id' => '',
    'theme' => 'default', // default, light
    'inverted' => false, // Untuk situasi di latar belakang gelap (seperti di hero)
])

@php
    // CSS classes based on type
    $buttonClasses = 'x-button';

    // Add type styles
    if ($type === 'primary') {
        $buttonClasses .= ' x-button-primary';
    } elseif ($type === 'outline') {
        $buttonClasses .= ' x-button-outline';
    } elseif ($type === 'secondary') {
        $buttonClasses .= ' x-button-secondary';
    }

    // Add size styles
    if ($size === 'small') {
        $buttonClasses .= ' x-button-small';
    } elseif ($size === 'large') {
        $buttonClasses .= ' x-button-large';
    }

    // Add animation level
    if ($animation === 'subtle') {
        $buttonClasses .= ' x-button-animation-subtle';
    } elseif ($animation === 'playful') {
        $buttonClasses .= ' x-button-animation-playful';
    }

    // Add theme class
    if ($theme === 'light') {
        $buttonClasses .= ' x-button-theme-light';
    }

    // Add inverted class for dark backgrounds
    if ($inverted) {
        $buttonClasses .= ' x-button-inverted';
    }

    // Add shimmer effect to all buttons
    $buttonClasses .= ' x-button-shimmer';

    // Add custom classes
    $buttonClasses .= ' ' . $class;
@endphp

<a href="{{ $href }}" class="{{ $buttonClasses }}" @if($id) id="{{ $id }}" @endif {{ $attributes }}>
    <div class="x-button-shimmer-overlay"></div>

    @if($icon && $iconPosition === 'left')
        <i class="fas fa-{{ $icon }} x-button-icon x-button-icon-left"></i>
    @endif

    <span class="x-button-text">{{ $slot }}</span>

    @if($icon && $iconPosition === 'right')
        <i class="fas fa-{{ $icon }} x-button-icon x-button-icon-right"></i>
    @endif

    <div class="x-button-spotlight"></div>
</a>

<style>
    /* CSS Variables dengan dua tema */
    :root {
        /* Default Theme (Red) - Sudah didefinisikan di main CSS */
        --x-button-primary-bg: var(--x-primary-gradient, linear-gradient(135deg, #FF2D20 0%, #FF8A00 100%));
        --x-button-secondary-bg: white;
        --x-button-primary-color: white;
        --x-button-secondary-color: var(--x-primary-red, #FF2D20);
        --x-button-outline-color: var(--x-primary-red, #FF2D20);
        --x-button-outline-border: var(--x-primary-red, #FF2D20);
        --x-button-shadow: var(--x-button-shadow, 0 6px 20px rgba(255, 45, 32, 0.3));
        --x-button-shadow-hover: var(--x-button-shadow-hover, 0 10px 30px rgba(255, 45, 32, 0.4));
        --x-button-outline-hover-bg: rgba(255, 45, 32, 0.05);
        --x-button-spotlight-bg: radial-gradient(circle, rgba(255, 215, 0, 0.8) 0%, rgba(255, 215, 0, 0) 70%);
        --x-shimmer-gradient: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.6) 50%, rgba(255, 255, 255, 0) 100%);

        /* Light Theme (White) */
        --x-light-primary-bg: white;
        --x-light-secondary-bg: rgba(255, 255, 255, 0.9);
        --x-light-primary-color: var(--x-primary-red, #FF2D20);
        --x-light-secondary-color: var(--x-primary-red, #FF2D20);
        --x-light-outline-color: white;
        --x-light-outline-border: white;
        --x-light-button-shadow: 0 6px 20px rgba(255, 255, 255, 0.3);
        --x-light-button-shadow-hover: 0 10px 30px rgba(255, 255, 255, 0.4);
        --x-light-button-outline-hover-bg: rgba(255, 255, 255, 0.2);
        --x-light-button-spotlight-bg: radial-gradient(circle, rgba(255, 255, 255, 0.8) 0%, rgba(255, 255, 255, 0) 70%);
    }

    /* Button Base Styles */
    .x-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem 1.5rem;
        border-radius: var(--x-button-radius, 40px);
        font-weight: 700;
        font-size: 1rem;
        transition: all 0.3s var(--x-transition-timing, cubic-bezier(0.34, 1.56, 0.64, 1));
        text-decoration: none;
        position: relative;
        overflow: hidden;
        cursor: pointer;
        transform-origin: center;
    }

    /* Primary Button Style - Default Theme */
    .x-button-primary {
        background: var(--x-button-primary-bg);
        color: var(--x-button-primary-color);
        box-shadow: var(--x-button-shadow);
    }

    .x-button-primary:hover, .x-button-primary:focus {
        transform: translateY(-4px);
        box-shadow: var(--x-button-shadow-hover);
        color: var(--x-button-primary-color);
    }

    .x-button-primary:active {
        transform: translateY(-2px);
        box-shadow: var(--x-button-shadow);
    }

    /* Outline Button Style - Default Theme */
    .x-button-outline {
        background: transparent;
        color: var(--x-button-outline-color);
        border: 2px solid var(--x-button-outline-border);
    }

    .x-button-outline:hover, .x-button-outline:focus {
        background: var(--x-button-outline-hover-bg);
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        color: var(--x-button-outline-color);
    }

    .x-button-outline:active {
        transform: translateY(-2px);
    }

    /* Secondary Button Style - Default Theme */
    .x-button-secondary {
        background: var(--x-button-secondary-bg);
        color: var(--x-button-secondary-color);
        box-shadow: var(--x-button-shadow);
    }

    .x-button-secondary:hover, .x-button-secondary:focus {
        transform: translateY(-4px);
        box-shadow: var(--x-button-shadow-hover);
        color: var(--x-button-secondary-color);
    }

    .x-button-secondary:active {
        transform: translateY(-2px);
    }

    /* Light Theme Styles */
    .x-button-theme-light.x-button-primary {
        background: var(--x-light-primary-bg);
        color: var(--x-light-primary-color);
        box-shadow: var(--x-light-button-shadow);
        border: 1px solid var(--x-primary-red, #FF2D20);
    }

    .x-button-theme-light.x-button-primary:hover,
    .x-button-theme-light.x-button-primary:focus {
        box-shadow: var(--x-light-button-shadow-hover);
        color: var(--x-light-primary-color);
        background: rgba(255, 255, 255, 0.9);
    }

    .x-button-theme-light.x-button-outline {
        color: white;
        border: 2px solid white;
        background-color: rgba(255, 45, 32, 0.1);
    }

    .x-button-theme-light.x-button-outline:hover,
    .x-button-theme-light.x-button-outline:focus {
        background: var(--x-light-button-outline-hover-bg);
        color: white;
    }

    .x-button-theme-light.x-button-secondary {
        background: var(--x-light-secondary-bg);
        color: var(--x-light-secondary-color);
        box-shadow: var(--x-light-button-shadow);
        border: 1px solid rgba(255, 45, 32, 0.2);
    }

    .x-button-theme-light.x-button-secondary:hover,
    .x-button-theme-light.x-button-secondary:focus {
        box-shadow: var(--x-light-button-shadow-hover);
        color: var(--x-light-secondary-color);
        background: white;
    }

    .x-button-theme-light .x-button-spotlight {
        background: var(--x-light-button-spotlight-bg);
    }

    /* Inverted Button Styles (untuk background gelap) */
    .x-button-inverted.x-button-primary {
        background: white;
        color: var(--x-primary-red, #FF2D20);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    .x-button-inverted.x-button-primary:hover {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .x-button-inverted.x-button-outline {
        border-color: white;
        color: white;
    }

    .x-button-inverted.x-button-outline:hover {
        background: rgba(255, 255, 255, 0.15);
    }

    .x-button-inverted.x-button-secondary {
        background: rgba(255, 255, 255, 0.15);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .x-button-inverted.x-button-secondary:hover {
        background: rgba(255, 255, 255, 0.25);
    }

    /* Button Sizes */
    .x-button-small {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }

    .x-button-large {
        padding: 1rem 2rem;
        font-size: 1.1rem;
    }

    /* Button Icon */
    .x-button-icon {
        display: inline-block;
        transition: transform 0.3s ease;
    }

    .x-button-icon-left {
        margin-right: 8px;
    }

    .x-button-icon-right {
        margin-left: 8px;
    }

    .x-button:hover .x-button-icon-right {
        transform: translateX(4px);
    }

    .x-button:hover .x-button-icon-left {
        transform: translateX(-2px);
    }

    /* Spotlight Effect */
    .x-button-spotlight {
        position: absolute;
        top: -100%;
        left: -100%;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--x-button-spotlight-bg);
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
        z-index: 1;
    }

    .x-button:hover .x-button-spotlight {
        opacity: 0.8;
        animation: spotlight 1s infinite alternate;
    }

    /* Shimmer effect styles */
    .x-button-shimmer {
        position: relative;
        overflow: hidden;
    }

    .x-button-shimmer-overlay {
        position: absolute;
        top: -100%;
        left: -100%;
        width: 300%;
        height: 300%;
        background: var(--x-shimmer-gradient);
        animation: shimmer 2s infinite;
        pointer-events: none;
        z-index: 2;
    }

    /* Different shimmer speeds for different button types */
    .x-button-primary .x-button-shimmer-overlay {
        animation-duration: 2s;
    }

    .x-button-outline .x-button-shimmer-overlay {
        animation-duration: 2.5s;
    }

    .x-button-secondary .x-button-shimmer-overlay {
        animation-duration: 3s;
    }

    /* Animation Variations */
    .x-button-animation-subtle:hover {
        transform: translateY(-2px);
    }

    .x-button-animation-playful:hover {
        transform: translateY(-5px) rotate(2deg);
    }

    /* Animations */
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

    @keyframes shimmer {
        0% {
            transform: translateX(-100%) skewX(-15deg);
        }

        100% {
            transform: translateX(100%) skewX(-15deg);
        }
    }

    /* Responsive Styles */
    @media (max-width: 767.98px) {
        .x-button {
            padding: 0.75rem 1.25rem;
        }

        .x-button-large {
            padding: 0.9rem 1.75rem;
        }
    }
</style>
