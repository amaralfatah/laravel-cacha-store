@extends('guest.layouts.app')

@section('header-class', '')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #map-container {
            height: 400px;
            width: 100%;
        }
    </style>
@endpush

@section('breadcrumb')
    <section class="page-title-area bg-color" data-bg-color="#f4f4f4">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="page-title">Contact Us</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{route('guest.home')}}">Home</a></li>
                        <li class="current"><span>Contact Us</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <div class="page-content-inner pt--75 pt-md--55">
        <!-- Contact Area Start -->
        <section class="contact-area mb--75 mb-md--55">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-5 mb-sm--30">
                        <div class="heading mb--32">
                            <h2>Get In Touch</h2>
                            <hr class="delimeter">
                        </div>
                        <div class="contact-info mb--20">
                            <p>
                                <i class="fa fa-map-marker"></i>
                                <span>{{ $store->address }}</span>
                            </p>
                            <p>
                                <i class="fa fa-phone"></i>
                                <span>{{ $store->phone }}</span>
                            </p>
                            <p>
                                <i class="fa fa-envelope"></i>
                                <span>{{ $store->email }}</span>
                            </p>
                            <p>
                                <i class="fa fa-building"></i>
                                <span>{{ $store->name }} ({{ $store->code }})</span>
                            </p>
                            <p>
                                <i class="fa fa-clock-o"></i>
                                <span>Mon – Fri : 9:00 – 18:00</span>
                            </p>
                        </div>
                        <div class="social social-rounded space-10">
                            <a href="https://www.facebook.com" class="social__link">
                                <i class="fa fa-facebook"></i>
                            </a>
                            <a href="https://www.twitter.com" class="social__link">
                                <i class="fa fa-twitter"></i>
                            </a>
                            <a href="https://www.instagram.com" class="social__link">
                                <i class="fa fa-instagram"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-7 offset-lg-1">
                        <div class="heading mb--40">
                            <h2>Contact Us</h2>
                            <hr class="delimeter">
                        </div>
                        <div id="email-status" class="mb--30"></div>
                        <form id="contact-form" class="form">
                            <input type="email" name="email" id="con_email" class="form__input mb--30" placeholder="Email*" required>
                            <input type="text" name="name" id="con_name" class="form__input mb--30" placeholder="Name*" required>
                            <textarea class="form__input form__input--textarea mb--30" placeholder="Message" id="con_message" name="message" required></textarea>
                            <input type="hidden" name="store_id" value="{{ $store->id }}">
                            <input type="hidden" name="store_name" value="{{ $store->name }}">
                            <input type="hidden" name="store_email" value="{{ $store->email }}">
                            <button type="submit" class="btn btn-shape-round form__submit">Send Request</button>

                            <div id="email-status" class="mt-3"></div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- Contact Area End -->

        <!-- Map Area Start -->
        <div class="map-area">
            <div id="map-container"></div>
        </div>
        <!-- Map Area End -->
    </div>
@endsection

@push('scripts')
    <!-- EmailJS SDK -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        // Initialize EmailJS
        (function() {
            // Replace with your EmailJS public key
            emailjs.init("QdkV99AroXCn9Zeji");
        })();

        // Handle form submission
        document.addEventListener('DOMContentLoaded', function() {
            const contactForm = document.getElementById('contact-form');
            const statusDiv = document.getElementById('email-status');

            contactForm.addEventListener('submit', function(event) {
                event.preventDefault();

                // Show loading state
                statusDiv.innerHTML = '<div class="alert alert-info">Sending your message...</div>';

                // Prepare template parameters
                const templateParams = {
                    name: document.getElementById('con_name').value,
                    email: document.getElementById('con_email').value,
                    message: document.getElementById('con_message').value,
                    site_name: contactForm.store_name.value,
                    our_email: contactForm.store_email.value,
                };

                // Send email using EmailJS
                // Replace with your EmailJS service ID and template ID
                emailjs.send('service_6sfihlo', 'template_22525qd', templateParams)
                    .then(function(response) {
                        console.log('SUCCESS!', response.status, response.text);
                        statusDiv.innerHTML = '<div class="alert alert-success">Thank you for your message. We will get back to you soon!</div>';
                        contactForm.reset();
                    }, function(error) {
                        console.log('FAILED...', error);
                        statusDiv.innerHTML = '<div class="alert alert-danger">Sorry, there was a problem sending your message. Please try again later.</div>';
                    });
            });

            // Map initialization
            const defaultLat = -6.2088;
            const defaultLng = 106.8456;

            // Get coordinates from store
            const lat = {{ $store->latitude ?? 'defaultLat' }};
            const lng = {{ $store->longitude ?? 'defaultLng' }};

            // Initialize the map
            const map = L.map('map-container').setView([lat, lng], 15);

            // Add OpenStreetMap tiles with default styling
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Add default marker
            const marker = L.marker([lat, lng]).addTo(map);

            // Add popup with store name
            marker.bindPopup('{{ $store->name }}').openPopup();
        });
    </script>
@endpush
