<?php

use App\Livewire\Actions\Logout;
use App\Models\Cart;
use function Livewire\Volt\{state, computed, on};

$logout = function (Logout $logout) {
    $logout();

    $this->redirect('/');
};

?>

<div>
    @auth
        <div class="d-lg-flex gap-3">
            <a href="#" class="text-white btn border position-relative">
                <i class="fa-solid fa-cart-shopping"></i>
            </a>
            <a href="#" class="text-white btn border">
                <i class="fa-solid fa-truck"></i>
            </a>
            <a href="/user/{{ auth()->id() }}" class="text-white btn border position-relative">
                <i class="fa-solid fa-user"></i>
            </a>
            <a wire:click="logout" href="#" class="text-white btn border">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    @else
        <a class="btn btn-outline-light btn-sm rounded" href="{{ route('login') }}" role="button">Masuk</a>
        <a class="btn btn-outline-light btn-sm rounded" href="{{ route('register') }}" role="button">Daftar</a>
    @endauth
</div>
