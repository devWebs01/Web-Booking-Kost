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
            <li class="sidebar-item">
                <a wire:navigate class="sidebar-link" href="{{ route('dashboard') }}" aria-expanded="false"
                    {{ request()->routeIs('dashboard') }}>
                    <iconify-icon icon="solar:home-2-bold"></iconify-icon>
                    <span class="hide-menu">Beranda
                    </span>
                </a>
            </li>
        </ul>
    </nav>
    <!-- End Sidebar navigation -->
</div>
