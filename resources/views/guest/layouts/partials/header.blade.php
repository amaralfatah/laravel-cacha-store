<header class="header header-default site-header @yield('header-class', 'header-transparent')">
    <div class="header__outer">
        <div class="header__inner header--fixed">
            <div class="container">
                <div class="header__main">
                    <div class="header__col header__left">
                        <a href="{{route('guest.home')}}" class="logo">
                            <figure class="logo--normal">
                                <img src="{{asset('payne/assets/img/logo/logo.png')}}" alt="Logo">
                            </figure>
                            <figure class="logo--transparency">
                                <img src="{{asset('payne/assets/img/logo/logo.png')}}" alt="Logo">
                            </figure>
                        </a>
                    </div>
                    <div class="header__col header__center">
                        <nav class="main-navigation d-none d-lg-block">
                            <ul class="mainmenu">
                                <li class="mainmenu__item">
                                    <a href="{{route('guest.home')}}" class="mainmenu__link">Home</a>
                                </li>
                                <li class="mainmenu__item">
                                    <a href="{{route('guest.shop')}}" class="mainmenu__link">Shop</a>
                                </li>
                                <li class="mainmenu__item">
                                    <a href="{{route('guest.contact')}}" class="mainmenu__link">Contact Us</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div class="header__col header__right">
                        <div class="toolbar-item d-none d-lg-block">
                            @if(Auth::user())
                                <a href="{{route('dashboard')}}" class="toolbar-btn">
                                    <span>Dashboard</span>
                                </a>
                            @else
                                <a href="{{route('login')}}" class="toolbar-btn">
                                    <span>Login</span>
                                </a>
                            @endif
                        </div>
                        <div class="toolbar-item d-block d-lg-none">
                            <a href="#offcanvasnav" class="hamburger-icon js-toolbar menu-btn">
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-sticky-height"></div>
    </div>
</header>
