<?php

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Setting;
use Carbon\Carbon;
use function Livewire\Volt\{state, on, uses};
use function Laravel\Folio\name;
use function Laravel\Folio\{middleware};
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);
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
        'expiry' => [
            'start_time' => $this->booking->expired_at ? Carbon::parse($this->booking->expired_at)->format('Y-m-d H:i:s O') : Carbon::now()->format('Y-m-d H:i:s O'),
            'unit' => 'minutes',
            'duration' => $this->booking->expired_at ? Carbon::now()->diffInMinutes(Carbon::parse($this->booking->expired_at)) : 5, // Menghitung durasi kedaluwarsa dalam menit
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

    $booking->payment->update([
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

        // dd($response);

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
            'pending' => 'PROCESS', // Menunggu pembayaran
            'deny' => 'FAILED', // Pembayaran gagal
            'cancel' => 'FAILED', // Pembatalan pembayaran
            'expire' => 'FAILED', // Pembayaran kadaluarsa
            'challenge' => 'VERIFICATION', // Masih dalam pengecekan
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

        if ($e instanceof ValidationException) {
            $errorMessages = implode('<br>', $e->validator->errors()->all());
        } else {
            $errorMessages = 'Terjadi kesalahan pada sistem. Silakan coba lagi.';
        }

        $this->alert('error', 'Error dalam pengecekan status Midtrans! <br>' . $errorMessages, [
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
        <div class="container-fluid">

            <div class="container">
                <!-- Menampilkan Detail Pemesanan Kamar -->

                <div class="alert alert-danger {{ $booking->payment->status === 'UNPAID' ?: 'd-none' }}" role="alert">
                    <strong>
                        Mohon untuk menyelesaikan pembayaran sebelum waktu habis dalam
                        {{ $this->getTimeRemainingAttribute() }}
                    </strong>
                </div>


                <div class="card py-4">
                    <section class="card-header bg-body border-0 mb-3">
                        <button type="button" class="btn btn-dark w-100 btn-lg d-print-none" id="printInvoiceBtn">Download
                            Invoice</button>

                        <script>
                            document.getElementById('printInvoiceBtn').addEventListener('click', function() {
                                window.print(); // Fungsi bawaan browser untuk mencetak halaman
                            });
                        </script>
                    </section>

                    <div class="card-body">

                        <section class="row align-items-center mb-4">
                            <div class="col">
                                <span class="display-6 fw-bold text-primary text-uppercase">
                                    {{ $booking->order_id }}
                                </span>
                                <div class="d-none spinner-border" wire:loading.class.remove="d-none"
                                    wire:target='processPayment, cancelBooking, checkStatus' role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                            <div class="col text-end">

                                <button class="btn btn-light">
                                    <span class="display-6 fw-bold">
                                        {{ $booking->status }}
                                    </span>
                                </button>

                            </div>
                        </section>

                        <section class="mb-5">
                            <div class="row">
                                <div class="col">
                                    <h5 class="fw-bold">
                                        Pemesanan
                                    </h5>

                                    <div>{{ $user->name }}</div>
                                    <div class="text-uppercase">{{ __('booking.' . $booking->status) }}</div>
                                    <div>{{ Carbon::parse($booking->created_at)->format('d-m-Y h:i:s') }}</div>
                                    <div class="text-uppercase">{{ $booking->payment->status_message }}</div>
                                </div>
                                <div class="col text-end">
                                    <h5 class="fw-bold">
                                        Pembayaran
                                    </h5>

                                    <div class="text-uppercase">{{ __('payment.' . $booking->payment->status) }}</div>
                                    <div class="text-uppercase">{{ formatRupiah($booking->payment->gross_amount) }}
                                    </div>
                                    <div class="text-uppercase">{{ $booking->payment->payment_type }}</div>
                                    <div class="text-uppercase">{{ $booking->payment->payment_detail }}</div>
                                </div>
                            </div>
                        </section>

                        <!-- Invoice Details -->
                        <section class="mb-5">
                            <h5 class="fw-bold">
                                Detail Pemesanan Kamar</h3>
                                <div class="table-responsive">
                                    <table class="table text-center rounded text-nowrap">
                                        <thead>
                                            <tr class="text-dark">
                                                <th class="text-start">Check-in</th>
                                                <th>Check-out</th>
                                                <th>Kamar</th>
                                                <th>Tipe Pemesanan</th>
                                                <th>Lama Menginap</th>
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

                                                    <td>
                                                        {{ Carbon::parse($item->check_in_date)->diffInDays(Carbon::parse($item->check_out_date)) }}
                                                        malam
                                                    </td>

                                                    <td class="text-end">
                                                        {{ formatRupiah($item->price) }}
                                                    </td>
                                                </tr>
                                            @endforeach

                                            <tr>
                                                <td colspan="4"></td>
                                                <td class="h5 fw-bold">
                                                    Total
                                                </td>
                                                <td class="h5 fw-bold text-end">
                                                    {{ formatRupiah($booking->total) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                        </section>

                    </div>
                </div>
            </div>

            <section class="container my-5" @if (now()->lessThan(\Carbon\Carbon::parse($expired_at))) wire:poll.5s @endif>

                <div
                    class="gap-4
                            {{ $booking->status !== 'CANCEL' ?: 'd-none' }}
                            ">
                    <div class="row mb-3 {{ empty($snapToken) ?: 'd-none' }}">
                        <div class="col-md">
                            <button wire:click='cancelBooking'
                                class="btn w-100 btn-outline-danger btn-lg {{ empty($snapToken) ?: 'disabled' }}">Batalkan</button>
                        </div>
                        <div class="col-md">
                            <button class="btn w-100 btn-outline-success btn-lg {{ empty($snapToken) ?: 'disabled' }}"
                                wire:click="processPayment">Proses Pembayaran</button>
                        </div>
                    </div>

                    <div class="col {{ !empty($snapToken) ?: 'd-none' }}">
                        <div class="row">
                            <div class="col-md">
                                <button type="button" id="pay-button" href="{{ $snapToken }}"
                                    class="btn btn-light border btn-lg w-100 {{ $booking->status === 'PENDING' ?: 'd-none' }}">Lanjutkan
                                    Pembayaran</button>
                            </div>
                            <div class="col-md">
                                <button
                                    class="btn btn-outline-dark btn-lg w-100 {{ $booking->payment->status === 'UNPAID' ?: 'd-none' }}"
                                    wire:click='checkStatus'>
                                    Check Status
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

            </section>

            @push('styles')
                <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
                    data-client-key="SB-Mid-server-yh7So1dkVPwD99Z4icKqvCX4"></script>
            @endpush

            @push('scripts')
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
            @endpush

        </div>
    @endvolt


</x-guest-layout>
