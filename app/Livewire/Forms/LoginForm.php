<?php

namespace App\Livewire\Forms;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Rule;
use Livewire\Form;

class LoginForm extends Form
{
    // Mendefinisikan properti dengan atribut Rule untuk validasi
    #[Rule('required|string|email')]
    public string $email = '';

    #[Rule('required|string')]
    public string $password = '';

    #[Rule('boolean')]
    public bool $remember = false;

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        // Memastikan bahwa permintaan otentikasi tidak dalam batas rate limit
        $this->ensureIsNotRateLimited();

        // Mencoba melakukan otentikasi menggunakan kredensial yang diberikan
        if (! Auth::attempt($this->only(['email', 'password']), $this->remember)) {
            // Jika otentikasi gagal, hit rate limiter dan lemparkan exception ValidationException
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Jika otentikasi berhasil, bersihkan rate limiter
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        // Jika terlalu banyak percobaan otentikasi, lemparkan exception Lockout
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        // Hitung waktu yang diperlukan untuk rate limiter
        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        // Mendapatkan kunci untuk rate limiter berdasarkan email dan alamat IP
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}
