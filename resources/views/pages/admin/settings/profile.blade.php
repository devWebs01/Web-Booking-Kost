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
    'phone' => fn() => $this->getSetting->phone ?? '',
    'location' => fn() => $this->getSetting->location ?? '',
    'description' => fn() => $this->getSetting->description ?? '',
    'expire_time' => fn() => $this->getSetting->expire_time ?? '',
]);

rules([
    'name' => 'required|min:5',
    'phone' => 'required|numeric',
    'location' => 'required|min:5',
    'description' => 'required|min:5',
    'expire_time' => 'required|numeric|min:5',
]);

$save = function () {
    $validate = $this->validate();

    Setting::updateOrCreate(['id' => $this->getSetting ? Setting::first()->id : null], $validate);

    $this->alert('success', 'Data berhasil diupdate!', [
        'position' => 'center',
        'timer' => 3000,
        'toast' => true,
    ]);
};

?>
@volt
    <div>

        <form wire:submit="save">
            @csrf

            <div class="row mb-3">

                <div class="col-md">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Kost</label>
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

                <div class="mb-3">
                    <label for="expire_time" class="form-label">Waktu Expire Pemesanan</label>

                    <div class="input-group ">
                        <input type="text" wire:model='expire_time' class="form-control @error('expire_time') is-invalid @enderror" aria-label="Expire time input"
                            aria-describedby="expire-time-default">
                        <span class="input-group-text" id="expire-time-default">Menit</span>
                    </div>
                    @error('expire_time')
                        <small id="expire_timeId" class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="mb-3">
                        <label for="location" class="form-label">Alamat Kost</label>
                        <textarea class="form-control @error('location') is-invalid @enderror" wire:model="location" id="location"
                            aria-describedby="locationId" placeholder=" Enter Alamat Lengkap" rows="4"></textarea>

                        @error('location')
                            <small id="locationId" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>


                <div class="col-12">
                    <div class="mb-3">
                        <label for="description" class="form-label">Keterangan Kost</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" wire:model="description" id="description"
                            aria-describedby="descriptionId" placeholder=" Enter Alamat Lengkap" rows="4"></textarea>

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

        </form>
    </div>
@endvolt
