<?php

use App\Models\Setting;
use App\Models\Booking;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use function Livewire\Volt\{state, uses};
use function Laravel\Folio\name;

uses([LivewireAlert::class]);

name('transactions.show');

state([
    'setting' => fn () => Setting::first(['name', 'location', 'description']),
    'payment' => fn () => $this->booking->payment,
    'user' => fn () => $this->booking->user,
    'booking',
]);

$confirmBooking = function (Booking $booking) {
    try {
        $booking->update([
            'status' => 'CONFIRM',
        ]);
        $this->alert('success', 'Pemesanan berhasil dikonfirmasi!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);
    } catch (\Throwable $th) {
        $this->alert('error', 'Proses gagal!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
};

$completeBooking = function () {
    $this->booking->update([
        'status' => 'COMPLETE',
    ]);

    $this->alert('success', 'Pemesanan telah selesai!', [
        'position' => 'center',
        'timer' => 2000,
        'toast' => true,
    ]);
};

?>

<x-admin-layout>
    <x-slot name="title">Transaksi Pemesanan Kamar</x-slot>

    <x-slot name="header">
        <li class="breadcrumb-item">
            <a href="{{ route('home') }}">Beranda</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('users.index') }}">Pemesanan</a>
        </li>
        <li class="breadcrumb-item active">Detail</li>
    </x-slot>


    @push('scripts')
        <script>
            document.getElementById('printInvoiceBtn').addEventListener('click', function() {
                window.print(); // Fungsi bawaan browser untuk mencetak halaman
            });
        </script>
    @endpush

    @volt
        <div class="card">

            <div class="card-body px-lg-5 mx-lg-5">

                <section class="row align-items-center mb-4 border-bottom pb-3">
                    <div class="col">
                        <span class="h3 fw-bolder text-primary text-uppercase">
                            {{ $booking->order_id }}
                        </span>
                        <div class="d-none spinner-border" wire:loading.class.remove="d-none"
                            wire:target='processPayment, cancelBooking, checkStatus' role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div class="col text-end">
                        <button type="button" class="btn btn-dark d-print-none"
                            id="printInvoiceBtn">Download
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
                            <h6 class="fw-bolder">
                                Checkin
                            </h6>
                            <p>{{ Carbon::parse($booking->check_in_date)->format('d M Y') }}</p>
                        </div>
                        <div class="col-4">
                            <h6 class="fw-bolder">
                                Checkin
                            </h6>
                            <p>{{ Carbon::parse($booking->check_out_date)->format('d M Y') }}</p>
                        </div>
                        <div class="col-4">
                            <h6 class="fw-bolder">
                                Tipe Pemesanan
                            </h6>
                            <p>
                                {{ $booking->booking_type }}
                            </p>
                        </div>

                        <div class="col-4">
                            <h6 class="fw-bolder">
                                Dibayarkan Ke
                            </h6>
                            <p class="mb-0">
                                {{ $booking->user->name }}
                            </p>

                        </div>
                        <div class="col-4">
                            <h6 class="fw-bolder">
                                Tipe Pemesanan
                            </h6>
                            <p>
                                {{ __('booking.' . $booking->status) }}
                            </p>
                        </div>

                        <div class="col-4">
                            <h6 class="fw-bolder">
                                Pembayaran
                            </h6>
                            <p>
                                {{ __('payment.'.$payment->status ?? '-') }}
                            </p>
                        </div>
                    </div>
                </section>

                <section>
                    <div class="table-responsive rounded">
                        <table class="table table-hover rounded border">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-bolder text-dark">No.</th>
                                    <th class="fw-bolder text-dark">Kamar</th>
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
                                            {{ $item->room->position === 'up' ? 'Kamar Atas' : 'Kamar Bawah' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfooter>
                               
                                <tr>
                                    <td colspan="2" class="fw-bolder">Status</td>
                                    <td class="text-end fw-bolder">{{ __('payment.' . $payment->status) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="fw-bolder">Waktu pembayaran</td>
                                    <td class="text-end fw-bolder">{{ $payment->payment_time ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="fw-bolder">Jenis pembayaran</td>
                                    <td class="text-end fw-bolder">{{ $payment->payment_type ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="fw-bolder">Detail pembayaran</td>
                                    <td class="text-end fw-bolder">{{ $payment->payment_detail ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="fw-bolder">Pesan pembayaran</td>
                                    <td class="text-end fw-bolder">{{ $payment->status_message ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <td colspan="2" class="fw-bolder">Jumlah harus dibayar</td>
                                    <td class="text-end fw-bolder">{{ formatRupiah($booking->price) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="fw-bolder">Jumlah yang diterima</td>
                                    <td class="text-end fw-bolder">{{ formatRupiah($payment->gross_amount) ?? '-' }}</td>
                                </tr>
                            </tfooter>
                        </table>

                    </div>
                </section>

            </div>
        </div>
    @endvolt

</x-admin-layout>
