<?php

use function Livewire\Volt\{computed, state, on};
use App\Models\Setting;

state([
    'setting' => fn() => Setting::first(),
]);

?>


<div>
    <div class="brand-logo d-flex align-items-center justify-content-between">
        <a href="#" class="text-nowrap logo-img">
            <h5 style="font-weight: 900" class="text-nowrap ms-lg-2 text-primary">
                {{ $setting->name }}
            </h5>
        </a>
        <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
        </div>
    </div>
    <!-- Sidebar navigation-->
    <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
        <ul id="sidebarnav">
            <li class="nav-small-cap">
                <iconify-icon icon="solar:shield-user-outline" class="nav-small-cap-icon fs-4"></iconify-icon>
                <span class="hide-menu">Home</span>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('home') }}" aria-expanded="false"
                    {{ request()->routeIs('home') }}>
                    <iconify-icon icon="solar:home-2-bold"></iconify-icon>
                    <span class="hide-menu">Beranda
                    </span>
                </a>
            </li>

            <li>
                <span class="sidebar-divider lg"></span>
            </li>

            <li class="nav-small-cap">
                <iconify-icon icon="solar:shield-user-outline" class="nav-small-cap-icon fs-4"></iconify-icon>
                <span class="hide-menu">Pengguna</span>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('users.index') }}" aria-expanded="false"
                    {{ request()->routeIs('admin/users') }}>
                    <iconify-icon icon="solar:home-2-bold"></iconify-icon>
                    <span class="hide-menu">
                        Admin
                    </span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('customers') }}" aria-expanded="false"
                    {{ request()->routeIs('admin/customers') }}>
                    <iconify-icon icon="solar:home-2-bold"></iconify-icon>
                    <span class="hide-menu">
                        Pelanggan
                    </span>
                </a>
            </li>


            <li>
                <span class="sidebar-divider lg"></span>
            </li>

            <li class="nav-small-cap">
                <iconify-icon icon="solar:shield-user-outline" class="nav-small-cap-icon fs-4"></iconify-icon>
                <span class="hide-menu">Ruangan</span>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('rooms.index') }}" aria-expanded="false"
                    {{ request()->routeIs('admin/rooms') }}>
                    <iconify-icon icon="solar:home-2-bold"></iconify-icon>
                    <span class="hide-menu">Kamar
                    </span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('settings.index') }}" aria-expanded="false"
                    {{ request()->routeIs('admin/customers') }}>
                    <iconify-icon icon="solar:home-2-bold"></iconify-icon>
                    <span class="hide-menu">
                        Pengaturan Kost
                    </span>
                </a>
            </li>

            <li>
                <span class="sidebar-divider lg"></span>
            </li>

            <li class="nav-small-cap">
                <iconify-icon icon="solar:shield-user-outline" class="nav-small-cap-icon fs-4"></iconify-icon>
                <span class="hide-menu">Transaksi</span>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('transactions.index') }}" aria-expanded="false"
                    {{ request()->routeIs(['admin/transactions', 'admin/transactions/*']) }}>
                    <iconify-icon icon="solar:home-2-bold"></iconify-icon>
                    <span class="hide-menu">
                        Pemesanan
                    </span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('reports.bookings') }}" aria-expanded="false"
                    {{ request()->routeIs('admin/reports') }}>
                    <iconify-icon icon="solar:home-2-bold"></iconify-icon>
                    <span class="hide-menu">
                        Laporan Pemesanan
                    </span>
                </a>
            </li>


        </ul>
    </nav>
    <!-- End Sidebar navigation -->
</div>
