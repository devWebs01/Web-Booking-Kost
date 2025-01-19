<?php

use App\Models\Room;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Setting;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, mount, uses};
use function Laravel\Folio\name;

uses([LivewireAlert::class]);

name('bookings.form');

state([
    'user_id',
    'check_in_date',
    'check_out_date',
    'customer_name',
    'customer_contact',
    'status',
    'type',
    'totalRooms',

    // booking
    'type' => 'daily',
    'check_out_date',
    'setting' => fn () => Setting::first(),
    'rooms' => fn() => Room::where('room_status', 'available')->get(),
    'check_in_date' => fn() => now()->format('Y-m-d'),
    'paymentAmount' => 0, // Tambahkan variabel untuk menyimpan jumlah pembayaran

    // function variabel
    'month',
]);

mount(function () {
    if (Auth::check() === true) {
        $this->user_id = Auth()->user()->id;
        $this->customer_name = Auth()->user()->name;
        $this->customer_contact = Auth()->user()->telp;
    }
});

$updated = function ($property) {
    // Jika tipe pemesanan adalah 'monthly' dan properti 'month' diperbarui
    if ($property === 'month' && $this->type === 'monthly') {
        $checkInDate = Carbon::parse($this->check_in_date);
        $this->check_out_date = Carbon::createFromDate(
            substr($this->month, 0, 4), // Tahun dari input 'month'
            substr($this->month, 5), // Bulan dari input 'month'
            $checkInDate->day, // Tanggal tetap sama seperti check-in
        )
            // ->endOfMonth()
            ->format('Y-m-d');
    }

    // Jika properti 'check_in_date' diperbarui
    if ($property === 'check_in_date') {
        $checkInDate = Carbon::parse($this->check_in_date);

        if ($this->type === 'monthly') {
            // Untuk tipe 'monthly', tanggal check-out menyesuaikan bulan berikutnya
            $this->check_out_date = $checkInDate
                ->addMonth()
                ->setDay($checkInDate->day) // Menjaga tanggal tetap sama
                ->format('Y-m-d');
        } else {
            // Untuk tipe selain 'monthly', logika default dapat diterapkan
            $this->check_out_date = $checkInDate->addDay()->format('Y-m-d');
        }
    }

    // Logika tambahan jika tipe pemesanan diubah
    if ($property === 'type' && $this->type !== 'monthly') {
        // Jika bukan tipe 'monthly', reset tanggal check-out ke default (misal, +1 hari)
        $this->check_out_date = Carbon::parse($this->check_in_date)
            ->addDay()
            ->format('Y-m-d');
    }

    if (in_array($property, ['check_in_date', 'check_out_date', 'type', 'month', 'totalRooms'])) {
        $this->calculatePaymentAmount();
    }
};

// Fungsi untuk menghitung jumlah pembayaran
$calculatePaymentAmount = function () {
    $setting = $this->setting;

    if (!$setting) {
        $this->paymentAmount = 0;
        return;
    }

    $dailyRate = $setting->daily_price;
    $monthlyRate = $setting->monthly_price;

    $totalRooms = $this->totalRooms ?? 1;

    if ($this->type === 'daily') {
        $days = Carbon::parse($this->check_in_date)->diffInDays(Carbon::parse($this->check_out_date));
        $this->paymentAmount = $dailyRate * $days * $totalRooms;
    } elseif ($this->type === 'monthly') {
        $months = Carbon::parse($this->check_in_date)->diffInMonths(Carbon::parse($this->check_out_date));
        $this->paymentAmount = $monthlyRate * $months * $totalRooms;
    }
};

rules([
    'user_id' => 'required|exists:users,id',
    'check_in_date' => 'required|date',
    'check_out_date' => 'required|date|after:check_in_date',
    'totalRooms' => 'required|integer|min:1',
    'customer_name' => 'required|string|max:255',
    'customer_contact' => 'required|string|max:255',
    'type' => 'required|string',
]);

$bookingForm = function () {
    try {
        if (Auth::check() === true) {
            $validate = $this->validate();

            if ($this->type === 'monthly' && $this->month < now()->format('Y-m')) {
                $this->alert('error', 'Bulan tidak valid!', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => true,
                ]);
                return;
            }

            // Membuat booking baru
            $booking = Booking::create($validate);

            Payment::create([
                'booking_id' => $booking->id,
                'payment_date' => now(),
                'amount' => $this->paymentAmount,
            ]);

            $this->alert('success', 'Pemesanan telah dibuat!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => true,
            ]);

            $this->redirectRoute('bookings.payment', ['booking' => $booking->id]);
        } else {
            $this->redirectRoute('login');
        }
    } catch (\Throwable $th) {
        $this->alert('error', 'Terjadi kesalahan pada proses, mohon ulangi pemesanan beberapa saat lagi!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
};

?>

@volt
    <div>

        @foreach ($errors->all() as $error)
            <p class="text-danger">{{ $error }}</p>
        @endforeach

        <form id="bookingForm" wire:submit='bookingForm' class="row">
            <div class="mb-3 col-12">
                <label for="type" class="form-label">Tipe Pemesanan</label>
                <select class="form-select" wire:model.live='type' id="type" name="type" required>
                    <option value="daily">Per Hari</option>
                    <option value="monthly">Per Bulan</option>
                </select>
            </div>

            <div class="mb-3 {{ $type === 'monthly' ? '' : 'd-none' }}">
                <label for="month" class="form-label">Pilih Bulan</label>
                <input type="month" wire:model.live='month' class="form-control" name="month" id="month"
                    placeholder="month" min="{{ now()->format('Y-m') }}" />
            </div>

            <div class="mb-3 col-md">
                <label for="check_in_date" class="form-label">Tanggal Check-In </label>
                <input type="date" wire:model.live='check_in_date' class="form-control" id="check_in_date"
                    name="check_in_date" min="{{ now()->format('Y-m-d') }}" required>
            </div>

            <div class="mb-3 col-md">
                <label for="check_out_date" class="form-label">Tanggal Check-Out</label>
                <input type="date" wire:model.live='check_out_date' class="form-control" id="check_out_date"
                    name="checkoutDate" min="{{ Carbon::parse($check_in_date)->addDays(1)->format('Y-m-d') }}" required
                    {{ $type !== 'monthly' ?: 'readonly' }}>
            </div>

            <!-- Jumlah Pembayaran -->
            <div class="mb-3 col-12">
                <label for="payment_amount" class="form-label">Jumlah Pembayaran</label>
                <input type="text" class="form-control" id="payment_amount" name="payment_amount"
                    value="{{ formatRupiah($paymentAmount) }}" readonly>
            </div>

            <div class="mb-3 col-12">
                <label for="rooms" class="form-label">Jumlah Kamar</label>
                <select wire:model.live="totalRooms" class="form-select" id="rooms" name="rooms" required>
                    @foreach ($rooms as $no => $room)
                        <option value="{{ ++$no }}">{{ $no }} Kamar</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary w-100">Pesan Sekarang</button>
            </div>
        </form>
    </div>
@endvolt
