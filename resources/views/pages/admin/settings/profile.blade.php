<?php

use App\Models\Setting;
use function Livewire\Volt\{state, computed, rules, uses};
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

$getSetting = computed(function () {
    return Setting::first();
});

state([
    'name' => fn() => $this->getSetting->name ?? '',
    'description' => fn() => $this->getSetting->description ?? '',
    'location' => fn() => $this->getSetting->location ?? '',
    'phone' => fn() => $this->getSetting->phone ?? '',
    'daily_price' => fn() => $this->getSetting->daily_price ?? '',
    'monthly_price' => fn() => $this->getSetting->monthly_price ?? '',
    'facilities' => fn() => $this->getSetting->facilities->pluck('name')->toArray(), // Muat facilities sebagai array
]);

rules([
    'name' => 'required|min:5',
    'phone' => 'required|numeric',
    'location' => 'required|min:5',
    'description' => 'required|min:5',
    'daily_price' => 'required|numeric',
    'monthly_price' => 'required|numeric',
    'facilities' => 'required', // Validasi array
    'facilities.*' => 'required|string|min:2', // Validasi setiap item
]);

$save = function () {
    $validate = $this->validate();

    // Menggunakan updateOrCreate untuk memperbarui atau membuat Setting
    $setting = Setting::updateOrCreate(
        ['id' => $this->getSetting ? Setting::first()->id : null], // Kondisi pencarian
        $validate, // Data yang akan diperbarui atau dibuat
    );

    // Pastikan facilities berupa array
    $facilities = is_array($this->facilities) ? $this->facilities : explode(',', $this->facilities);

    // Hapus fasilitas lama dan tambahkan yang baru
    $setting->facilities()->delete();

    foreach ($facilities as $facility) {
        $setting->facilities()->create([
            'name' => $facility,
        ]);
    }


    $this->alert('success', 'Data berhasil diupdate!', [
        'position' => 'center',
        'timer' => 3000,
        'toast' => true,
    ]);
};

?>
@volt
<div>
    @include('layouts.tom-select')
    

    <form wire:submit="save">
        @csrf

        <div class="row mb-3">

            <div class="col-md">
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name"
                        id="name" aria-describedby="nameId" placeholder="Enter name" />
                    @error('name')
                        <small id="nameId" class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>


            <div class="col-md">
                <div class="mb-3">
                    <label for="phone" class="form-label">No. Telp</label>
                    <input type="number" class="form-control @error('phone') is-invalid @enderror" wire:model="phone"
                        id="phone" aria-describedby="phoneId" placeholder="Enter phone" />
                    @error('phone')
                        <small id="phoneId" class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label for="location" class="form-label">Alamat Lengkap</label>
                    <textarea class="form-control @error('location') is-invalid @enderror" wire:model="location"
                        id="location" aria-describedby="locationId" placeholder=" Enter Alamat Lengkap"
                        rows="4"></textarea>

                    @error('location')
                        <small id="locationId" class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md">
                <div class="mb-3">
                    <label for="daily_price" class="form-label">Harga Perhari</label>
                    <input type="number" class="form-control @error('daily_price') is-invalid @enderror"
                        wire:model="daily_price" id="daily_price" aria-describedby="daily_priceId"
                        placeholder="Enter daily_price" />
                    @error('daily_price')
                        <small id="daily_priceId" class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md">
                <div class="mb-3">
                    <label for="monthly_price" class="form-label">Harga Perbulan</label>
                    <input type="number" class="form-control @error('monthly_price') is-invalid @enderror"
                        wire:model="monthly_price" id="monthly_price" aria-describedby="monthly_priceId"
                        placeholder="Enter monthly_price" />
                    @error('monthly_price')
                        <small id="monthly_priceId" class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label for="facilities" class="form-label">Fasilitas</label>
                    <div wire:ignore>
                        <input type="text" wire:model="facilities" id="input-tags" aria-describedby="facilitiesId"
                            value="{{ implode(',', $facilities) }}" autocomplete="facilities" />
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
                    <label for="description" class="form-label">Keterangan Kost</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" wire:model="description"
                        id="description" aria-describedby="descriptionId" placeholder=" Enter Alamat Lengkap"
                        rows="4"></textarea>

                    @error('description')
                        <small id="descriptionId" class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>




            <div class="col-12 ">
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
            </div>
        </div>



        <div class="row mb-3">

        </div>
    </form>
</div>
@endvolt
