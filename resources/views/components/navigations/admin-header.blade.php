<?php

use function Livewire\Volt\{state};

$logout = function () {
    Auth::logout();
    $this->redirect("/");
};
?>

@volt
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
        <div class="message-body">
            <a href="{{ route("account.auth") }}" class="d-flex align-items-center gap-2 dropdown-item">
                <i class="ti ti-user fs-6"></i>
                <p class="mb-0 fs-3">Akun Profile</p>
            </a>
            <button wire:click="logout" class="d-flex align-items-center gap-2 dropdown-item">
                <i class="ti ti-logout fs-6"></i>
                <p class="mb-0 fs-3">Keluar</p>
            </button>
        </div>
    </div>
@endvolt
