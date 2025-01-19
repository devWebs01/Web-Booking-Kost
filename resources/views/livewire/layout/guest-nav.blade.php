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

            <a href="{{ route('histories.index') }}" class="fw-bold nav-item nav-link">Riwayat</a>

            <a href="" class="fw-bold nav-item nav-link">Profil</a>

            <a wire:click="logout" class="fw-bold nav-item nav-link">Keluar</a>

        @else
            <a href="{{ route('login') }}" class="fw-bold nav-item nav-link">Masuk</a>
        @endauth
    </div>
    @endvolt
</div>
