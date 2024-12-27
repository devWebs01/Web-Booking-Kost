<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke(): void
    {
        // Logout pengguna dari guard 'web'
        Auth::guard('web')->logout();

        // Menghapus data sesi dan me-regenerate token sesi
        Session::invalidate();
        Session::regenerateToken();
    }
}
