<?php

use function Livewire\Volt\{state, on, uses};
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Setting;
use App\Models\Booking;
use App\Models\Item;
use App\Models\Room;
use App\Models\Payment;
use function Laravel\Folio\name;
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

name('catalogs.cart');

state([
    'carts' => fn() => Cart::where('user_id', auth()->id())->get(),
    'setting' => fn() => Setting::first(),
    'user' => fn() => Auth()->user(),
]);

on([
    'cart-updated' => function () {
        $this->cart = $this->carts;
    },
]);

$calculateTotal = function () {
    $total = 0;
    foreach ($this->carts as $cart) {
        $total += $cart->price;
    }
    return $total;
};

$deleteRoom = function ($cartId) {
    $cart = Cart::find($cartId);
    $cart->delete();
    $this->dispatch('cart-updated');
};

$ConfirmBooking = function () {
    DB::beginTransaction();

    try {
        if (!Auth::check()) {
            Log::error('User not authenticated');
            throw new \Exception('User tidak terautentikasi');
        }

        if ($this->carts->isEmpty()) {
            Log::error('Cart is empty for user: ' . Auth::user()->id);
            throw new \Exception('Keranjang kosong');
        }

        $total = $this->calculateTotal();
        if ($total <= 0) {
            Log::error('Total booking is invalid');
            throw new \Exception('Total tidak valid');
        }

        // Membuat booking dengan order_id unik
        $booking = Booking::create([
            'user_id' => Auth::user()->id,
            'total' => $total,
            'order_id' => uniqid('ORD-'), // Gunakan uniqid untuk menghindari duplikasi
        ]);

        $items = [];

        foreach ($this->carts as $cart) {
            if (!$cart->room) {
                Log::error('Room not found for cart ID: ' . $cart->id);
                throw new \Exception('Kamar tidak ditemukan dalam keranjang');
            }

            // Lock kamar dengan lebih ketat untuk mencegah race condition
            $room = Room::where('id', $cart->room->id)->lockForUpdate()->first();

            // Periksa kembali apakah kamar masih tersedia
            if ($room->room_status === 'booked') {
                Log::warning('Room ID ' . $room->id . ' already booked by another user.');
                throw new \Exception('Kamar sudah dibooking oleh pengguna lain');
            }

            // Tambahkan item booking ke dalam array untuk batch insert
            $items[] = [
                'booking_id' => $booking->id,
                'room_id' => $cart->room->id,
                'check_in_date' => $cart->check_in_date,
                'check_out_date' => $cart->check_out_date,
                'type' => $cart->type,
                'price' => $cart->price,
            ];

            // Perbarui status kamar menjadi 'booked'
            $room->update(['room_status' => 'booked']);

            Log::info('Room ID ' . $room->id . ' successfully booked by User ID: ' . Auth::id());
        }

        // Insert semua item booking sekaligus untuk efisiensi
        Item::insert($items);

        // Buat entri pembayaran
        Payment::create([
            'booking_id' => $booking->id,
        ]);

        // Hapus semua item di keranjang pengguna setelah booking sukses
        Cart::where('user_id', Auth::user()->id)->delete();

        $this->dispatch('cart-updated');

        $this->alert('success', 'Proses berhasil! Silahkan lanjut untuk proses pembayaran', [
            'position' => 'center',
            'timer' => 2000,
            'toast' => true,
            'timerProgressBar' => true,
        ]);

        DB::commit(); // Pastikan transaksi selesai sebelum redirect

        return $this->redirectRoute('histories.show', [
            'booking' => $booking,
        ]);
    } catch (\Exception $e) {
        DB::rollback();

        Log::error('Booking error: ' . $e->getMessage());

        $this->alert('error', 'Proses gagal! Terjadi kesalahan dalam proses konfirmasi', [
            'position' => 'center',
            'timer' => 2000,
            'toast' => true,
            'timerProgressBar' => true,
        ]);
    }
};


?>

<x-guest-layout>
    <x-slot name="title">Keranjang Belanja</x-slot>

    @volt
        <div>
            <div class="container">
                <!-- Menampilkan Detail Pemesanan Kamar -->

                <div class="text-dark w-100 h-100 py-4">
                    <div class="container-fluid">

                        <!-- Invoice Details -->
                        <div class="card border-dark bg-light">
                            <div class="card-body">
                                <h3 class="border-bottom pb-2 mb-4 fw-bold">
                                    Detail Pemesanan Kamar
                                    <span class="text-center">
                                        <div class="d-none spinner-border fw-normal" wire:loading.class.remove="d-none" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </span>
                                </h3>
                                <div class="table-responsive">
                                    <table class="table text-center rounded text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Check-in</th>
                                                <th>Check-out</th>
                                                <th>Jumlah Kamar</th>
                                                <th>Tipe Pemesanan</th>
                                                <th>Lama Menginap</th>
                                                <th>Jumlah</th>
                                                <th>Opsi</th>
                                            </tr>
                                        </thead>
                                        <tbody style="vertical-align: middle">
                                            @foreach ($carts as $cart)
                                                <tr>

                                                    <td>
                                                        {{ Carbon::parse($cart->check_in_date)->format('d M Y') }}
                                                    </td>

                                                    <td>
                                                        {{ Carbon::parse($cart->check_out_date)->format('d M Y') }}
                                                    </td>

                                                    <td>
                                                        Kamar {{ $cart->room->number }}
                                                    </td>

                                                    <td>{{ __('type.' . $cart->type) }}</td>

                                                    <td>
                                                        {{ Carbon::parse($cart->check_in_date)->diffInDays(Carbon::parse($cart->check_out_date)) }}
                                                        malam
                                                    </td>

                                                    <td>
                                                        {{ formatRupiah($cart->price) }}
                                                    </td>
                                                    <td>
                                                        <button wire:click="deleteRoom('{{ $cart->id }}')"
                                                            type="button" class="border-0">
                                                            <i class='bx bx-x fs-3 rounded bg-danger text-white'></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Summary -->
                        <div class="card border-dark my-4">
                            <div class="card-body bg-light d-flex justify-content-between">
                                <strong class="text-dark fs-4">
                                    Total Pembayaran
                                </strong>
                                <strong class="text-dark fs-4">
                                    {{ formatRupiah($this->calculateTotal()) }}
                                </strong>
                            </div>
                        </div>

                        <div class="row gap-3">
                            <a class="col-md mb-3 btn btn-outline-primary" href="{{ route('catalogs.index') }}">
                                Cek Kamar Lain
                            </a>
                            <button wire:click='ConfirmBooking' class="col-md mb-3 btn btn-primary btn-lg"
                                {{ $carts->count() > 0 ?: 'disabled' }}>
                                Konfirmasi
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    @endvolt
</x-guest-layout>
