<?php

// Mengimpor kelas RouteServiceProvider dari namespace App\Providers
use App\Providers\RouteServiceProvider;
// Mengimpor kelas Auth dari namespace Illuminate\Support\Facades
use Illuminate\Support\Facades\Auth;
// Mengimpor kelas ValidationException dari namespace Illuminate\Validation
use Illuminate\Validation\ValidationException;

// Menggunakan fungsi-fungsi dari Livewire\Volt
use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

// Mengatur layout untuk halaman guest menggunakan 'layouts.guest'
layout('layouts.guest');

// Mendefinisikan state (keadaan) dengan variabel 'password' dan memberinya nilai awal kosong
state(['password' => '']);

// Mendefinisikan aturan validasi untuk 'password'
rules(['password' => ['required', 'string']]);

// Membuat fungsi bernama $confirmPassword
$confirmPassword = function () {
    // Memvalidasi input dengan aturan yang telah ditentukan sebelumnya
    $this->validate();

    // Memeriksa kecocokan email dan password pengguna menggunakan Auth
    if (! Auth::guard('web')->validate([
        'email' => Auth::user()->email,
        'password' => $this->password,
    ])) {
        // Jika tidak cocok, lempar ValidationException dengan pesan kesalahan
        throw ValidationException::withMessages([
            'password' => __('auth.password'),
        ]);
    }

    // Jika cocok, simpan waktu konfirmasi password dalam sesi
    session(['auth.password_confirmed_at' => time()]);

    // Arahkan pengguna ke halaman yang diinginkan atau halaman utama jika tidak ada yang diinginkan
    $this->redirect(
        session('url.intended', RouteServiceProvider::HOME),
        navigate: true
    );
};

?>


<div>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form wire:submit="confirmPassword">
        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="password"
                          id="password"
                          class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</div>
