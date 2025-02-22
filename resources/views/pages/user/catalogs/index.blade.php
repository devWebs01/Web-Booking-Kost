<?php

use App\Models\Image;
use App\Models\Room;
use App\Models\Facility;
use App\Models\Setting;
use App\Models\Cart;
use Carbon\Carbon;
use function Livewire\Volt\{state, uses, computed, mount, on};
use function Laravel\Folio\name;
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

name('catalogs.index');

state([])->url();

$rooms = computed(function () {
    return Room::get();
});

state([
    'galleries' => fn() => Image::get(),
    'facilities' => fn() => Facility::get(),
    //
    'user_id' => fn() => Auth()->user()->id ?? '',
    'setting' => fn() => Setting::first(),
    'rooms' => fn() => Room::get(),
    //
]);

state([
    'now' => fn() => Carbon::now()->format('Y-m-d'),
    'selectedRooms' => [],
    'booking_type' => 'daily',
    'total_price' => 0,
    'check_in_date',
    'check_out_date',
]);

$selectRoom = function ($roomId) {
    if (in_array($roomId, $this->selectedRooms)) {
        // Jika kamar sudah dipilih, hapus dari daftar
        $this->selectedRooms = array_diff($this->selectedRooms, [$roomId]);
        $this->dispatch('updateTotalPrice');
    } else {
        // Jika belum dipilih, tambahkan ke daftar
        $this->selectedRooms[] = $roomId;
        $this->dispatch('updateTotalPrice');
    }
};

on([
    'updateTotalPrice' => function () {
        if (!$this->check_in_date || !$this->check_out_date) {
            $this->total_price = 0;
            return;
        } elseif (!Auth()->check()) {
            $this->redirectRoute('login');
            return;
        }

        $daily_price = $this->setting->daily_price;
        $monthly_price = $this->setting->monthly_price;
        $getRooms = count($this->selectedRooms) ?? 0;

        $checkIn = Carbon::parse($this->check_in_date);

        if ($this->booking_type === 'daily') {
            $checkOut = Carbon::parse($this->check_out_date);
            $duration = $checkIn->diffInDays($checkOut);
        } else {
            // Ambil tanggal dari check_in_date
            $day = $checkIn->day;

            // Gabungkan bulan dan tahun dari check_out_date dengan tanggal dari check_in_date
            $checkOut = Carbon::parse($this->check_out_date . '-' . $day);

            // Pastikan tidak melebihi jumlah hari dalam bulan yang dipilih user
            if ($checkOut->day != $day) {
                $checkOut = $checkOut->endOfMonth(); // Jika tidak valid, set ke akhir bulan
            }

            $duration = $checkIn->diffInMonths($checkOut);
        }

        $this->total_price = $duration * ($this->booking_type === 'daily' ? $daily_price : $monthly_price) * $getRooms;
    },
]);

$submitBooking = function () {
    // Validasi input
    if (!$this->check_in_date || !$this->check_out_date || empty($this->selectedRooms)) {
        $this->alert('error', 'Silakan lengkapi semua data pemesanan.', [
            'position' => 'center',
            'timer' => 2000,
            'toast' => true,
            'timerProgressBar' => true,
        ]);
        return;
    }

    // Ambil data yang diperlukan
    $userId = $this->user_id;
    $bookingType = $this->booking_type;
    $totalPrice = $this->total_price;

    // Konversi check_in_date menjadi Carbon
    $checkIn = Carbon::parse($this->check_in_date);
    $checkOut = Carbon::parse($this->check_out_date);

    if ($bookingType === 'monthly') {
        $day = $checkIn->day;

        // Gabungkan bulan dan tahun dari check_out_date dengan tanggal dari check_in_date
        $checkOut = Carbon::parse($this->check_out_date . '-' . $day);

        // Pastikan tidak melebihi jumlah hari dalam bulan yang dipilih user
        if ($checkOut->day != $day) {
            $checkOut = $checkOut->endOfMonth();
        }
    }

    // Buat entri di tabel bookings
    $booking = \App\Models\Booking::create([
        'user_id' => $userId,
        'booking_type' => $bookingType,
        'check_in_date' => $checkIn->format('Y-m-d'),
        'check_out_date' => $checkOut->format('Y-m-d'),
        'price' => $totalPrice,
        'order_id' => 'INV-' . rand(),
        'expired_at' => Carbon::now()->addMinutes($this->setting->expire_time),
    ]);

    // Simpan item kamar yang dipilih
    foreach ($this->selectedRooms as $roomId) {
        \App\Models\Item::create([
            'booking_id' => $booking->id,
            'room_id' => $roomId,
        ]);
    }

    $this->alert('success', 'Proses berhasil! Silahkan lanjut untuk proses pembayaran', [
        'position' => 'center',
        'timer' => 2000,
        'toast' => true,
        'timerProgressBar' => true,
    ]);
};


?>

<x-guest-layout>
    <x-slot name="title">Katalog Kamar</x-slot>

    @volt
        <div class="container">
            @foreach ($errors->all() as $item)
                <p>{{ $item }}</p>
            @endforeach

            @include('pages.user.catalogs.detailKost')

            <section class="mt-5">
                <div class="row">
                    <div class="col-md">
                        <h3 class="fw-bold">
                            {{ $setting->name }}
                        </h3>
                        <p>
                            {{ $setting->description }}
                        </p>

                        <p class="fw-bold">Fasilitas</p>
                        <ol>
                            @foreach ($facilities as $facility)
                                <li>
                                    {{ $facility->name }}
                                </li>
                            @endforeach
                        </ol>

                        <p class="fw-bold">Lantai Bawah</p>
                        <hr>
                        <div class="row">
                            @foreach ($rooms->where('position', 'up') as $roomUp)
                                <div class="col-md-3 mb-3">
                                    <button wire:click="selectRoom({{ $roomUp->id }})"
                                        class="btn w-100 py-3 
                                        {{ in_array($roomUp->id, $selectedRooms) ? 'btn-danger text-white' : 'btn-outline-primary' }}">
                                        {{ $roomUp->number }}
                                    </button>
                                </div>
                            @endforeach
                        </div>


                        <!-- Kamar Lantai Bawah -->
                        <p class="fw-bold">Lantai Bawah</p>
                        <hr>
                        <div class="row">
                            @foreach ($rooms->where('position', 'down') as $roomDown)
                                <div class="col-md-3 mb-3">
                                    <button wire:click="selectRoom({{ $roomDown->id }})"
                                        class="btn w-100 py-3 
                                        {{ in_array($roomDown->id, $selectedRooms) ? 'btn-danger text-white' : 'btn-outline-primary' }}">
                                        {{ $roomDown->number }}
                                    </button>
                                </div>
                            @endforeach
                        </div>

                    </div>
                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-body">
                                <form wire:submit="submitBooking">

                                    @if (!empty($selectedRooms))
                                        <div class="alert alert-success mt-3 text-center">
                                            Kamu telah memilih kamar:
                                            <strong>{{ implode(', ', App\Models\Room::whereIn('id', $selectedRooms)->pluck('number')->toArray()) }}</strong>

                                            <p>{{ $check_in_date }} - {{ $check_out_date }}</p>
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <label for="booking_type" class="form-label">Tipe</label>
                                        <select wire:model.live='booking_type' class="form-select" name="booking_type"
                                            id="booking_type">
                                            <option selected>Select one</option>
                                            <option value="daily">Harian - {{ formatRupiah($setting->daily_price) }}
                                            </option>
                                            <option value="monthly">Bulanan - {{ formatRupiah($setting->monthly_price) }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="check_in_date" class="form-label">Check In</label>
                                        <input wire:model.live="check_in_date" wire:change="dispatch('updateTotalPrice')"
                                            type="date" class="form-control" min="{{ $now }}"
                                            name="check_in_date" id="check_in_date" />
                                    </div>

                                    <div class="mb-3">
                                        <label for="check_out_date" class="form-label">Check Out</label>
                                        <input wire:model.live="check_out_date" wire:change="dispatch('updateTotalPrice')"
                                            type="{{ $booking_type === 'daily' ? 'date' : 'month' }}" class="form-control"
                                            min="{{ $booking_type === 'daily'
                                                ? Carbon::parse($check_in_date ?? now())->addDay()->format('Y-m-d')
                                                : Carbon::parse($check_in_date ?? now())->addMonth()->format('Y-m') }}"
                                            name="check_out_date" id="check_out_date" />
                                    </div>

                                    <div class="mb-3">
                                        <label for="total_price" class="form-label">Total</label>
                                        <input type="text" class="form-control" name="total_price"
                                            value="{{ formatRupiah($total_price) }}" id="total_price" readonly />
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="submit" class="btn btn-primary">
                                            Submit
                                        </button>
                                        <div wire:loading="submitBooking" class="d-nonw" Wire:loading.remove.class="d-none">
                                            <div class="spinner-border spinner-border-sm" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <div class="spinner-grow spinner-grow-sm" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @endvolt
</x-guest-layout>
