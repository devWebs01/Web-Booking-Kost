<?php

use App\Models\Setting;
use App\Models\Booking;
use Carbon\Carbon;
use function Livewire\Volt\{state, uses};
use function Laravel\Folio\name;
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

name('transactions.show');

state([
    'setting' => fn() => Setting::first(['name', 'location', 'description']),
    'payment' => fn() => $this->booking->payment,
    'user' => fn() => $this->booking->user,
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
        <div>

            <div class="card">
                <section class="card-header bg-body border-0 mb-3">
                    <div class="row justify-content-end">
                        <div class="col-6 text-end">
                            <button type="button" class="btn btn-dark d-print-none" id="printInvoiceBtn">
                                Download
                                Invoice
                            </button>
                        </div>
                    </div>
                </section>

                <div class="card-body">

                    <section class="mb-5">
                        <div class="row">
                            <div class="col">
                                <h5 class="fw-bold">
                                    Pemesanan
                                </h5>

                                <div>INV-{{ $booking->order_id }}</div>
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
    @endvolt

</x-admin-layout>
