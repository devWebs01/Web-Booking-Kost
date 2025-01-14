<?php

use App\Models\Image;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, uses, usesFileUploads};
use function Laravel\Folio\name;

uses([LivewireAlert::class]);
usesFileUploads();

name('images.create');

state(['image_path']);

$create = function () {
    $this->validate([
        'image_path' => 'image|required',
    ]);

    $path = $this->image_path->store('images', 'public'); // Simpan ke folder "fields" di storage
    Image::create([
        'image_path' => $path,
    ]);

    $this->alert('success', 'Data berhasil ditambahkan!', [
        'position' => 'center',
        'timer' => 3000,
        'toast' => true,
    ]);

    $this->redirectRoute('images.index');
};

?>

<x-admin-layout>
    <x-slot name="title">Tambah image Baru</x-slot>


    @volt
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="alert alert-primary" role="alert">
                        <strong>Tambah Galleri</strong>
                        <p>Pada halaman tambah galleri, kamu dapat memasukkan informasi dari galleri baru yang akan disimpan
                            ke
                            sistem.
                        </p>
                    </div>
                </div>

                <div class="card-body">
                    <form wire:submit="create">
                        @csrf

                        <div class="row">

                            @if ($image_path)
                                <div class="col-12">
                                    <div class="card mb-0">
                                        <div class="card-img-top">
                                            <img src="{{ $image_path->temporaryUrl() }}" class="img"
                                                style="object-fit: cover;" width="100%" height="500px" alt="preview">
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text text-center">
                                            {{ Str::limit($image_path->getClientOriginalName(), 20, '...') }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="image_path" class="form-label">
                                        Gambar
                                    </label>
                                    <input type="file" class="form-control @error('image_path') is-invalid @enderror"
                                        wire:model="image_path" id="image_path" aria-describedby="image_pathId"
                                        placeholder="Enter gallery image_path" autofocus autocomplete="image_path" />
                                    @error('image_path')
                                        <small id="image_pathId" class="form-text text-danger">{{ $message }}</small>
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
