<?php

namespace App\Http\Middleware;

use App\Models\Booking;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AutoCancelBooking
{
    public function handle(Request $request, Closure $next)
    {
        // Cari booking yang statusnya pending dan sudah melewati batas waktu (misalnya 1 menit untuk testing, ubah sesuai kebutuhan)
        $expiredBookings = Booking::where('status', 'pending')
            ->where('created_at', '<=', now()->subMinutes(1))
            ->get();

        $canceledCount = 0;

        foreach ($expiredBookings as $booking) {
            // Update status booking menjadi 'canceled'
            $booking->update(['status' => 'canceled']);

            // Jika booking memiliki payment, update status payment juga menjadi 'canceled'
            if ($booking->payment) {
                $booking->payment()->update(['status' => 'failed']);
            }

            $canceledCount++;
        }

        if ($canceledCount > 0) {
            Log::info("AutoCancelBooking: {$canceledCount} booking dibatalkan karena melewati batas waktu yang ditentukan.");
        }

        return $next($request);
    }
}
