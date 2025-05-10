<?php

use App\Models\Booking;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};

uses([LivewireAlert::class]);

name('transactions.index');

state(['search'])->url();
usesPagination(theme: 'bootstrap');

$bookings = computed(function () {
    if ($this->search == null) {
        return booking::query()->latest()->paginate(10);
    } else {
        return booking::query()
            ->where(function ($query) {
                // isi
                $query->whereAny(['check_in_date', 'check_out_date', 'customer_name', 'status'], 'LIKE', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);
    }
});

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

$completeBooking = function (Booking $booking) {
    try {
        $booking->update([
            'status' => 'COMPLETE',
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

$cancelBooking = function (Booking $booking) {
    try {
        $booking->update([
            'status' => 'CANCEL',
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

?>

<x-admin-layout>
    <div>
        <x-slot name="title">Data Pemesanan</x-slot>

        <x-slot name="header">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Beranda</a>
            </li>
            <li class="breadcrumb-item active">Pemesanan</li>
        </x-slot>


        @volt
            <div>
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <input wire:model.live="search" type="search" class="form-control" name="search"
                                    id="search" aria-describedby="searchId"
                                    placeholder="Masukkan kata kunci pencarian" />
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive border rounded">
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
                                                {{ $booking->items->count() . ' Kamar' ?? '-' }}
                                            </td>
                                            <td>
                                                {{ $booking->created_at->format('d M Y') }}
                                            </td>
                                            <td>
                                                <button class="btn btn-primary btn-sm">
                                                    {{ __('booking.' . $booking->status) }}
                                                </button>
                                            </td>
                                            <td>
                                                <button wire:loading.attr='disabled'
                                                    wire:click='confirmBooking({{ $booking->id }})'
                                                    class="btn btn-sm btn-dark {{ $booking->status === 'PROCESS' ?: 'd-none' }}">
                                                    Konfirmasi
                                                </button>

                                                <button wire:loading.attr='disabled'
                                                    wire:click='cancelBooking({{ $booking->id }})'
                                                    class="btn btn-sm btn-danger {{ $booking->status === 'PROCESS' ?: 'd-none' }}">
                                                    Tolak
                                                </button>

                                                <button wire:loading.attr='disabled'
                                                    wire:click='completeBooking({{ $booking->id }})'
                                                    class="btn btn-sm btn-success {{ $booking->status === 'CONFIRM' ?: 'd-none' }}">
                                                    Selesai
                                                </button>

                                                <a class="btn btn-primary btn-sm"
                                                    href="{{ route('transactions.show', ['booking' => $booking]) }}">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>

                            <div class="container d-flex justify-content-center">
                                {{ $this->bookings->links() }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @endvolt

    </div>
</x-admin-layout>
