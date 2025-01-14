<?php

use App\Models\Room;
use App\Models\Booking;
use function Livewire\Volt\{state, rules, mount};
use function Laravel\Folio\name;

name('bookings.form');

state([
    'user_id',
    'customer_name',
    'customer_contact',

    // booking
    'rooms' => fn() => Room::where('room_status', 'available')->get(),
    'check_in_date' => fn() => now()->format('Y-m-d'),
    'check_out_date' => fn() => now()->format('Y-m-d'),
    'type' => 'daily',

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

$bookingForm = function () {
    if (Auth::check() === true) {
        Booking::create([
            'user_id' => $this->user_id,
            'check_in_date' => $this->check_in_date,
            'check_out_date' => $this->check_out_date,
            'customer_name' => $this->user->name,
            'customer_contact' => $this->user->phone,
            'type' => $this->type,
        ]);
    } else {
        $this->redirectRoute('login');
    }
};

$gapMount = function () {};

?>


@volt
    <div>
        <h1>{{ $month }}</h1>
        <form id="bookingForm" wire:submit='bookingForm' class="row">

            <div class="mb-3 col-12">
                <label for="type" class="form-label">Tipe Pemesanan</label>
                <select class="form-select" wire:model.live='type' id="type" name="type" required>
                    <option value="daily">Per Hari</option>
                    <option value="monthly">Per Bulan</option>
                </select>
            </div>

            <div class="mb-3 {{ $type === 'monthly' ?: 'd-none' }}">
                <label for="month" class="form-label">Pilih Bulan</label>
                <input type="month" wire:model.live='month' class="form-control" name="month" id="month"
                    aria-describedby="helpId" placeholder="month" />
            </div>

            <div class="mb-3 col-md">
                <label for="check_in_date" class="form-label">Tanggal Check-In </label>
                <input type="date" wire:model.live='check_in_date' class="form-control" id="check_in_date"
                    name="check_in_date" min="{{ now()->format('Y-m-d') }}" placeholder="Pilih tanggal check-in" required>
            </div>
            <div class="mb-3 col-md">
                <label for="check_out_date" class="form-label">Tanggal Check-Out</label>
                <input type="date" wire:model.live='check_out_date' class="form-control" id="check_out_date"
                    name="checkoutDate" min="{{ now()->format('Y-m-d') }}" placeholder="Pilih tanggal check-out" required>
            </div>

            <div class="mb-3 col-12">
                <label for="rooms" class="form-label">
                    Jumlah Kamar
                </label>
                <select class="form-select" id="rooms" name="rooms" required>
                    @foreach ($rooms as $no => $room)
                        <option value="{{ ++$no }}">
                            {{ +$no }} Kamar
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary w-100">Pesan Sekarang</button>
            </div>
        </form>
    </div>
@endvolt
