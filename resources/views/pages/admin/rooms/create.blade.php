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
    'facilities' => [], // facility models
    'images' => [], // Image models
    'previmages',

    // Room Models
    'price',
    'description',
    'room_status',
]);

rules([
    'price' => 'required|numeric|min:0', // Harga harus ada, berupa angka, dan tidak negatif
    'description' => 'required|string|max:255', // Deskripsi harus ada, berupa string, dan maksimal 255 karakter
    'room_status' => 'required|in:available,booked,maintenance', // Status kamar harus ada dan salah satu dari nilai yang ditentukan
    'facilities' => 'required', // Validasi array
    'facilities.*' => 'required|string|min:5', // Validasi setiap item
    'images' => 'required', // Validasi file gambar
    'images.*' => 'image|max:2048', // Validasi file gambar
]);

$updatingImages = function ($value) {
    $this->previmages = $this->images;
};

$updatedImages = function ($value) {
    $this->images = array_merge($this->previmages, $value);
};

$removeItem = function ($key) {
    if (isset($this->images[$key])) {
        $file = $this->images[$key];
        $file->delete();
        unset($this->images[$key]);
    }

    $this->images = array_values($this->images);
};

$create = function () {
    $validateData = $this->validate();

    try {
        // Mulai transaksi database
        \DB::beginTransaction();
        // Konversi fasilitas dari string menjadi array jika diperlukan
        $facilities = is_array($this->facilities) ? $this->facilities : explode(',', $this->facilities);

        // Simpan data Room
        $room = Room::create($validateData);

        // Simpan fasilitas
        foreach ($facilities as $facility) {
            Facility::create([
                'room_id' => $room->id, // Perbaikan key
                'name' => $facility,
            ]);
        }

        // Simpan Images
        foreach ($this->images as $image) {
            $path = $image->store('rooms', 'public'); // Simpan ke folder "fields" di storage
            Image::create([
                'room_id' => $room->id, // Perbaikan key
                'image_path' => $path,
            ]);

            $image->delete();
        }

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
    <x-slot name="title">Tambah room Baru</x-slot>
    @include('layouts.tom-select')

    @volt
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="alert alert-primary" role="alert">
                        <strong>Tambah room</strong>
                        <p>Pada halaman tambah room, kamu dapat memasukkan informasi dari room baru yang akan disimpan ke
                            sistem.
                        </p>
                    </div>
                </div>

                @if ($images)
                    <div class="card-body">
                        <div class="d-flex flex-nowrap gap-3 overflow-auto" style="white-space: nowrap;">
                            @foreach ($images as $key => $image)
                                <div class="position-relative" style="width: 200px; flex: 0 0 auto;">
                                    <div class="card mt-3">
                                        <img src="{{ $image->temporaryUrl() }}" class="card-img-top"
                                            style="object-fit: cover; width: 200px; height: 200px;" alt="preview">
                                        <a type="button" class="position-absolute top-0 start-100 translate-middle p-2"
                                            wire:click.prevent='removeItem({{ json_encode($key) }})'>
                                            <i class='bx bx-x p-2 rounded-circle ri-20px text-white bg-danger'></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif


                <div class="card-body">
                    <form wire:submit="create">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="images" class="form-label">Gambar Kamar
                                        <span wire:target='images' wire:loading.class.remove="d-none"
                                            class="d-none spinner-border spinner-border-sm" role="status">
                                        </span>
                                    </label>
                                    <input type="file" class="form-control @error('images') is-invalid @enderror"
                                        wire:model="images" id="images" aria-describedby="imagesId" autocomplete="images"
                                        accept="image/*" multiple />
                                    @error('images')
                                        <small id="imagesId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Harga</label>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror"
                                        wire:model="price" id="price" aria-describedby="priceId"
                                        placeholder="Enter room price" autofocus autocomplete="price" />
                                    @error('price')
                                        <small id="priceId" class="form-text text-danger">{{ $message }}</small>
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

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="facilities" class="form-label">Fasilitas</label>
                                    <div wire:ignore>
                                        <input type="text" wire:model="facilities" id="input-tags"
                                            aria-describedby="facilitiesId" autocomplete="facilities" />
                                    </div>
                                    @error('facilities')
                                        <small id="facilitiesId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                    <br>
                                    @error('facilities.*')
                                        <small id="facilitiesId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Keterangan Kamar</label>
                                    <textarea wire:model="description" class="form-control" name="description" id="description" rows="3"></textarea>
                                    @error('description')
                                        <small id="descriptionId" class="form-text text-danger">{{ $message }}</small>
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
