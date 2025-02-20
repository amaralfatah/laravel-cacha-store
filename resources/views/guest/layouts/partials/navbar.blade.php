<nav class="navbar navbar-expand-lg navbar-light fixed-top bg-white">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{route('guest.home')}}">
            <div class="logo-container me-2">
                <img src="{{asset('images/logo-snack-circle.png')}}" alt="Cacha Snack Logo" height="40">
            </div>
            <span class="text-primary-cacha">Cacha Snack</span>
        </a>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#home">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#popular">Terlaris</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#products">Produk</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#testimonials">Testimoni</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">Tentang Kami</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact">Kontak</a>
                </li>
                <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                    <a class="btn btn-primary-cacha" href="#order">
                        <i class="fas fa-shopping-cart me-2"></i>Beli Sekarang
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
