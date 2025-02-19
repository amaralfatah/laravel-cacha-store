<!-- Navbar -->

<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <!-- Search -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item d-flex align-items-center position-relative">
                <i class="bx bx-search fs-4 lh-0"></i>
                <input type="text" class="form-control border-0 shadow-none search-input" placeholder="Search..."
                    aria-label="Search..." />
                <div class="search-results position-absolute start-0 w-100 bg-white rounded shadow-lg p-3"
                    style="display: none; z-index: 1000; top: 100%; margin-top: 1rem; min-width: 100%;"></div>
            </div>
        </div>
        <!-- /Search -->

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <!-- POS Button -->
            <li class="nav-item lh-1 me-3">
                <a href="{{ route('pos.index') }}"
                    class="btn btn-danger position-relative hover-shadow-lg d-inline-flex align-items-center transition-all hover:scale-105">
                    <i class="bx bx-cart-alt me-2 animate-bounce"></i>
                    Kasir
                    <span
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning animate-pulse">
                        POS
                    </span>
                </a>
            </li>

            <!-- User Dropdown -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow p-2 rounded-circle hover:bg-light transition-all"
                    href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                    <div class="avatar avatar-online">
                        <img src="{{Auth::user()->store->logo ?? asset('sneat/assets/img/avatars/1.png') }}" alt="Profile"
                            class="w-px-40 h-auto rounded-circle shadow-sm hover:shadow-md transition-all" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg animate__animated animate__fadeIn">
                    <li>
                        <a class="dropdown-item hover:bg-light-primary" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="{{Auth::user()->store->logo ?? asset('sneat/assets/img/avatars/1.png') }}" alt="Profile"
                                            class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">{{ Auth::user()->name }}</span>
                                    <small class="text-muted">{{ Auth::user()->role }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    @if (Auth::user()->role == 'admin')
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 hover:bg-light-primary"
                                href="{{ route('users.edit', Auth::user()->id) }}">
                                <i class="bx bx-user"></i>
                                <span>My Profile</span>
                            </a>
                        </li>
                    @endif
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 hover:bg-light-primary" href="{{route('stores.index')}}">
                            <i class="bx bx-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" id="logout-form">
                            @csrf
                            <button
                                class="dropdown-item d-flex align-items-center gap-2 hover:bg-light-danger text-danger">
                                <i class="bx bx-power-off"></i>
                                <span>Log Out</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<!-- / Navbar -->
