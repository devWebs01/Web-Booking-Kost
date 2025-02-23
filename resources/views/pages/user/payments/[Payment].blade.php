<?php

use Midtrans\Snap;
use Midtrans\Config;
use Carbon\Carbon;
use function Livewire\Volt\{state, on, uses};
use function Laravel\Folio\name;
use function Laravel\Folio\{middleware};
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);
middleware(['auth']);

name('payments.guest');

state([
    'booking' => fn() => $this->payment->booking,
    'user' => fn() => Auth()->user(),
    'snapToken' => fn() => $this->booking->snapToken,
    'expired_at' => fn() => $this->booking->expired_at,
    'payment',
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

$updateStatus = function () {
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
        \Log::error('Payment Error: ' . $e->getMessage());


        $this->alert('error', 'Error dalam pengecekan status Midtrans!', [
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
                <section>
                    <div class="alert alert-primary alert-dismissible fade show" role="alert">
                        <strong>Setelah pembayaran selesai!</strong> Silakan perbarui status pembayaran dengan mengklik
                        tombol di
                        bawah ini.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </section>

                <!-- Menampilkan Detail Pemesanan Kamar -->

                <div class="row">
                    <div class="col-md">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6>Total Pembayaran:</h6>
                                <div class="row align-items-center justify-content-between">
                                    <div class="col-auto">
                                        <h1 class="text-primary fw-bold">
                                            {{ formatRupiah($booking->price) }}
                                        </h1>
                                    </div>

                                    <div class="col-auto text-end">
                                        <div wire:loading class="spinner-border spinner-border-sm ms-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>

                                    <div class="my-3">
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                Status
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ __('payment.' . $payment->status) }}
                                            </div>
                                            <div class="col-6">
                                                Jumlah harus dibayar
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ formatRupiah($booking->price) }}
                                            </div>
                                            <div class="col-6">
                                                Jumlah yang diterima
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ $payment->gross_amount ?? '-' }}
                                            </div>
                                            <div class="col-6">
                                                Waktu pembayaran
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ $payment->payment_time ?? '-' }}
                                            </div>
                                            <div class="col-6">
                                                Jenis pembayaran
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ $payment->payment_type ?? '-' }}
                                            </div>
                                            <div class="col-6">
                                                Detail pembayaran
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ $payment->payment_detail ?? '-' }}
                                            </div>
                                            <div class="col-6">
                                                Pesan pembayaran
                                            </div>
                                            <div class="col-6 text-end">
                                                {{ $payment->status_message ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="row gap-3">
                                            <div class="col-md">
                                                <button type="button" id="pay-button" href="{{ $snapToken }}"
                                                    class="btn btn-light border w-100">
                                                    Pilih
                                                    Metode
                                                </button>
                                            </div>
                                            <div class="col-md">
                                                <button class="btn btn-outline-dark w-100" wire:click='updateStatus'>
                                                    Perbarui Status
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-md">
                        <div class="card h-100">
                            <div class="card-body w-100" id="snap-container"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="{{ !empty($snapToken) ?: 'd-none' }}">

                @push('styles')
                    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
                        data-client-key="SB-Mid-server-yh7So1dkVPwD99Z4icKqvCX4"></script>
                @endpush

                @push('scripts')
                    <script type="text/javascript">
                        document.addEventListener('DOMContentLoaded', function() {
                            var payButton = document.getElementById('pay-button');

                            // Cek apakah payButton ada di DOM
                            if (!payButton) {
                                console.error("Tombol bayar tidak ditemukan!");
                                return;
                            }

                            // Debugging: Cek apakah snapToken tersedia
                            var snapToken = @json($snapToken);
                            console.log("Snap Token:", snapToken);

                            if (!snapToken) {
                                alert("Snap Token tidak tersedia, pastikan token dibuat di backend.");
                                return;
                            }

                            // Tambahkan event listener ke tombol
                            payButton.addEventListener('click', function() {
                                window.snap.embed(snapToken, {
                                    embedId: 'snap-container',
                                    onSuccess: function(result) {
                                        alert("Pembayaran sukses!");
                                        console.log(result);
                                        location.reload();
                                    },
                                    onPending: function(result) {
                                        alert("Menunggu pembayaran!");
                                        console.log(result);
                                        location.reload();
                                    },
                                    onError: function(result) {
                                        alert("Pembayaran gagal!");
                                        console.log(result);
                                        location.reload();
                                    },
                                    onClose: function() {
                                        alert('Anda menutup pembayaran sebelum selesai.');
                                    }
                                });
                            });
                        });
                    </script>
                @endpush

            </div>

        </div>
    @endvolt


</x-guest-layout>
