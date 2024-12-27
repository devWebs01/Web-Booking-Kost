<?php

use function Laravel\Folio\name;
use function Livewire\Volt\{state, computed, rules};

name('account.auth');

?>
<x-admin-layout>
    <x-slot name="title">Akun Profile</x-slot>
    <x-slot name="header">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('account.auth') }}">Akun Profile</a></li>
    </x-slot>

    @volt
        <div>
            <div class="card overflow-hidden">
                <div class="card-header p-0">
                    <img src="https://img.freepik.com/premium-photo/abstract-background-design-images-wallpaper-ai-generated_643360-150284.jpg"
                        alt="matdash-img" width="100%" height="300px" class="object-fit-cover">
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist"
                            aria-orientation="vertical">

                            <button class="nav-link active" id="v-pills-profile-tab" data-bs-toggle="pill"
                                data-bs-target="#v-pills-profile" type="button" role="tab"
                                aria-controls="v-pills-profile" aria-selected="true">Akun Profile</button>

                            <button class="nav-link" id="v-pills-password-tab" data-bs-toggle="pill"
                                data-bs-target="#v-pills-password" type="button" role="tab"
                                aria-controls="v-pills-password" aria-selected="true">Ganti Password</button>


                        </div>
                        <div class="tab-content" id="v-pills-tabContent">

                            <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel"
                                aria-labelledby="v-pills-profile-tab" tabindex="0">
                                @include('pages.admin.account.profile')

                            </div>

                            <div class="tab-pane fade" id="v-pills-password" role="tabpanel"
                                aria-labelledby="v-pills-password-tab" tabindex="0">
                                @include('pages.admin.account.password')

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endvolt
</x-admin-layout>
