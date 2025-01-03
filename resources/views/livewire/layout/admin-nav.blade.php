<?php

use function Livewire\Volt\{computed, state, on};

state([]);

?>


<div>
    <div class="brand-logo d-flex align-items-center justify-content-between">
        <a href="#" class="text-nowrap logo-img">
            <h4 style="font-weight: 900" class="ms-lg-2 text-primary">
                nama/logo
            </h4>
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
                <span class="hide-menu">Dashboard</span>
            </li>

            <li class="sidebar-item">
                <a wire:navigate class="sidebar-link" href="{{ route('dashboard') }}" aria-expanded="false"
                    {{ request()->routeIs('dashboard') }}>
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
                <span class="hide-menu">Ruangan</span>
            </li>

            <li class="sidebar-item">
                <a wire:navigate class="sidebar-link" href="{{ route('rooms.index') }}" aria-expanded="false"
                    {{ request()->routeIs('admin/rooms') }}>
                    <iconify-icon icon="solar:home-2-bold"></iconify-icon>
                    <span class="hide-menu">Kamar
                    </span>
                </a>
            </li>
        </ul>
    </nav>
    <!-- End Sidebar navigation -->
</div>
