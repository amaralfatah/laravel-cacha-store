@extends('guest.layouts.app')

@section('header-class', '')

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
                                <span>221b Baker St, Marylebone <br>London NW1 6XE, UK</span>
                            </p>
                            <p>
                                <i class="fa fa-phone"></i>
                                <span>+1-202-242-8157</span>
                            </p>
                            <p>
                                <i class="fa fa-fax"></i>
                                <span>+1-202-501-1829</span>
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
                            <a href="https://www.plus.google.com" class="social__link">
                                <i class="fa fa-google-plus"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-7 offset-lg-1">
                        <div class="heading mb--40">
                            <h2>Contact Us</h2>
                            <hr class="delimeter">
                        </div>
                        <form action="mail.php" class="form" id="contact-form">
                            <input type="email" name="con_email" id="con_email" class="form__input mb--30" placeholder="Email*">
                            <input type="text" name="con_name" id="con_name" class="form__input mb--30" placeholder="Name*">
                            <textarea class="form__input form__input--textarea mb--30" placeholder="Message" id="con_message" name="con_message"></textarea>
                            <button type="submit" class="btn btn-shape-round form__submit">Send Request</button>
                            <div class="form__output"></div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- Contact Area End -->

        <!-- Google Map Area Start -->
        <div class="google-map-area">
            <div id="google-map"></div>
        </div>
        <!-- Google Map Area End -->
    </div>
@endsection

@push('scripts')
    <!-- Google Map -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCxvP66_Xk1ts77oL2Z7EpDxhDD_jMg-D0"></script>
    <script>
        // When the window has finished loading create our google map below
        google.maps.event.addDomListener(window, 'load', init);

        function init() {
            // Basic options for a simple Google Map
            // For more options see: https://developers.google.com/maps/documentation/javascript/reference#MapOptions
            var mapOptions = {
                // How zoomed in you want the map to start at (always required)
                zoom: 12,

                scrollwheel: false,

                // The latitude and longitude to center the map (always required)
                center: new google.maps.LatLng(40.740610, -73.935242), // New York

                // How you would like to style the map.
                // This is where you would paste any style found on

                styles: [{
                    "featureType": "water",
                    "elementType": "geometry",
                    "stylers": [{
                        "color": "#e9e9e9"
                    },
                        {
                            "lightness": 17
                        }
                    ]
                },
                    {
                        "featureType": "landscape",
                        "elementType": "geometry",
                        "stylers": [{
                            "color": "#f5f5f5"
                        },
                            {
                                "lightness": 20
                            }
                        ]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "geometry.fill",
                        "stylers": [{
                            "color": "#ffffff"
                        },
                            {
                                "lightness": 17
                            }
                        ]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "geometry.stroke",
                        "stylers": [{
                            "color": "#ffffff"
                        },
                            {
                                "lightness": 29
                            },
                            {
                                "weight": 0.2
                            }
                        ]
                    },
                    {
                        "featureType": "road.arterial",
                        "elementType": "geometry",
                        "stylers": [{
                            "color": "#ffffff"
                        },
                            {
                                "lightness": 18
                            }
                        ]
                    },
                    {
                        "featureType": "road.local",
                        "elementType": "geometry",
                        "stylers": [{
                            "color": "#ffffff"
                        },
                            {
                                "lightness": 16
                            }
                        ]
                    },
                    {
                        "featureType": "poi",
                        "elementType": "geometry",
                        "stylers": [{
                            "color": "#f5f5f5"
                        },
                            {
                                "lightness": 21
                            }
                        ]
                    },
                    {
                        "featureType": "poi.park",
                        "elementType": "geometry",
                        "stylers": [{
                            "color": "#dedede"
                        },
                            {
                                "lightness": 21
                            }
                        ]
                    },
                    {
                        "elementType": "labels.text.stroke",
                        "stylers": [{
                            "visibility": "on"
                        },
                            {
                                "color": "#ffffff"
                            },
                            {
                                "lightness": 16
                            }
                        ]
                    },
                    {
                        "elementType": "labels.text.fill",
                        "stylers": [{
                            "saturation": 36
                        },
                            {
                                "color": "#333333"
                            },
                            {
                                "lightness": 40
                            }
                        ]
                    },
                    {
                        "elementType": "labels.icon",
                        "stylers": [{
                            "visibility": "off"
                        }]
                    },
                    {
                        "featureType": "transit",
                        "elementType": "geometry",
                        "stylers": [{
                            "color": "#f2f2f2"
                        },
                            {
                                "lightness": 19
                            }
                        ]
                    },
                    {
                        "featureType": "administrative",
                        "elementType": "geometry.fill",
                        "stylers": [{
                            "color": "#fefefe"
                        },
                            {
                                "lightness": 20
                            }
                        ]
                    },
                    {
                        "featureType": "administrative",
                        "elementType": "geometry.stroke",
                        "stylers": [{
                            "color": "#fefefe"
                        },
                            {
                                "lightness": 17
                            },
                            {
                                "weight": 1.2
                            }
                        ]
                    }
                ]
            };

            // Get the HTML DOM element that will contain your map
            // We are using a div with id="map" seen below in the <body>
            var mapElement = document.getElementById('google-map');

            // Create the Google Map using our element and options defined above
            var map = new google.maps.Map(mapElement, mapOptions);

            // Let's also add a marker while we're at it
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(40.740610, -73.935242),
                map: map,
                title: 'Contixs',
                icon: "{{asset('payne/assets/img/icons/marker.png')}}",
                animation: google.maps.Animation.BOUNCE
            });
        }
    </script>
@endpush
