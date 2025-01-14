<?php

use App\Models\Room;
use App\Models\Image;
use App\Models\Setting;
use function Livewire\Volt\{state, rules, computed};
use function Laravel\Folio\name;

name('bookings.index');

state([
    'setting' => fn() => Setting::first(['name', 'location', 'description']),
    'images' => fn() => Image::get(),
    'room' => fn() => Room::first(),
]);

?>

<x-guest-layout>
    @include('layouts.fancybox')

    @volt
        <div>
            <x-slot name="title">Reservasi Kamar {{ $setting->name }}</x-slot>

            
            <section class="pb-5">
                <div class="container">
                    <div class="row gx-2">
                        <aside class="col-12">
                            <div class="card rounded-4 mb-3" style="width: 100%; height: 550px">
                                <a href="{{ Storage::url($images->first()->image_path) }}" data-fancybox="gallery"
                                    data-src="{{ Storage::url($images->first()->image_path) }}">
                                    <img class="card-img-top" src="{{ Storage::url($images->first()->image_path) }}"
                                        width=100%; height=550px; style="object-fit: cover;" alt="card-img-top">
                                </a>
                            </div>

                            <div class="d-flex flex-row gap-1 overflow-auto">
                                @foreach ($images as $image)
                                    <div class="col">
                                        <div class="card rounded-4 mb-3" style="width: 100px; height: 100px">
                                            <a href="{{ Storage::url($image->image_path) }}" data-fancybox="gallery">
                                                <img class="card-img-top" src="{{ Storage::url($image->image_path) }}"
                                                    width=100px; height=100px; style="object-fit: cover;"
                                                    alt="other images">
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </aside>

                        <div class="col-12 mt-5">
                            <h2 id="font-custom" class="display-3 text-dark fw-bold">
                                {{ $setting->name }}
                            </h2>

                            <div class="row">
                                <div class="col-md">
                                    <div class="pb-5">
                                        <p>
                                            <span class="fs-4 fw-bold">
                                                {{ formatRupiah($room->daily_price) }}
                                            </span>
                                        </p>

                                        <div class="my-3">
                                            <p>
                                                <strong>Perhari:</strong> {{ formatRupiah($room->daily_price) }} /Perhari
                                            </p>
                                            <p>
                                                <strong>Perbulan:</strong> {{ formatRupiah($room->monthly_price) }} /Perhari
                                            </p>
                                            <p>
                                                <strong>Fasilitas:</strong>
                                                @foreach ($room->facilities as $facility)
                                                    {{ $facility->name }}
                                                @endforeach
                                            </p>
                                        </div>

                                        <p class="mb-3 fs-5">
                                            {{ $room->description }}
                                        </p>

                                    </div>
                                </div>
                                <div class="col-md">
                                    @include('pages.user.bookings.form')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @endvolt


</x-guest-layout>
