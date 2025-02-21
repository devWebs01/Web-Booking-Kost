<?php

use function Laravel\Folio\name;

name('settings.index');



?>
<x-admin-layout>
    <x-slot name="title">Pengaturan</x-slot>
    <x-slot name="header">
        <li class="breadcrumb-item">
            <a href="{{ route('home') }}">Beranda</a>
        </li>
        <li class="breadcrumb-item active">Pengaturan Kost</li>
    </x-slot>


    @volt
        <div>
            <div class="card overflow-hidden">
                <div class="card-header p-0">
                    <img src="https://bootstrapdemos.adminmart.com/matdash/dist/assets/images/backgrounds/profilebg.jpg"
                        alt="matdash-img" class="img-fluid">
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                          <button class="nav-link active" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="true">Profile Kost</button>
                        </li>

                        <li class="nav-item" role="presentation">
                          <button class="nav-link" id="pills-facility-tab" data-bs-toggle="pill" data-bs-target="#pills-facility" type="button" role="tab" aria-controls="pills-facility" aria-selected="false">Fasilitas & Gambar</button>
                        </li>
                      </ul>
                      <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                            @include('pages.admin.settings.profile')
                        </div>
                        <div class="tab-pane fade" id="pills-facility" role="tabpanel" aria-labelledby="pills-facility-tab" tabindex="0">
                            @include('pages.admin.settings.facility&image')
                        </div>
                      </div>
                    
                </div>
            </div>
        </div>
    @endvolt
</x-admin-layout>
