<?php

use App\Models\Booking;
use function Livewire\Volt\{computed, state, on};

$logout = function () {
    Auth::logout();
    $this->redirect('/');
};

state([
    'userId' => Auth()->user()->id ?? '',
    'booking' => Booking::where('user_id', Auth()->user()->id ?? '')
        ->where('status', 'pending')->count()
]);

on([
    'cart-updated' => function () {
        $this->booking = Booking::where('user_id', Auth()->user()->id ?? '')
            ->where('status', 'pending')->count();
    },
]);

?>

<div>
    @volt

    <div class="navbar-nav mx-0 mx-lg-auto">
        <a href="/" class="fw-bold nav-item nav-link {{ Route::is(['/']) ? 'active text-primary' : '' }}">Beranda</a>

        <a href="{{ route('catalogs.index') }}" class="fw-bold nav-item nav-link {{ Route::is(['catalogs.index']) ? 'active text-primary' : '' }}">Pesan Kamar</a>

        @auth

        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle fw-bolder {{ Route::is(['histories.index', 'profile.guest']) ? 'active text-primary' : '' }}"
                href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Pengguna
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="{{ route('profile.guest') }}">Akun Profil</a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('histories.index') }}">Transaksi</a>
                </li>
                <li>
                    <a class="dropdown-item" wire:click='logout'>Keluar</a>
                </li>
            </ul>
        </li>

        @else
            <a href="{{ route('login') }}" class="fw-bold nav-item nav-link">Masuk</a>
        @endauth
    </div>
    @endvolt
</div>
