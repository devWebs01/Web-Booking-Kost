<?php

use App\Models\Cart;
use App\Models\Booking;
use function Livewire\Volt\{computed, state, on};

$logout = function () {
    Auth::logout();
    $this->redirect('/');
};

state([
    'userId' => Auth()->user()->id ?? '',
    'cart' => fn() => Cart::where('user_id', auth()->user()->id ?? '')->count(),
    'booking' => Booking::where('user_id', Auth()->user()->id ?? '')
        ->where('status', 'pending')->count()
]);

on([
    'cart-updated' => function () {
        $this->cart = Cart::where('user_id', auth()->user()->id)->count();
        $this->booking = Booking::where('user_id', Auth()->user()->id ?? '')
            ->where('status', 'pending')->count();
    },
]);

?>

<div>
    @volt

    <div class="navbar-nav mx-0 mx-lg-auto">
        <a href="/" class="fw-bold nav-item nav-link active">Beranda</a>

        <a href="{{ route('catalogs.index') }}" class="fw-bold nav-item nav-link">Pemesanan</a>

        @auth

            <a href="{{ route('catalogs.cart') }}" class="fw-bold nav-item nav-link">
                <span class="position-relative">
                    Keranjang
                    @if ($cart > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $cart }}
                        </span>
                    @endif
                </span>
            </a>

            <a href="{{ route('histories.index') }}" class="fw-bold nav-item nav-link">
                <span class="position-relative">
                    Riwayat
                    @if ($booking > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $booking }}
                        </span>
                    @endif
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
