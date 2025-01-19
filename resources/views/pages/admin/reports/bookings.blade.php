<?php

use App\Models\Booking;
use Carbon\Carbon;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

name('reports.bookings');

state(['reports' => fn() => Booking::get()]);

?>

<x-admin-layout>
    <div>
        <x-slot name="title">Data booking</x-slot>

        @include('layouts.print')
        @volt
        <div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive rounded">
                        <table class="table table-striped text-center text-nowrap">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Order ID</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Kontak Pelanggan</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Total Kamar</th>
                                    <th>Tipe</th>
                                    <th>Total Harga</th>
                                    <th>Status</th>
                                    <th>Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reports as $no => $report)
                                    <tr>
                                        <td>
                                            {{ ++$no }}
                                        </td>
                                        <td>
                                            {{ $report->order_id }}
                                        </td>
                                        <td>
                                            {{ $report->customer_name }}
                                        </td>
                                        <td>
                                            {{ $report->customer_contact }}
                                        </td>
                                        <td>
                                            {{ Carbon::parse($report->check_in_date)->format('d-m-Y') }}
                                        </td>
                                        <td>
                                            {{ Carbon::parse($report->check_out_date)->format('d-m-Y') }}
                                        </td>
                                        <td>
                                            {{ $report->totalRooms }}
                                        </td>
                                        <td>
                                            {{ __('type.' . $report->type) }}
                                        </td>
                                        <td>
                                            {{ formatRupiah($report->payment->amount) }}
                                        </td>
                                        <td>
                                            <button class="btn btn-primary btn-sm">
                                                {{ __('booking.' . $report->status) }}
                                            </button>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary btn-sm">
                                              {{$report->payment->status}}
                                            </button>

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

    </div>
</x-admin-layout>