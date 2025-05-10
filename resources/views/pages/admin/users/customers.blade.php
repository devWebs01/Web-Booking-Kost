<?php

use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use function Livewire\Volt\{computed, uses};
use function Laravel\Folio\name;

uses([LivewireAlert::class]);

name("customers");

$customers = computed(function () {
    return User::query()->where("role", "customer")->latest()->paginate(10);
});

?>

<x-admin-layout>
    <div>
        <x-slot name="title">Pelanggan</x-slot>
        <x-slot name="header">
            <li class="breadcrumb-item">
                <a href="{{ route("home") }}">Beranda</a>
            </li>
            <li class="breadcrumb-item active">Pelanggan</li>
        </x-slot>

        @include("components.partials.datatables")

        @volt
            <div>
                <div class="card">

                    <div class="card-body">
                        <div class="table-responsive border rounded p-3 ">
                            <table class="table text-center text-nowrap">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Telp</th>
                                        <th>Total Pemesanan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($this->customers as $no => $customer)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>{{ $customer->name }}</td>
                                            <td>{{ $customer->email }}</td>
                                            <td>{{ $customer->telp }}</td>
                                            <td>
                                                {{ $customer->bookings->count() }} Booking
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
