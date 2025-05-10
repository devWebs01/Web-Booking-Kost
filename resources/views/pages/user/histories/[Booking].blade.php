<?php

use App\Models\Payment;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Setting;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use function Livewire\Volt\{state, on, uses};
use function Laravel\Folio\name;
use function Laravel\Folio\{middleware};

uses([LivewireAlert::class]);
middleware(["auth"]);

name("histories.show");

state([
    "setting" => fn () => Setting::first(),
    "user" => fn () => Auth()->user(),
    "snapToken" => fn () => $this->booking->snapToken ?? "",
    "expired_at" => fn () => $this->booking->expired_at ?? "",
    "payment" => fn () => $this->booking->payment ?? null,
    "booking",
]);

on([
    "updateSnapToken" => function () {
        $this->snapToken = $this->booking->snapToken;
    },
]);

$processPayment = function () {
    Config::$serverKey = config("midtrans.server_key");
    Config::$isProduction = config("midtrans.is_production");
    Config::$isSanitized = true;
    Config::$is3ds = true;

    try {
        // Data transaksi

        if (empty($this->snapToken)) {
            $params = [
                "transaction_details" => [
                    "order_id" => $this->booking->order_id,
                    "gross_amount" => $this->booking->price,
                ],
                "customer_details" => [
                    "first_name" => $this->user->name,
                    "email" => $this->user->email,
                    "phone" => $this->user->telp,
                ],
                "expiry" => [
                    "start_time" => $this->booking->expired_at ? Carbon::parse($this->booking->expired_at)->format("Y-m-d H:i:s O") : Carbon::now()->format("Y-m-d H:i:s O"),
                    "unit" => "minutes",
                    "duration" => $this->booking->expired_at ? Carbon::now()->diffInMinutes(Carbon::parse($this->booking->expired_at)) : 5, // Menghitung durasi kedaluwarsa dalam menit
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            // Simpan snapToken ke dalam booking
            $this->booking->update(["snapToken" => $snapToken]);

            // Dispatch event untuk update snapToken di state
            $this->dispatch("updateSnapToken");
        }

        if (! $this->booking->payment) {
            $payment = Payment::create([
                "booking_id" => $this->booking->id,
            ]);
        } else {
            $payment = $this->booking->payment;
        }

        $this->redirectRoute("payments.guest", [
            "payment" => $payment,
        ]);
    } catch (\Exception $e) {
        \Log::error("Payment Error: " . $e->getMessage());
        $this->alert("error", "Proses gagal! Terjadi kesalahan pada sistem.", [
            "position" => "center",
            "timer" => 2000,
            "toast" => true,
            "timerProgressBar" => true,
        ]);
    }
};

$cancelBooking = function () {
    $booking = $this->booking;

    $booking->update([
        "status" => "CANCEL",
    ]);

    $this->redirectRoute("histories.index");
};

$getTimeRemainingAttribute = function () {
    $now = Carbon::now();
    $expiry = Carbon::parse($this->expired_at);

    if ($expiry->isPast()) {
        return "Expired";
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
        <div class="container-fluid">

            <div class="container">
                <!-- Menampilkan Detail Pemesanan Kamar -->
                <div class="card py-4">
                    <div class="text-center col-10" style="place-self:center;">
                        <h2 class="fw-bold text-primary text-capitalize">Helo {{ $booking->user->name }}</h2>
                        <p class="fw-bold">
                            Kami dengan senang hati menginformasikan bahwa pesanan kamar Anda pada tanggal
                            {{ $booking->created_at->format("d M Y") }}
                            telah berhasil dibuat.
                            <br>
                        </p>

                        @if ($payment->status !== 'PAID' && $payment->status !== 'VERIFICATION')
                        <div wire:poll.1s class="alert alert-warning" role="alert">
                            Mohon untuk menyelesaikan pembayaran sebelum waktu habis dalam
                            {{ $this->getTimeRemainingAttribute() }}
                        </div>
                        @endif

                    </div>

                    <div class="card-body px-lg-5 mx-lg-5">

                        <section class="row align-items-center mb-4 border-bottom pb-3">
                            <div class="col">
                                <span class="h3 fw-bold text-primary text-uppercase">
                                    {{ $booking->order_id }}
                                </span>
                                <div class="d-none spinner-border" wire:loading.class.remove="d-none"
                                    wire:target='processPayment, cancelBooking, checkStatus' role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                            <div class="col text-end">
                                <button type="button" class="btn btn-dark d-print-none" id="printInvoiceBtn">Download
                                    Invoice</button>

                                <script>
                                    document.getElementById('printInvoiceBtn').addEventListener('click', function() {
                                        window.print(); // Fungsi bawaan browser untuk mencetak halaman
                                    });
                                </script>

                            </div>
                        </section>

                        <section class="mb-5">
                            <div class="row">
                                <div class="col-4">
                                    <h6 class="fw-bold">
                                        Checkin
                                    </h6>
                                    <p>{{ Carbon::parse($booking->check_in_date)->format("d M Y") }}</p>
                                </div>
                                <div class="col-4">
                                    <h6 class="fw-bold">
                                        Checkin
                                    </h6>
                                    <p>{{ Carbon::parse($booking->check_out_date)->format("d M Y") }}</p>
                                </div>
                                <div class="col-4">
                                    <h6 class="fw-bold">
                                        Tipe Pemesanan
                                    </h6>
                                    <p>
                                        {{ __("type." . $booking->booking_type) }}
                                    </p>
                                </div>

                                <div class="col-4">
                                    <h6 class="fw-bold">
                                        Dibayarkan Ke
                                    </h6>
                                    <p class="mb-0">
                                        {{ $booking->user->name }}
                                    </p>

                                </div>
                                <div class="col-4">
                                    <h6 class="fw-bold">
                                        Tipe Pemesanan
                                    </h6>
                                    <p>
                                        {{ __("booking." . $booking->status) }}
                                    </p>
                                </div>

                                <div class="col-4">
                                    <h6 class="fw-bold">
                                        Pembayaran
                                    </h6>
                                    <p>
                                        {{ __("payment." . $payment->status ?? "-") }}
                                    </p>
                                </div>
                            </div>
                        </section>

                        <section>
                            <div class="table-responsive rounded">
                                <table class="table table-hover rounded">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="fw-bold text-dark">No.</th>
                                            <th class="fw-bold text-dark">Kamar</th>
                                            <th class="text-end text-dark">Tempat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($booking->items as $no => $item)
                                            <tr>
                                                <td>
                                                    {{ ++$no }}.
                                                </td>
                                                <td>
                                                    Kamar {{ $item->room->number }}
                                                </td>
                                                <td class="text-end">
                                                    {{ $item->room->position === "up" ? "Kamar Atas" : "Kamar Bawah" }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfooter>

                                        <tr>
                                            <td colspan="2" class="fw-bold">Status</td>
                                            <td class="text-end fw-bold">{{ __("payment." . $payment->status) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="fw-bold">Waktu pembayaran</td>
                                            <td class="text-end fw-bold">{{ $payment->payment_time ?? "-" }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="fw-bold">Jenis pembayaran</td>
                                            <td class="text-end fw-bold">{{ $payment->payment_type ?? "-" }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="fw-bold">Detail pembayaran</td>
                                            <td class="text-end fw-bold">{{ $payment->payment_detail ?? "-" }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="fw-bold">Pesan pembayaran</td>
                                            <td class="text-end fw-bold">{{ $payment->status_message ?? "-" }}</td>
                                        </tr>

                                        <tr>
                                            <td colspan="2" class="fw-bold">Jumlah harus dibayar</td>
                                            <td class="text-end fw-bold">{{ formatRupiah($booking->price) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="fw-bold">Jumlah yang diterima</td>
                                            <td class="text-end fw-bold">
                                                {{ $payment->gross_amount ? formatRupiah($payment->gross_amount) : "-" }}
                                            </td>
                                        </tr>

                                    </tfooter>
                                </table>

                                <div class="my-5" @if (now()->lessThan(\Carbon\Carbon::parse($expired_at))) wire:poll.5s @endif>

                                    <div class="{{ $booking->status !== "CANCEL" ?: "d-none" }}">
                                        <div
                                            class="d-flex justify-content-between mb-3 {{ empty($snapToken) ?: "d-none" }}">
                                            <button wire:click='cancelBooking'
                                                class="btn btn-outline-danger {{ empty($snapToken) ?: "disabled" }}">Batalkan</button>
                                            <button class="btn btn-outline-success {{ empty($snapToken) ?: "disabled" }}"
                                                wire:click="processPayment">Konfirmasi</button>
                                        </div>

                                        <div class="col {{ !empty($snapToken) ?: "d-none" }}">
                                            <div class="d-flex justify-content-between mb-3">
                                                <a href="{{ route("histories.index") }}"
                                                    class="btn btn-outline-dark">Kembali</a>

                                                <a href="{{ route("payments.guest", [
                                                    "payment" => $payment,
                                                ]) }}"
                                                    class="btn btn-outline-success {{ $payment->status === "UNPAID" ?: "d-none" }}">Lanjut
                                                    Pembayaran</a>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>

                    </div>
                </div>
            </div>

        </div>
    @endvolt

</x-guest-layout>
