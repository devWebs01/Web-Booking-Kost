<?php

use App\Models\Booking;
use Carbon\Carbon;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};
use Jantinnerezo\LivewireAlert\LivewireAlert;

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
                $query->whereAny([
                    'check_in_date',
                    'check_out_date',
                    'customer_name',
                    'status',
                ], 'LIKE', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);
    }
});

?>

<x-admin-layout>
    <div>
        <x-slot name="title">Data booking</x-slot>


        @volt
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <input wire:model.live="search" type="search" class="form-control" name="search" id="search"
                                aria-describedby="searchId" placeholder="Masukkan kata kunci pencarian" />
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
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Status</th>
                                    <th>Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($this->bookings as $no => $item)
                                    <tr>
                                        <td>
                                            {{ ++$no }}
                                        </td>
                                        <td>
                                            {{ $item->user->name }}
                                        </td>
                                        <td>
                                            {{ Carbon::parse($item->check_in_date)->format('d m Y') }}
                                        </td>
                                        <td>
                                            {{ Carbon::parse($item->check_out_date)->format('d m Y') }}
                                        </td>
                                        <td>
                                            <button class="btn btn-primary btn-sm">
                                                {{ __('booking.' . $item->status) }}
                                            </button>
                                        </td>
                                        <td>
                                            <a class="btn btn-primary btn-sm"
                                                href="{{ route('transactions.show', ['booking' => $item]) }}">
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