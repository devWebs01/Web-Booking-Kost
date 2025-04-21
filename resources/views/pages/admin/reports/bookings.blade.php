<?php

use App\Models\Booking;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};

uses([LivewireAlert::class]);

name('reports.bookings');

state(['reports' => fn () => Booking::get()]);

?>

<x-admin-layout>
    <div>
        <x-slot name="title">Laporan Pemesanan</x-slot>

        <x-slot name="header">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Beranda</a>
            </li>
            <li class="breadcrumb-item active">Laporan Pemesanan</li>
        </x-slot>

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
                                        <th>Total Harga</th>
                                        <th>Status</th>
                                        <th>Tanggal Dibuat</th>
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
                                                {{ $report->user->name }}
                                            </td>
                                            <td>
                                                {{ $report->user->telp }}
                                            </td>
                                            <td>
                                                {{ Carbon::parse($report->check_in_date)->format('d-m-Y') }}
                                            </td>
                                            <td>
                                                {{ Carbon::parse($report->check_out_date)->format('d-m-Y') }}
                                            </td>
                                            <td>
                                                {{ $report->items->count() }}
                                            </td>
                                            <td>
                                                {{ formatRupiah($report->price) }}
                                            </td>
                                            <td>
                                                {{ $report->status }}
                                            </td>
                                            <td>
                                                {{ $report->created_at }}
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
