<?php

use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use function Livewire\Volt\{computed, uses};
use function Laravel\Folio\name;

uses([LivewireAlert::class]);

name("users.index");

$users = computed(function () {
    return User::query()->where("role", "admin")->latest()->paginate(10);
});

$destroy = function (User $user) {
    try {
        $user->delete();
        $this->alert("success", "Data user berhasil di hapus!", [
            "position" => "center",
            "timer" => 3000,
            "toast" => true,
        ]);
    } catch (\Throwable $th) {
        $this->alert("error", "Data user gagal di hapus!", [
            "position" => "center",
            "timer" => 3000,
            "toast" => true,
        ]);
    }

    $this->redirectRoute("users.index");
};

?>
<x-admin-layout>
    <div>
        <x-slot name="title">Admin</x-slot>
        <x-slot name="header">
            <li class="breadcrumb-item">
                <a href="{{ route("home") }}">Beranda</a>
            </li>
            <li class="breadcrumb-item active">Admin</li>
        </x-slot>

        @include("components.partials.datatables")

        @volt
            <div>
                <div class="card">
                    <div class="card-header">
                        <a href="{{ route("users.create") }}" class="btn btn-primary">Tambah
                            Admin</a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive border rounded p-3">
                            <table class="table text-center text-nowrap">
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
                                    @foreach ($this->users as $no => $user)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->telp }}</td>
                                            <td>
                                                <div class="">
                                                    <a href="{{ route("users.edit", ["user" => $user->id]) }}"
                                                        class="btn btn-sm btn-warning">Edit</a>
                                                    <button wire:loading.attr='disabled'
                                                        wire:click='destroy({{ $user->id }})'
                                                        class="btn btn-sm btn-danger">
                                                        {{ __("Hapus") }}
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>

                            <div class="container d-flex justify-content-center">
                                {{ $this->users->links() }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @endvolt

    </div>
</x-admin-layout>
