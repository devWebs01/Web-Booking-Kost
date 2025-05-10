<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Require authentication.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the dashboard page with recent bookings and chart data.
     */
    public function index()
    {
        // Ambil 10 booking terbaru
        $recentBookings = Booking::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Hitung jumlah booking dalam 30 hari terakhir per tanggal
        $bookingCounts = Booking::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as count')
        )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('home', [
            'recentBookings' => $recentBookings,
            'bookingCounts' => $bookingCounts,
        ]);
    }
}
