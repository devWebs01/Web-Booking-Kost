<?php

namespace App\Http\Middleware;

use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Closure;

class AutoCancelBooking
{
    public function handle(Request $request, Closure $next)
    {
        $setting = Setting::first();
        $expire_time = $setting ? $setting->expire_time : 10; // Default 60 menit jika tidak ada setting

        $expiredBookings = Booking::where('status', 'PENDING')
            ->where('expired_at', '<=', now()) // Gunakan 'expired_at' daripada 'created_at'
            ->get();

        $canceledCount = 0;

        foreach ($expiredBookings as $booking) {
            // Update status booking menjadi 'CANCEL'
            $booking->update(['status' => 'CANCEL']);

            // Jika booking memiliki pembayaran, update status pembayaran menjadi 'FAILED'
            if ($booking->payment) {
                $booking->payment()->update(['status' => 'FAILED']);
            }

            // Ubah status kamar kembali menjadi 'available'
            foreach ($booking->items as $item) {
                if ($item->room) {
                    $item->room->update(['room_status' => 'available']);
                    Log::info("Room ID {$item->room->id} dikembalikan menjadi 'available' karena booking ID {$booking->id} dibatalkan.");
                }
            }

            $canceledCount++;
        }

        if ($canceledCount > 0) {
            Log::info("AutoCancelBooking: {$canceledCount} booking dibatalkan karena melewati batas waktu yang ditentukan.");
        }

        return $next($request);
    }
}
