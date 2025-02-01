<?php

use function Livewire\Volt\{state, rules, on, uses};
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Setting;
use App\Models\Booking;
use App\Models\Item;
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

        $booking = Booking::create([
            'user_id' => Auth::user()->id,
            'total' => $total,
        ]);

        foreach ($this->carts as $cart) {
            Item::create([
                'booking_id' => $booking->id,
                'room_id' => $cart->room->id,
                'check_in_date' => $cart->check_in_date,
                'check_out_date' => $cart->check_out_date,
                'type' => $cart->type,
                'price' => $cart->price,
            ]);
        }

        Payment::create([
            'booking_id' => $booking->id,
            'expired_at' => now()->addMinutes(1),
            'amount' => $total,
            'receipt' => null,
        ]);

        Cart::where('user_id', Auth::user()->id)->delete();

        $this->dispatch('cart-updated');

        $this->alert('success', 'Proses berhasil! Silahkan lanjut untuk proses pembayaran', [
            'position' => 'center',
            'timer' => 2000,
            'toast' => true,
            'timerProgressBar' => true,
        ]);

        DB::commit(); // Pastikan ini sebelum redirect

        $this->redirectRoute('histories.show', [
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
                        <!-- Header -->
                        <div class="row align-items-center mb-4">
                            <div class="col">
                                <a href="#" class="text-primary">
                                    <span class="display-5 fw-bold">
                                        {{ $setting->name }}
                                    </span>
                                    <div class="d-none spinner-border" wire:loading.class.remove="d-none" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col text-end text-muted small">
                                <div>{{ $user->name }}</div>
                                <div>{{ $user->email }}</div>
                                <div>{{ $user->telp }}</div>
                            </div>
                        </div>

                        <!-- Invoice Details -->
                        <div class="card border-dark bg-light">
                            <div class="card-body">
                                <h3 class="border-bottom pb-2 mb-4 fw-bold">Detail Pemesanan Kamar</h3>
                                <div class="table-responsive">
                                    <table class="table text-center rounded text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Check-in</th>
                                                <th>Check-out</th>
                                                <th>Jumlah Kamar</th>
                                                <th>Tipe Pemesanan</th>
                                                <th>Jumlah yang dibayarkan</th>
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
