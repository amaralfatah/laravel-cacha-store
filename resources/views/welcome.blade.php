@extends('guest.layouts.app')

@section('header-class', '')

@section('content')
    <!-- Slider area Start -->
    @include('guest.welcome.slider-area')
    <!-- Slider area End -->

    <!-- Featured Product Area Start -->
    @include('guest.welcome.featured-product-area')
    <!-- Featured Product Area End -->

    <!-- Product Area Start -->
    @include('guest.welcome.latest-product-area')
    <!-- Product Area End -->

    <!-- Banner Area Start -->
    @include('guest.welcome.banner-area')
    <!-- Banner Area End -->

    <!-- Product Area Start -->
    @include('guest.welcome.popular-product-area')
    <!-- Product Area End -->

    <!-- Countdown Product Area Start -->
    @include('guest.welcome.countdown-product-area')
    <!-- Countdown Product Area End -->

    <!-- Featured Product Area Start -->
    @include('guest.welcome.featured-product-area')
    <!-- Featured Product Area End -->

    @include('guest.welcome.benefit-area')

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Quick view product
            $('.action-btn').on('click', function() {
                const productId = $(this).data('product-id');
                // Implementasi AJAX untuk quick view
            });

            // Add to cart functionality
            $('.add-to-cart').on('click', function(e) {
                e.preventDefault();
                const productId = $(this).data('product-id');
                // Implementasi AJAX untuk add to cart
            });
        });
    </script>
@endpush
