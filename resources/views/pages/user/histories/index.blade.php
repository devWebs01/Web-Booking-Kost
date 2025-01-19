<?php

use App\Models\Booking;
use Carbon\Carbon;
use function Livewire\Volt\{state, uses, computed};
use function Laravel\Folio\name;
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

name('histories.index');

state([
    'bookings' => fn() => Booking::where('user_id', Auth()->user()->id)->get(),

]);

?>
<x-guest-layout>
    <x-slot name="title">Riwayat Pemesanan</x-slot>
    @include('layouts.datatables')
    @volt
    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-primary text-center text-nowrap">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Tanggal Check In</th>
                                <th>Tanggal Check Out</th>
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
                                        {{ Carbon::parse($booking->check_in_date)->format('d M Y')}}
                                    </td>
                                    <td>
                                        {{ Carbon::parse($booking->check_out_date)->format('d M Y')}}
                                    </td>
                                    <td>
                                        {{__('booking.' . $booking->status)}}
                                    </td>
                                    <td>
                                        <a class="btn btn-primary"
                                            href="{{ route('histories.show', ['booking' => $booking]) }}" role="button">
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