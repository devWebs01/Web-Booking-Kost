<?php

// Mengimpor kelas Password dari namespace Illuminate\Support\Facades
use Illuminate\Support\Facades\Password;
// Mengimpor kelas Session dari namespace Illuminate\Support\Facades
use Illuminate\Support\Facades\Session;

// Menggunakan fungsi-fungsi dari Livewire\Volt
use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

// Mengatur layout untuk halaman guest menggunakan 'layouts.guest'
layout('layouts.guest');

// Mendefinisikan state (keadaan) dengan variabel 'email' dan memberinya nilai awal kosong
state(['email' => '']);

// Mendefinisikan aturan validasi untuk 'email'
rules(['email' => ['required', 'string', 'email']]);

// Membuat fungsi bernama $sendPasswordResetLink
$sendPasswordResetLink = function () {
    // Memvalidasi input dengan aturan yang telah ditentukan sebelumnya
    $this->validate();

    // Mengirim link reset password ke alamat email yang diberikan
    $status = Password::sendResetLink(
        $this->only('email')
    );

    // Memeriksa status dari upaya pengiriman link reset
    if ($status != Password::RESET_LINK_SENT) {
        // Jika gagal, tambahkan pesan kesalahan ke variabel 'email'
        $this->addError('email', __($status));

        return;
    }

    // Jika berhasil, reset nilai 'email' dan tampilkan pesan sukses
    $this->reset('email');

    // Menyimpan pesan sukses dalam sesi untuk ditampilkan kepada pengguna
    Session::flash('status', __($status));
};

?>


<div>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</div>
