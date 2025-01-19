<?php

use App\Models\Room;
use App\Models\Facility;
use App\Models\Image;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, uses, usesFileUploads};
use function Laravel\Folio\name;

usesFileUploads();
uses([LivewireAlert::class]);

name('rooms.create');

state([

    // Room Models
    'number',
    'room_status',
]);

rules([
    'number' => 'required|numeric|min:0', // Harga harus ada, berupa angka, dan tidak negatif
    'room_status' => 'required|in:available,booked,maintenance', // Status kamar harus ada dan salah satu dari nilai yang ditentukan
]);

$create = function () {
    $validateData = $this->validate();

    try {
        // Mulai transaksi database
        \DB::beginTransaction();

        // Simpan data Room
        Room::create($validateData);

        // Commit transaksi
        \DB::commit();

        $this->alert('success', 'Data berhasil ditambahkan!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);

        $this->redirectRoute('rooms.index');
    } catch (\Throwable $th) {
        // Rollback transaksi jika terjadi kesalahan
        \DB::rollBack();

        // Tampilkan notifikasi error
        $this->alert('error', 'Terjadi kesalahan saat menyimpan data!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
};

?>

<x-admin-layout>
    <x-slot name="title">Tambah Kamar Baru</x-slot>

    @volt
    <div>
        <div class="card">
            <div class="card-header">
                <div class="alert alert-primary" role="alert">
                    <strong>Tambah Kamar</strong>
                    <p>Pada halaman tambah kamar, kamu dapat memasukkan informasi dari kamar baru yang akan disimpan ke
                        sistem.
                    </p>
                </div>
            </div>
            <div class="card-body">
                <form wire:submit="create">
                    @csrf

                    <div class="row">
                        <div class="col-md">
                            <div class="mb-3">
                                <label for="number" class="form-label">
                                    Nomor Kamar
                                </label>
                                <input type="number" class="form-control @error('number') is-invalid @enderror"
                                    wire:model="number" id="number" aria-describedby="numberId"
                                    placeholder="Enter room daily price" autofocus autocomplete="number" />
                                @error('number')
                                    <small id="numberId" class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md">
                            <div class="mb-3">
                                <label for="room_status" class="form-label">Status</label>
                                <select wire:model='room_status' class="form-select" name="room_status"
                                    id="room_status">
                                    <option selected>Pilih status</option>
                                    <option value="available">Tersedia</option>
                                    <option value="booked">Telah dipesan</option>
                                    <option value="maintenance">Perbaikan</option>
                                </select>
                                @error('room_status')
                                    <small id="room_statusId" class="form-text text-danger">{{ $message }}</small>
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
