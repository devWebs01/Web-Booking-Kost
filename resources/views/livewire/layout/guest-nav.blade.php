<?php

use App\Livewire\Actions\Logout;
use App\Models\Order;
use function Livewire\Volt\{computed, state, on};

$logout = function (Logout $logout) {
    $logout();
    $this->redirect('/');
};
?>

<div>
    @volt
        <header id="header">
            <nav id="primary-header" class="navbar navbar-expand-lg py-4">
                <div class="container-fluid padding-side">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <a class="navbar-brand" href="/">
                            <img src="/guest/images/main-logo.png" class="logo img-fluid">
                        </a>
                        <button class="navbar-toggler border-0 d-flex d-lg-none order-3 p-2 shadow-none" type="button"
                            data-bs-toggle="offcanvas" data-bs-target="#bdNavbar" aria-controls="bdNavbar"
                            aria-expanded="false">
                            <svg class="navbar-icon" width="60" height="60">
                                <use xlink:href="#navbar-icon"></use>
                            </svg>
                        </button>
                        <div class="header-bottom offcanvas offcanvas-end " id="bdNavbar"
                            aria-labelledby="bdNavbarOffcanvasLabel">
                            <div class="offcanvas-header px-4 pb-0">
                                <button type="button" class="btn-close btn-close-black mt-2" data-bs-dismiss="offcanvas"
                                    aria-label="Close" data-bs-target="#bdNavbar"></button>
                            </div>
                            <div class="offcanvas-body align-items-center justify-content-center">
                                <div class="search d-block d-lg-none m-5">
                                    <form class=" position-relative">
                                        <input type="text" class="form-control bg-secondary border-0 rounded-5 px-4 py-2"
                                            placeholder="Search...">
                                        <a href="#"
                                            class="position-absolute top-50 end-0 translate-middle-y p-1 me-3">
                                            <svg class="" width="20" height="20">
                                                <use xlink:href="#search"></use>
                                            </svg>
                                        </a>
                                    </form>
                                </div>
                                <ul class="navbar-nav align-items-center mb-2 mb-lg-0">
                                    <li class="nav-item px-3">
                                        <a class="nav-link p-0" aria-current="page" href="/">Beranda</a>
                                    </li>
                                    <li class="nav-item px-3">
                                        <a class="nav-link p-0" href="{{ route('bookings.index') }}">Pemesanan</a>
                                    </li>

                                    @auth
                                        <li class="nav-item px-3 dropdown">
                                            <a class="nav-link p-0 dropdown-toggle text-center" data-bs-toggle="dropdown"
                                                href="#" role="button" aria-expanded="false">Pengguna</a>
                                            <ul class="dropdown-menu dropdown-menu-end animate slide mt-3 border-0 shadow">
                                                <li><a href="/" class="dropdown-item ">Profil Akun </a>
                                                </li>
                                                <li><a href="/" class="dropdown-item ">Pemesanan </a>
                                                </li>

                                            </ul>
                                        </li>
                                    @endauth
                                </ul>
                            </div>
                        </div>
                        <div class="search d-lg-block d-none">
                            <div class="d-flex gap-4">
                                @guest
                                    <a class="btn btn-primary" href="{{ route('login') }}" role="button">Login</a>
                                    <a class="btn btn-primary" href="{{ route('register') }}" role="button">Register</a>
                                @else
                                    <button type="button" wire:click="logout" class="btn btn-primary" href="#"
                                        role="button">Logout</button>
                                @endguest
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </header>
    @endvolt
</div>
