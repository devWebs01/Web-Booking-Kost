<?php

use App\Models\Booking;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use function Laravel\Folio\name;
use function Livewire\Volt\{computed, uses};

uses([LivewireAlert::class]);

name("transactions.index");

$bookings = computed(function () {
    return booking::query()->latest()->get();
});

$confirmBooking = function (Booking $booking) {
    try {
        $booking->update([
            "status" => "CONFIRM",
        ]);
        $this->alert("success", "Pemesanan berhasil dikonfirmasi!", [
            "position" => "center",
            "timer" => 3000,
            "toast" => true,
        ]);
    } catch (\Throwable $th) {
        $this->alert("error", "Proses gagal!", [
            "position" => "center",
            "timer" => 3000,
            "toast" => true,
        ]);
    }
    $this->redirectRoute("transactions.index");
};

$completeBooking = function (Booking $booking) {
    try {
        $booking->update([
            "status" => "COMPLETE",
        ]);
        $this->alert("success", "Pemesanan berhasil dikonfirmasi!", [
            "position" => "center",
            "timer" => 3000,
            "toast" => true,
        ]);
    } catch (\Throwable $th) {
        $this->alert("error", "Proses gagal!", [
            "position" => "center",
            "timer" => 3000,
            "toast" => true,
        ]);
    }
    $this->redirectRoute("transactions.index");
};

$cancelBooking = function (Booking $booking) {
    try {
        $booking->update([
            "status" => "CANCEL",
        ]);
        $this->alert("success", "Pemesanan berhasil dikonfirmasi!", [
            "position" => "center",
            "timer" => 3000,
            "toast" => true,
        ]);
    } catch (\Throwable $th) {
        $this->alert("error", "Proses gagal!", [
            "position" => "center",
            "timer" => 3000,
            "toast" => true,
        ]);
    }
    $this->redirectRoute("transactions.index");
};

?>

<x-admin-layout>
    <div>
        <x-slot name="title">Data Pemesanan</x-slot>

        <x-slot name="header">
            <li class="breadcrumb-item">
                <a href="{{ route("home") }}">Beranda</a>
            </li>
            <li class="breadcrumb-item active">Pemesanan</li>
        </x-slot>

        @include("components.partials.datatables")

        @volt
            <div>
                <div class="card">

                    <div class="card-body">
                        <div class="table-responsive border rounded p-3">
                            <table class="table table-striped text-center text-nowrap">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Pelanggan</th>
                                        <th>Total kamar</th>
                                        <th>Tanggal Pemesanan</th>
                                        <th>Status</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($this->bookings as $no => $booking)
                                        <tr>
                                            <td>
                                                {{ ++$no }}
                                            </td>
                                            <td>
                                                {{ $booking->user->name }}
                                            </td>
                                            <td>
                                                {{ $booking->items->count() . " Kamar" ?? "-" }}
                                            </td>
                                            <td>
                                                {{ $booking->created_at->format("d M Y") }}
                                            </td>
                                            <td>
                                                <button class="btn btn-primary btn-sm">
                                                    {{ __("booking." . $booking->status) }}
                                                </button>
                                            </td>
                                            <td>
                                                <button wire:loading.attr='disabled'
                                                    wire:click='confirmBooking({{ $booking->id }})'
                                                    class="btn btn-sm btn-dark {{ $booking->status === "PROCESS" ?: "d-none" }}">
                                                    Konfirmasi
                                                </button>

                                                <button wire:loading.attr='disabled'
                                                    wire:click='cancelBooking({{ $booking->id }})'
                                                    class="btn btn-sm btn-danger {{ $booking->status === "PROCESS" ?: "d-none" }}">
                                                    Tolak
                                                </button>

                                                <button wire:loading.attr='disabled'
                                                    wire:click='completeBooking({{ $booking->id }})'
                                                    class="btn btn-sm btn-success {{ $booking->status === "CONFIRM" ?: "d-none" }}">
                                                    Selesai
                                                </button>

                                                <a class="btn btn-primary btn-sm"
                                                    href="{{ route("transactions.show", ["booking" => $booking]) }}">
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

    </div>
</x-admin-layout>
