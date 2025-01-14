<?php

use App\Models\image;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Livewire\Volt\{state, rules, uses, usesFileUploads};
use function Laravel\Folio\name;

uses([LivewireAlert::class]);
usesFileUploads();

name('images.edit');

state([
    'image_path' => fn() => $this->image->image_path,
    'new_image',
    'image',
]);

rules([
    'new_image' => 'nullable|image',
]);

$edit = function () {
    $image = $this->image;
    $new_image = $this->new_image;

    if ($new_image) {
        Storage::disk('public')->delete($image->image_path);

        $path = $this->new_image->store('images', 'public'); // Simpan ke folder "fields" di storage
        $image->update([
            'image_path' => $path,
        ]);
    }

    $this->alert('success', 'Data berhasil diedit!', [
        'position' => 'center',
        'timer' => 3000,
        'toast' => true,
    ]);

    $this->redirectRoute('images.index');
};

?>

<x-admin-layout>
    <x-slot name="title">Edit image</x-slot>


    @volt
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="alert alert-primary" role="alert">
                        <strong>Edit Gambar</strong>
                        <p>Pada halaman edit gambar, kamu dapat mengubah informasi gambar yang sudah ada.
                        </p>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit="edit">
                        @csrf

                        <div class="row">

                            @if ($new_image !== null)
                                <div class="col-12">
                                    <div class="card mb-0">
                                        <div class="card-img-top">
                                            <img src="{{ $new_image->temporaryUrl() }}" class="img"
                                                style="object-fit: cover;" width="100%" height="500px" alt="preview">
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text text-center">
                                            {{ Str::limit($new_image->getClientOriginalName(), 20, '...') }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-img-top">
                                            <img src="{{ Storage::url($image_path) }}" class="img"
                                                style="object-fit: cover;" width="100%" height="500px" alt="preview">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="new_image" class="form-label">Gambar</label>
                                    <input type="file" class="form-control @error('new_image') is-invalid @enderror"
                                        wire:model="new_image" id="new_image" aria-describedby="new_imageId"
                                        placeholder="Enter image new_image" autofocus autocomplete="new_image" />
                                    @error('new_image')
                                        <small id="new_imageId" class="form-text text-danger">{{ $message }}</small>
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
