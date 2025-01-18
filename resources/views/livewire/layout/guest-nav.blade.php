<?php

use App\Models\Order;
use function Livewire\Volt\{computed, state, on};

$logout = function () {
    Auth::logout();
    $this->redirect('/');
};

?>

<div>
    @volt

    <div class="navbar-nav mx-0 mx-lg-auto">
        <a href="/" class="fw-bold nav-item nav-link active">Beranda</a>

        <a href="{{ route('bookings.index') }}" class="fw-bold nav-item nav-link">Pemesanan</a>

        @auth
            <div class="nav-item dropdown">
                <a href="#" class="nav-link" data-bs-toggle="dropdown">
                    <span class="dropdown-toggle">Profil</span>
                </a>
                <div class="dropdown-menu">
                    <a href="feature.html" class="dropdown-item">
                        Akun
                    </a>
                    <a wire:click="logout" class="dropdown-item">
                        Logout
                    </a>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}" class="fw-bold nav-item nav-link">Masuk</a>
        @endauth
    </div>
    @endvolt
</div>