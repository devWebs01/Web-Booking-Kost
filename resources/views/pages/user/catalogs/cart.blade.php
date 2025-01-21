<?php

use App\Models\Cart;
use function Laravel\Folio\name;
use function Livewire\Volt\{computed, state, usesPagination, uses};
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

name('catalogs.carts');

state(['search'])->url();
usesPagination(theme: 'bootstrap');

$carts = computed(function () {
    if ($this->search == null) {
        return cart::query()->latest()->paginate(10);
    } else {
        return cart::query()
            ->where(function ($query) {
                // isi
                $query->whereAny([' '], 'LIKE', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);
    }
});



?>

<x-admin-layout>
    <div>
        <x-slot name="title">Data cart</x-slot>


        @volt
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <a href="{{ route('carts.create') }}" class="btn btn-primary">Tambah
                                cart</a>
                        </div>
                        <div class="col">
                            <input wire:cart.live="search" type="search" class="form-control" name="search" id="search"
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
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Telp</th>
                                    <th>Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($this->carts as $no => $item)
                                    <tr>
                                        <td>{{ ++$no }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>{{ $item->telp }}</td>
                                        <td>
                                            <div>
                                                <a href="{{ route('carts.edit', ['cart' => $item->id]) }}"
                                                    class="btn btn-sm btn-warning">Edit</a>
                                                <button wire:loading.attr='disabled' wire:click='destroy({{ $item->id }})'
                                                    wire:confirm="Apakah kamu yakin ingin menghapus data ini?"
                                                    class="btn btn-sm btn-danger">
                                                    {{ __('Hapus') }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>

                        <div class="container d-flex justify-content-center">
                            {{ $this->carts->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
        @endvolt

    </div>
</x-admin-layout>
