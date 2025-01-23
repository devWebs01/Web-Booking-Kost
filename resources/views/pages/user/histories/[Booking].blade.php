<?php

use App\Models\Room;
use App\Models\Image;
use App\Models\Setting;
use Carbon\Carbon;
use function Livewire\Volt\{state, rules, computed};
use function Laravel\Folio\name;
use function Laravel\Folio\{middleware};

middleware(['auth']);

name('histories.show');

state([
    'setting' => fn() => Setting::first(),
    'user' => fn() => Auth()->user(),
    'booking',
]);

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
                                        </tr>
                                    </thead>
                                    <tbody style="vertical-align: middle">
                                        @foreach ($booking->items as $item)
                                            <tr>

                                                <td>
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
                                                    {{ formatRupiah($item->price) }}
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
                                {{ formatRupiah($booking->total) }}
                            </strong>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button class="btn btn-primary btn-lg">
                            Lakukan Pembayaran
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </div>
    @endvolt


</x-guest-layout>
