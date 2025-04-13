@extends('guest.layouts.app')

@section('content')
    <!-- Hero Section -->
    @include('guest.landing.hero')

    <!-- Categories - Light background with pattern -->
    <x-guest.section-wrapper id="categories" title="Pilih Kategori Favoritmu" titleHighlight="Kategori"
                             subtitle="Temukan berbagai jenis cemilan kekinian yang bikin nagih"
                             background="light" pattern="true">
        @include('guest.landing.category')
    </x-guest.section-wrapper>

    <!-- Popular Products - Gradient background with particles and pattern -->
    <x-guest.section-wrapper id="popular" title="Produk Terlaris Bulan Ini" titleHighlight="Terlaris"
                             subtitle="Cemilan yang paling banyak diburu anak muda" background="gradient"
                             pattern="true" particles="true">
        @include('guest.landing.popular')
    </x-guest.section-wrapper>

    <!-- All Products - Secondary background with pattern -->
    <x-guest.section-wrapper id="products" title="Katalog Produk Kami" titleHighlight="Produk"
                             subtitle="Temukan berbagai cemilan kekinian khas Pangandaran"
                             background="secondary" pattern="true">
        @include('guest.landing.all-product')
    </x-guest.section-wrapper>

    <!-- Gallery - Light background with pattern -->
    {{--    <x-guest.section-wrapper id="gallery" title="Galeri Produk" titleHighlight="Produk"--}}
    {{--        subtitle="Jelajahi berbagai varian produk menarik dari Cacha Snack"--}}
    {{--        background="light" pattern="true">--}}
    {{--        @include('guest.landing.gallery')--}}
    {{--    </x-guest.section-wrapper>--}}

    <!-- Testimonials - Secondary background with pattern and particles -->
    <x-guest.section-wrapper id="testimonials" title="Apa Kata Mereka?" titleHighlight="Mereka"
                             subtitle="Pengalaman para pelanggan kami yang sudah merasakan kelezatan Cacha Snack"
                             background="gradient" pattern="true" particles="true">
        @include('guest.landing.testimonials')
    </x-guest.section-wrapper>

    <!-- Benefits - Light background with pattern -->
    <x-guest.section-wrapper id="about" title="Mengapa Pilih Cacha Snack?"
                             subtitle="Keunggulan yang membuat kami berbeda dari yang lain"
                             background="light" pattern="true">
        @include('guest.landing.benefits')
    </x-guest.section-wrapper>

    <!-- Statistics - Gradient background with particles and pattern -->
    <x-guest.section-wrapper id="stats" title="Cacha Snack dalam Angka"
                             subtitle="Cemilan kekinian terpercaya yang telah melayani ribuan pelanggan di seluruh Indonesia"
                             background="gradient" particles="true" pattern="true">
        @include('guest.landing.statistics')
    </x-guest.section-wrapper>

    <!-- FAQ - Secondary background with pattern -->
    <x-guest.section-wrapper id="faq" title="Pertanyaan Umum"
                             subtitle="Jawaban untuk pertanyaan yang sering ditanyakan"
                             background="secondary" pattern="true">
        @include('guest.landing.faq')
    </x-guest.section-wrapper>

    <!-- CTA - Primary background (solid red) with particles -->
    <x-guest.section-wrapper id="order" title="Dapatkan Diskon 25% untuk Pembelian Pertama!"
                             subtitle="Gunakan kode promo CACHANEW saat checkout" background="primary"
                             particles="true" pattern="true" padding="large">
        @include('guest.landing.cta')
    </x-guest.section-wrapper>

    <!-- Contact -->
        @include('guest.landing.contact')
@endsection
