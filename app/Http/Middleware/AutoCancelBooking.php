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
        $expire_time = 5;
        // Cari booking yang statusnya pending dan sudah melewati batas waktu (misalnya 1 menit untuk testing, ubah sesuai kebutuhan)
        $expiredBookings = Booking::where('status', 'PENDING')
            ->where('created_at', '<=', now()->subMinutes($expire_time))
            ->get();

        $canceledCount = 0;

        foreach ($expiredBookings as $booking) {
            // Update status booking menjadi 'canceled'
            $booking->update(['status' => 'CANCEL']);

            // Jika booking memiliki payment, update status payment juga menjadi 'canceled'
            if ($booking->payment) {
                $booking->payment()->update(['status' => 'FAILED']);
            }

            $canceledCount++;
        }

        if ($canceledCount > 0) {
            Log::info("AutoCancelBooking: {$canceledCount} booking dibatalkan karena melewati batas waktu yang ditentukan.");
        }

        return $next($request);
    }
}
