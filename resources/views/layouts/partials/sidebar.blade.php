<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme sidebar">
    <!-- App Brand -->
    <div class="app-brand demo">

        <a href="{{ route('dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('images/logo-snack-circle.png') }}" alt="Logo Aplikasi {{ config('app.name') }}"
                    style="width: 35px;">
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2 app-brand-shimmer" style="font-size: 1.5rem;">
                <span class="text-capitalize">{{ config('app.name') }}</span>
            </span>
        </a>

        <a href="#" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <!-- Menu Items -->
    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ setActive('dashboard') }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Dashboard">Beranda</div>
            </a>
        </li>

        <!-- Penjualan -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Penjualan</span>
        </li>

        <li class="menu-item {{ setActive('pos.*') }}">
            <a href="{{ route('pos.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cart"></i>
                <div data-i18n="POS">Kasir</div>
            </a>
        </li>

        <li class="menu-item {{ setActive('transactions.*') }}">
            <a href="{{ route('transactions.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-receipt"></i>
                <div data-i18n="Transactions">Riwayat Transaksi</div>
            </a>
        </li>

        <!-- Produk & Stok -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Produk & Stok</span>
        </li>

        <li class="menu-item {{ setActive('products.*') }}">
            <a href="{{ route('products.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-box"></i>
                <div data-i18n="Products">Produk</div>
            </a>
        </li>

        <li class="menu-item {{ setActive('purchases.*') }}">
            <a href="{{ route('purchases.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-package"></i>
                <div data-i18n="Purchases">Pembelian</div>
            </a>
        </li>

        <li class="menu-item {{ setActive('stock.*') }} {{ setOpen('stock.*') }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Stock Management">Stok</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ setActive('stock.histories.*') }}">
                    <a href="{{ route('stock.histories.index') }}" class="menu-link">
                        <div data-i18n="Stock History">Riwayat Stok</div>
                    </a>
                </li>
                <li class="menu-item {{ setActive('stock.adjustments.*') }}">
                    <a href="{{ route('stock.adjustments.index') }}" class="menu-link">
                        <div data-i18n="Stock Adjustments">Penyesuaian Stok</div>
                    </a>
                </li>
                <li class="menu-item {{ setActive('stock-takes.*') }}">
                    <a href="{{ route('stock-takes.index') }}" class="menu-link">
                        <div data-i18n="Stock Take">Stok Opname</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Mitra -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Mitra</span>
        </li>

        <li class="menu-item {{ setActive('suppliers.*') }}">
            <a href="{{ route('suppliers.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-truck"></i>
                <div data-i18n="Suppliers">Pemasok</div>
            </a>
        </li>

        <li class="menu-item {{ setActive('customers.*') }}">
            <a href="{{ route('customers.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-group"></i>
                <div data-i18n="Customers">Pelanggan</div>
            </a>
        </li>

        <!-- Data Master -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Data Master</span>
        </li>

        <li class="menu-item {{ setActive('groups.*') }}">
            <a href="{{ route('groups.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-grid-alt"></i>
                <div data-i18n="Groups">Kelompok</div>
            </a>
        </li>

        <li class="menu-item {{ setActive('categories.*') }}">
            <a href="{{ route('categories.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-category"></i>
                <div data-i18n="Categories">Kategori</div>
            </a>
        </li>

        <li class="menu-item {{ setActive('units.*') }}">
            <a href="{{ route('units.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-ruler"></i>
                <div data-i18n="Units">Satuan</div>
            </a>
        </li>

        <li class="menu-item {{ setActive('taxes.*') }}">
            <a href="{{ route('taxes.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-badge-check"></i>
                <div data-i18n="Taxes">Pajak</div>
            </a>
        </li>

        <li class="menu-item {{ setActive('discounts.*') }}">
            <a href="{{ route('discounts.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-tag"></i>
                <div data-i18n="Discounts">Diskon</div>
            </a>
        </li>

        <!-- Laporan -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Laporan</span>
        </li>

        <li class="menu-item {{ setActive('reports.sales') }}">
            <a href="{{ route('reports.sales') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-trending-up"></i>
                <div data-i18n="Sales Report">Penjualan</div>
            </a>
        </li>

        <li class="menu-item {{ setActive('reports.purchasing') }}">
            <a href="{{ route('reports.purchasing') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cart"></i>
                <div data-i18n="Purchasing Report">Pembelian</div>
            </a>
        </li>

        <li class="menu-item {{ setActive('reports.inventory') }}">
            <a href="{{ route('reports.inventory') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-package"></i>
                <div data-i18n="Inventory Report">Inventory</div>
            </a>
        </li>

        <li class="menu-item {{ setActive('reports.financial') }}">
            <a href="{{ route('reports.financial') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-money"></i>
                <div data-i18n="Financial Report">Keuangan</div>
            </a>
        </li>

        @if (auth()->user()->role === 'admin')
            <li class="menu-item {{ setActive('reports.store-performance') }}">
                <a href="{{ route('reports.store-performance') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-store"></i>
                    <div data-i18n="Store Performance">Performa Toko</div>
                </a>
            </li>
        @endif

        <!-- Pengaturan -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Pengaturan</span>
        </li>

        @if (Auth::user()->role === 'admin')
            <li class="menu-item {{ setActive('stores.*') }}">
                <a href="{{ route('stores.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bxl-shopify"></i>
                    <div data-i18n="Stores">Toko</div>
                </a>
            </li>

            <li class="menu-item {{ setActive('users.*') }}">
                <a href="{{ route('users.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user-circle"></i>
                    <div data-i18n="Users">Pengguna</div>
                </a>
            </li>
        @endif

        @if (Auth::user()->role === 'user')
            <li class="menu-item {{ setActive('user.store.*') }}">
                <a href="{{ route('user.store.show') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bxl-shopify"></i>
                    <div data-i18n="Store">Toko</div>
                </a>
            </li>
        @endif

        <li class="menu-item {{ setActive('settings.*') }}">
            <a href="{{ route('settings.printer') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-printer"></i>
                <div data-i18n="Printer Settings">Printer</div>
            </a>
        </li>
    </ul>
</aside>
<!-- / Menu -->
