<?php

use Jantinnerezo\LivewireAlert\LivewireAlert;

use function Livewire\Volt\{state, rules, uses, usesFileUploads};
use function Laravel\Folio\name;

usesFileUploads();

uses([LivewireAlert::class]);

name('rooms.edit');

state([
    'number' => fn () => $this->room->number,
    'room_status' => fn () => $this->room->room_status,
    'position' => fn () => $this->room->position,
    'room',
]);

rules([
    'number' => 'required|numeric',
    'room_status' => 'required|in:active,non-active',
    'position' => 'required|in:up,down',
]);

$edit = function () {
    $validateData = $this->validate();
    try {
        \DB::beginTransaction();

        $room = $this->room;

        $room->update([
            'number' => $validateData['number'],
            'room_status' => $validateData['room_status'],
        ]);

        \DB::commit();

        $this->alert('success', 'Data berhasil diedit!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);

        $this->redirectRoute('rooms.index');
    } catch (\Exception $e) {
        \DB::rollBack();

        $this->alert('error', 'Terjadi kesalahan saat menyimpan data!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
};

?>

<x-admin-layout>
    <x-slot name="title">Edit Kamar</x-slot>

    <x-slot name="header">
        <li class="breadcrumb-item">
            <a href="{{ route('home') }}">Beranda</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('rooms.index') }}">Kamar</a>
        </li>
        <li class="breadcrumb-item active">Edit Kamar</li>
    </x-slot>

    @volt
        <div>

            <div class="card">
                <div class="card-header">
                    <div class="alert alert-primary" role="alert">
                        <strong>Edit Kamar</strong>
                        <p>Pada halaman edit kamar, kamu dapat mengubah informasi kamar yang sudah ada.
                        </p>
                    </div>
                </div>


                <div class="card-body">
                    <form wire:submit="edit">
                        @csrf
                        <div class="row">

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="number" class="form-label">No Kamar</label>
                                    <input type="number" class="form-control @error('number') is-invalid @enderror"
                                        wire:model="number" id="number" aria-describedby="numberId"
                                        placeholder="Enter room number" autofocus autocomplete="number" />
                                    @error('number')
                                        <small id="numberId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="room_status" class="form-label">Status</label>
                                    <select wire:model='room_status' class="form-select" name="room_status"
                                        id="room_status">
                                        <option selected>Pilih status</option>
                                        <option value="active">Aktif</option>
                                        <option value="non-active">Tidak Aktif</option>
                                    </select>
                                    @error('room_status')
                                        <small id="room_statusId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="position" class="form-label">Posisi Kamar</label>
                                    <select wire:model='position' class="form-select" name="position"
                                        id="position">
                                        <option selected>Pilih salah satu</option>
                                        <option value="up">Kamar Atas</option>
                                        <option value="down">Kamar Bawah</option>
                                    </select>
                                    @error('position')
                                        <small id="positionId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                        </div>


                        <div class="row mb-3">
                            <div class="col-md">
                                <button type="submit" class="btn btn-primary">
                                    Submit
                                </button>
                            </div>
                            <div class="col-md align-self-center text-end">
                                <span wire:loading class="spinner-border spinner-border-sm"></span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endvolt
</x-admin-layout>
