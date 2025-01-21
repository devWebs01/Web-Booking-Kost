<?php

use App\Models\Cart;
use function Livewire\Volt\{computed, state, on};

$logout = function () {
    Auth::logout();
    $this->redirect('/');
};

state([
    'userId' => Auth()->user()->id ?? '',
    'cart' => fn() => Cart::where('user_id', auth()->user()->id ?? '')->get(),
]);

on([
    'cart-updated' => function () {
        $this->cart = Cart::where('user_id', auth()->user()->id)->get();
    },
]);

?>

<div>
    @volt

    <div class="navbar-nav mx-0 mx-lg-auto">
        <a href="/" class="fw-bold nav-item nav-link active">Beranda</a>

        <a href="{{ route('catalogs.index') }}" class="fw-bold nav-item nav-link">Pemesanan</a>

        @auth

            <a href="{{ route('histories.index') }}" class="fw-bold nav-item nav-link">Riwayat</a>

            <a href="{{ route('histories.index') }}" class="fw-bold nav-item nav-link">
                <span class="position-relative">
                    Keranjang
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $cart->count() }}
                    </span>
                </span>
            </a>

            <a href="{{ route('profile.guest') }}" class="fw-bold nav-item nav-link">Profil</a>

            <a wire:click="logout" class="fw-bold nav-item nav-link">Keluar</a>

        @else
            <a href="{{ route('login') }}" class="fw-bold nav-item nav-link">Masuk</a>
        @endauth
    </div>
    @endvolt
</div>