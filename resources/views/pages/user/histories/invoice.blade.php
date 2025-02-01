<?php

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Setting;
use App\Models\Booking;
use App\Models\Payment;
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
            'order_id' => $this->booking->order_id,
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
        'status' => 'CANCEL',
    ]);

    $booking->payment([
        'status' => 'FAILED',
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

$checkStatus = function () {
    Config::$serverKey = config('midtrans.server_key');
    Config::$isProduction = config('midtrans.is_production');
    Config::$isSanitized = true;
    Config::$is3ds = true;

    try {
        $response = \Midtrans\Transaction::status($this->booking->order_id);

        // Temukan Booking dan Payment berdasarkan order_id
        $booking = $this->booking;
        $payment = $this->booking->payment;

        if (!$booking) {
            Log::warning("Booking tidak ditemukan untuk order_id: {$response->order_id}");
            return;
        }

        // Mapping status Midtrans ke status Booking
        $bookingStatusMapping = [
            'capture' => 'PROCESS', // Pembayaran berhasil, siap diproses
            'settlement' => 'PROCESS', // Sudah lunas, booking selesai
            'pending' => 'PENDING', // Menunggu pembayaran
            'deny' => 'CANCEL', // Ditolak Midtrans
            'cancel' => 'CANCEL', // Dibatalkan pengguna/admin
            'expire' => 'CANCEL', // Kadaluarsa
            'challenge' => 'VERIFICATION', // Perlu verifikasi manual
        ];

        // Mapping status Midtrans ke status Payment
        $paymentStatusMapping = [
            'capture' => 'PAID', // Pembayaran berhasil
            'settlement' => 'PAID', // Sudah lunas
            'pending' => 'UNPAID', // Menunggu pembayaran
            'deny' => 'FAILED', // Pembayaran gagal
            'cancel' => 'FAILED', // Pembatalan pembayaran
            'expire' => 'FAILED', // Pembayaran kadaluarsa
            'challenge' => 'PROCESS', // Masih dalam pengecekan
        ];

        // Tentukan status berdasarkan response Midtrans
        $bookingStatus = $bookingStatusMapping[$response->transaction_status] ?? 'VERIFICATION'; // Default PROCESS jika status tidak dikenali
        $paymentStatus = $paymentStatusMapping[$response->transaction_status] ?? 'PROCESS'; // Default UNPAID jika status tidak dikenali

        // Update status pada Booking dan Payment
        $booking->update(['status' => $bookingStatus]);
        if ($payment) {
            if ($response->payment_type === 'credit_card') {
                $detail = 'Bank: ' . $response->bank . ', Tipe Kartu' . $response->card_type;
            } elseif ($response->payment_type === 'bank_transfer') {
                $bank = $response->va_numbers[0]->bank;
                $va_number = $response->va_numbers[0]->va_number;
                $detail = 'Bank: ' . $bank . ', VA Number: ' . $va_number;
            } elseif ($response->payment_type === 'cstore') {
                $detail = $response->store;
            } else {
                $detail = $response->payment_type;
            }

            $payment->update([
                'status' => $paymentStatus,
                'status_message' => $response->status_message,
                'gross_amount' => $response->gross_amount,
                'payment_time' => $response->settlement_time ?? $response->transaction_time,
                'payment_type' => $response->payment_type,
                'payment_detail' => $detail ?? '',
            ]);
        }

        Log::info("Booking dan Payment diperbarui: Order ID: {$response->order_id}, Booking Status: {$bookingStatus}, Payment Status: {$paymentStatus}");

        $this->redirectRoute('histories.show', [
            'booking' => $this->booking,
        ]);
    } catch (\Exception $e) {
        Log::error('Error dalam pengecekan status Midtrans: ' . $e->getMessage());

        $errorMessages = implode('<br>', $e->validator->errors()->all());

        $this->alert('error', 'Error dalam pengecekan status Midtrans! ' . '<br>' . $errorMessages, [
            'position' => 'center',
            'timer' => 4000,
            'toast' => true,
            'timerProgressBar' => true,
        ]);
    }
};

/**
 * Fungsi untuk mengembalikan label status Booking dalam bahasa Indonesia
 */
$getBookingStatusLabel = function ($status) {
    $labels = [
        'PENDING' => 'Menunggu Pembayaran',
        'CANCEL' => 'Dibatalkan',
        'PROCESS' => 'Dalam Proses',
        'CONFIRM' => 'Dikonfirmasi',
        'COMPLETE' => 'Selesai',
    ];

    return $labels[$status] ?? 'Status Tidak Diketahui';
};

/**
 * Fungsi untuk mengembalikan label status Payment dalam bahasa Indonesia
 */
$getPaymentStatusLabel = function ($status) {
    $labels = [
        'PAID' => 'Sudah Dibayar',
        'UNPAID' => 'Belum Dibayar',
        'FAILED' => 'Gagal',
    ];

    return $labels[$status] ?? 'Status Tidak Diketahui';
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
                                        wire:target='processPayment, cancelBooking, checkStatus' role="status">
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
                                            <div class="text-uppercase">{{ $booking->payment->status_message }}</div>
                                        </div>
                                        <div class="col text-end">
                                            <h5 class="fw-bold">
                                                Pembayaran
                                            </h5>
                                            <div
                                                class="text-uppercase {{ $booking->payment->status === 'UNPAID' ?: 'd-none' }}">
                                                {{ $this->getTimeRemainingAttribute() }}</div>
                                            <div class="text-uppercase">{{ $booking->payment->status }}</div>
                                            <div class="text-uppercase">{{ formatRupiah($booking->payment->gross_amount) }}
                                            </div>
                                            <div class="text-uppercase">{{ $booking->payment->payment_type }}</div>
                                            <div class="text-uppercase">{{ $booking->payment->payment_detail }}</div>
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

                            <div
                                class="gap-4
                            {{ $booking->status !== 'CANCEL' ?: 'd-none' }}
                            ">
                                <div class="row mb-3 {{ empty($snapToken) ?: 'd-none' }}">
                                    <div class="col-md">
                                        <button wire:click='cancelBooking'
                                            class="btn w-100 btn-danger btn-lg {{ empty($snapToken) ?: 'disabled' }}">Batalkan</button>
                                    </div>
                                    <div class="col-md">
                                        <button class="btn w-100 btn-primary btn-lg {{ empty($snapToken) ?: 'disabled' }}"
                                            wire:click="processPayment">Proses Pembayaran</button>
                                    </div>
                                </div>

                                <div class="col {{ !empty($snapToken) ?: 'd-none' }}">
                                    <div class="row">
                                        <div class="col-md">
                                            <button type="button" id="pay-button" href="{{ $snapToken }}"
                                                class="btn btn-light border btn-lg w-100 {{ $booking->status === 'PENDING' ?: 'disabled' }}">Lanjutkan
                                                Pembayaran</button>
                                        </div>
                                        <div class="col-md">
                                            <button
                                                class="btn btn-outline-dark btn-lg w-100 {{ $booking->payment->status === 'UNPAID' ?: 'disabled' }}"
                                                wire:click='checkStatus'>
                                                Check Status
                                            </button>
                                        </div>
                                    </div>

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
