<?php

use App\Models\Room;
use App\Models\Image;
use App\Models\Setting;
use Carbon\Carbon;
use function Livewire\Volt\{state, rules, computed};
use function Laravel\Folio\name;
use function Laravel\Folio\{middleware};

middleware(['auth']);

name('bookings.payment');

state([
    'setting' => fn() => Setting::first(['name', 'location', 'description']),
    'payment' => fn() => $this->booking->payment,
    'booking',
]);

?>

<x-guest-layout>
    <x-slot name="title">Pembayaran</x-slot>

    @volt
        <div>

            <div class="container">
                <!-- Menampilkan Detail Pemesanan Kamar -->

                <div class="bg-light text-dark w-100 h-100 py-4">
                    <div class="container-fluid">
                        <!-- Header -->
                        <div class="row align-items-center mb-4">
                            <div class="col">
                                <a href="#" class="text-primary">
                                    <span class="display-5 fw-bold">
                                        {{ $setting->name }}
                                    </span>
                                </a>
                            </div>
                            <div class="col text-end text-muted small">
                                <div>{{ $booking->customer_name }}</div>
                                <div>{{ $booking->customer_contact }}</div>
                                <div>{{ $booking->order_id }}</div>
                                <div>{{ $booking->created_at }}</div>
                            </div>
                        </div>

                        <!-- Invoice Summary -->
                        <div class="card border-dark mb-4">
                            <div class="card-body bg-light d-flex justify-content-between">
                                <strong class="text-dark fs-4">
                                    {{ formatRupiah($payment->amount) }}
                                </strong>
                                <span class="text-muted text-uppercase">{{ $payment->status }}</span>
                            </div>
                        </div>

                        <!-- Invoice Details -->
                        <div class="card border-dark">
                            <div class="card-body">
                                <h3 class="border-bottom pb-2 mb-4 fw-bold">Detail Pemesanan Kamar</h3>
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td>Check-in:</td>
                                            <td class="text-end">
                                                {{ Carbon::parse($booking->check_in_date)->format('d M Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Check-out:</td>
                                            <td class="text-end">
                                                {{ Carbon::parse($booking->check_out_date)->format('d M Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Jumlah Kamar:</td>
                                            <td class="text-end">
                                                {{ $booking->totalRooms }} Kamar</td>
                                        </tr>
                                        <tr>
                                            <td>Tipe Pemesanan</td>
                                            <td class="text-end">{{ $booking->type }}</td>
                                        </tr>
                                        <tr class="fw-bold border-top border-bottom">
                                            <td>Jumlah yang dibayarkan</td>
                                            <td class="text-end"> {{ formatRupiah($payment->amount) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endvolt


</x-guest-layout>
