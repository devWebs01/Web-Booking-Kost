<?php

use App\Models\Room;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};

uses([LivewireAlert::class]);

name("rooms.index");

state(["search"])->url();

usesPagination(theme: "bootstrap");

$rooms = computed(function () {
    return Room::query()->latest()->get();
});

$destroy = function (room $room) {
    try {
        $room->delete();
        $this->alert("success", "Proses berhasil!", [
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

    $this->redirectRoute("rooms.index");
};

?>

<x-admin-layout>
    <x-slot name="title">Data kamar</x-slot>

    <x-slot name="header">
        <li class="breadcrumb-item">
            <a href="{{ route("home") }}">Beranda</a>
        </li>
        <li class="breadcrumb-item active">Kamar</li>
    </x-slot>

    @include("components.partials.datatables")

    @volt
        <div>

            <div class="card">
                <div class="card-header">
                    <a href="{{ route("rooms.create") }}" class="btn btn-primary">Tambah
                        Kamar</a>
                </div>

                <div class="card-body">
                    <div class="table-responsive border rounded p-3">
                        <table class="table table-striped text-center text-nowrap">
                            <thead>
                                <tr>
                                    <th>No. Kamar</th>
                                    <th>Status</th>
                                    <th>Posisi</th>
                                    <th>Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($this->rooms as $no => $item)
                                    <tr>
                                        <td>
                                            {{ $item->number }}
                                        </td>
                                        <td>
                                            <button class="btn btn-primary btn-sm">
                                                {{ __("room." . $item->room_status) }}
                                            </button>
                                        </td>
                                        <td>
                                            {{ __("position." . $item->position) }}
                                        </td>
                                        <td>
                                            <div>
                                                <a href="{{ route("rooms.edit", ["room" => $item->id]) }}"
                                                    class="btn btn-sm btn-warning">Edit</a>
                                                <button wire:loading.attr='disabled'
                                                    wire:click='destroy({{ $item->id }})'
                                                    wire:confirm="Apakah kamu yakin ingin menghapus data ini?"
                                                    class="btn btn-sm btn-danger">
                                                    {{ __("Hapus") }}
                                                </button>
                                            </div>
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

</x-admin-layout>
