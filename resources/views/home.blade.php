<?php


use function Livewire\Volt\{state};

state([]);

?>

<x-admin-layout>
    <x-slot name="title">Dashboard</x-slot>

    <x-slot name="header">
        <li class="breadcrumb-item active active">
            <a href="{{ route('home') }}">Home</a>
        </li>
    </x-slot>

    @volt
        <div>
            <div class="card">
                <div class="card-body">

                </div>
            </div>

        </div>
    @endvolt
</x-admin-layout>
