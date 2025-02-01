<?php

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Setting;
use Carbon\Carbon;
use function Livewire\Volt\{state, on};
use function Laravel\Folio\name;
use function Laravel\Folio\{middleware};

middleware(['auth']);

name('histories.show');

state([
    'setting' => fn() => Setting::first(),
    'user' => fn() => Auth()->user(),
    'snapToken' => fn() => $this->booking->snapToken ?? '',
    'expired_at' => fn() => $this->booking->expired_at ?? '',
    'booking',
]);

on([
    'updateSnapToken' => function () {
        $this->snapToken = $this->booking->snapToken;
    },
]);

$processPayment = function () {
    Config::$serverKey = config('midtrans.server_key');
    Config::$isProduction = config('midtrans.is_production');
    Config::$isSanitized = true;
    Config::$is3ds = true;

    // Data transaksi
    $params = [
        'transaction_details' => [
            'order_id' => rand(),
            'gross_amount' => $this->booking->total,
        ],
        'customer_details' => [
            'first_name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->user->telp,
        ],
    ];

    try {
        $snapToken = Snap::getSnapToken($params);

        // Simpan snapToken ke dalam booking
        $this->booking->update(['snapToken' => $snapToken]);

        // Dispatch event untuk update snapToken di state
        $this->dispatch('updateSnapToken');

        $this->redirectRoute('histories.show', [
            'booking' => $this->booking,
        ]);
    } catch (\Exception $e) {
        \Log::error('Payment Error: ' . $e->getMessage());
    }
};

$cancelBooking = function () {
    $booking = $this->booking;

    $booking->update([
        'status' => 'canceled',
    ]);

    $booking->payment([
        'status' => 'failed',
    ]);

    $this->redirectRoute('histories.index');
};

$getTimeRemainingAttribute = function () {
    $now = Carbon::now();
    $expiry = Carbon::parse($this->expired_at);

    if ($expiry->isPast()) {
        return 'Expired';
    }

    $diffInSeconds = $expiry->diffInSeconds($now);
    $minutes = floor($diffInSeconds / 60);
    $seconds = $diffInSeconds % 60;

    return "{$minutes}m {$seconds}s";
};

?>

<x-guest-layout>
    <x-slot name="title">Pembayaran</x-slot>

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
                                    <div class="d-none spinner-border" wire:loading.class.remove="d-none"
                                        wire:target='processPayment, cancelBooking' role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col text-end">
                                <button type="button" class="btn btn-dark mb-3 d-print-none" id="printInvoiceBtn">Download
                                    Invoice</button>

                                <script>
                                    document.getElementById('printInvoiceBtn').addEventListener('click', function() {
                                        window.print(); // Fungsi bawaan browser untuk mencetak halaman
                                    });
                                </script>
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
                                                <th class="text-start">Check-in</th>
                                                <th>Check-out</th>
                                                <th>Kamar</th>
                                                <th>Tipe Pemesanan</th>
                                                <th class="text-end">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody style="vertical-align: middle">
                                            @foreach ($booking->items as $item)
                                                <tr>

                                                    <td class="text-start">
                                                        {{ Carbon::parse($item->check_in_date)->format('d M Y') }}
                                                    </td>

                                                    <td>
                                                        {{ Carbon::parse($item->check_out_date)->format('d M Y') }}
                                                    </td>

                                                    <td>
                                                        Kamar {{ $item->room->number }}
                                                    </td>

                                                    <td>{{ __('type.' . $item->type) }}</td>

                                                    <td class="text-end">
                                                        {{ formatRupiah($item->price) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div @if (now()->lessThan(\Carbon\Carbon::parse($expired_at))) wire:poll.1s @endif>

                            <!-- Invoice Summary -->
                            <div class="card border-dark my-4">
                                <div class="card-body bg-light">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="fw-bold">
                                                Pemesanan
                                            </h5>

                                            <div>{{ $user->name }}</div>
                                            <div>{{ Carbon::parse($booking->created_at)->format('d-m-Y h:i:s') }}</div>
                                            <div class="text-uppercase">{{ $booking->status }}</div>
                                        </div>
                                        <div class="col text-end">
                                            <h5 class="fw-bold">
                                                Pembayaran
                                            </h5>
                                            <div class="text-uppercase">{{ $this->getTimeRemainingAttribute() }}</div>
                                            <div class="text-uppercase">{{ $booking->payment->status }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body bg-light d-flex justify-content-between">

                                    <strong class="text-dark fs-4">
                                        Total Pembayaran
                                    </strong>
                                    <strong class="text-dark fs-4">
                                        {{ formatRupiah($booking->total) }}
                                    </strong>
                                </div>
                            </div>




                            <div class="gap-4 {{ $booking->status !== 'canceled' ?: 'd-none' }}">
                                <div class="row">
                                    <div class="col-md">
                                        <button wire:click='cancelBooking'
                                            class="btn w-100 btn-danger btn-lg {{ empty($snapToken) ?: 'disabled' }}">Batalkan</button>
                                    </div>
                                    <div class="col-md">
                                        <button class="btn w-100 btn-primary btn-lg {{ empty($snapToken) ?: 'disabled' }}"
                                            wire:click="processPayment">Proses Pembayaran</button>
                                    </div>
                                </div>

                                <div class="row">
                                    <button type="button" id="pay-button" href="{{ $snapToken }}"
                                        class="btn btn-primary btn-lg {{ !empty($snapToken) ?: 'd-none' }}">Lanjutkan
                                        Pembayaran</button>
                                </div>
                            </div>

                        </div>


                    </div>
                </div>
            </div>

            <div class="{{ !empty($snapToken) ?: 'd-none' }}">

                @push('styles')
                    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
                        data-client-key="SB-Mid-server-yh7So1dkVPwD99Z4icKqvCX4"></script>
                @endpush

                @if (!empty($snapToken))
                    <script type="text/javascript">
                        document.addEventListener('DOMContentLoaded', function() {
                            var payButton = document.getElementById('pay-button');
                            if (payButton) {
                                payButton.addEventListener('click', function() {
                                    window.snap.pay(@json($snapToken), {
                                        onSuccess: function(result) {
                                            alert("Payment success!");
                                            console.log(result);
                                            location.reload(); // Refresh halaman setelah sukses
                                        },
                                        onPending: function(result) {
                                            alert("Waiting for your payment!");
                                            console.log(result);
                                            location.reload(); // Refresh halaman setelah pending
                                        },
                                        onError: function(result) {
                                            alert("Payment failed!");
                                            console.log(result);
                                            location.reload(); // Refresh halaman setelah gagal
                                        },
                                        onClose: function() {
                                            alert('You closed the popup without finishing the payment');
                                        }
                                    });
                                });
                            }
                        });
                    </script>
                @endif

            </div>

        </div>
    @endvolt


</x-guest-layout>
