<?php

use App\Models\Image;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};

uses([LivewireAlert::class]);

name('images.index');

usesPagination(theme: 'bootstrap');

$images = computed(function () {
    return image::query()->latest()->paginate(2);
});

$destroy = function (image $image) {
    try {
        Storage::disk('public')->delete($image->image_path);

        $image->delete();
        $this->alert('success', 'Data image berhasil dihapus!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);
    } catch (\Throwable $th) {
        $this->alert('error', 'Data image gagal dihapus!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
};

?>

<x-admin-layout>
    <div>
        <x-slot name="title">Data Gambar</x-slot>
        @include('layouts.fancybox')

        @volt
            <div>
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <a href="{{ route('images.create') }}" class="btn btn-primary">Tambah
                                    Gambar</a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive border rounded">
                            <table class="table table-striped text-center text-nowrap">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Gambar</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($this->images as $no => $item)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>
                                                <a data-fancybox data-src="{{ Storage::url($item->image_path) }}">
                                                    <img src="{{ Storage::url($item->image_path) }}"
                                                        class="img object-fit-cover rounded" alt="profile" width="100"
                                                        height="100" />
                                                </a>

                                            </td>

                                            <td class="align-content-center">

                                                <a href="{{ route('images.edit', ['image' => $item->id]) }}"
                                                    class="btn btn-warning">Edit</a>
                                                <button wire:loading.attr='disabled'
                                                    wire:click='destroy({{ $item->id }})'
                                                    wire:confirm="Apakah kamu yakin ingin menghapus data ini?"
                                                    class="btn btn-danger">
                                                    {{ __('Hapus') }}
                                                </button>

                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>

                            <div class="container d-flex justify-content-center">
                                {{ $this->images->links() }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @endvolt

    </div>
</x-admin-layout>
