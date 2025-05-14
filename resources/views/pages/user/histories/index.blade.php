<?php

use App\Models\Booking;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use function Livewire\Volt\{state, uses};
use function Laravel\Folio\name;

uses([LivewireAlert::class]);

name("histories.index");

state([
    "bookings" => fn() => Booking::where("user_id", Auth()->user()->id)
        ->latest()
        ->get(),
]);

?>
<x-guest-layout>
    <x-slot name="title">Riwayat Pemesanan</x-slot>
    @include("components.partials.datatables")
    @volt
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-center text-nowrap">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Invoice</th>
                                    <th>Tanggal</th>
                                    <th>Tipe</th>
                                    <th>Status</th>
                                    <th>Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookings as $no => $booking)
                                    <tr>
                                        <td>
                                            {{ ++$no }}
                                        </td>
                                        <td>
                                            <span class="text-uppercase">{{ $booking->order_id }}</span>
                                        </td>
                                        <td>
                                            {{ Carbon\Carbon::parse($booking->check_in_date)->format("d M Y") }}
                                            -
                                            {{ Carbon\Carbon::parse($booking->check_out_date)->format("d M Y") }}
                                        </td>
                                        <td>
                                            {{ __("type." . $booking->booking_type) }}
                                        </td>
                                        <td>
                                            {{ __("booking." . $booking->status) }}
                                        </td>
                                        <td>
                                            <a class="btn btn-primary"
                                                href="{{ route("histories.show", ["booking" => $booking]) }}" role="button">
                                                Detail
                                            </a>

                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    @endvolt
</x-guest-layout>
