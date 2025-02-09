<?php

namespace App\Http\Middleware;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Closure;

class AutoCancelBooking
{
    public function handle(Request $request, Closure $next)
    {
        $expire_time = 5;

        // Cari booking yang statusnya 'PENDING' dan telah melewati batas waktu
        $expiredBookings = Booking::where('status', 'PENDING')
            ->where('created_at', '<=', now()->subMinutes($expire_time))
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
