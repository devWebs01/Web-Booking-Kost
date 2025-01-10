<?php

use App\Models\Room;
use App\Models\Image;
use App\Models\Setting;
use function Livewire\Volt\{state, rules, computed};
use function Laravel\Folio\name;

name('welcome');

state([
    'rooms' => fn() => Room::where('room_status', 'available')
        ->with(['images', 'facilities'])
        ->get(),
    'images' => fn() => Image::limit(10)->get('image_path'),
    'setting' => fn() => Setting::first(['name', 'location', 'description']),
]);

?>

<x-guest-layout>
    <x-slot name="title">Selamat Datang</x-slot>

    @volt
        <div>
            <section id="slider" data-aos="fade-up">
                <div class="container-fluid padding-side">
                    <div class="d-flex rounded-5"
                        style="background-image: url(/guest/images/slider-image.jpg); background-size: cover; background-repeat: no-repeat; height: 85vh; background-position: center;">
                        <div class="row align-items-center m-auto pt-5 px-4 px-lg-0">
                            <div class="text-start col-md-6 col-lg-5 col-xl-6 offset-lg-1">
                                <h2 class="display-1 fw-normal">{{ $setting->name }}, Solusi Hunian Nyaman dan Strategis.
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="about-us" class="padding-large">
                <div class="container-fluid padding-side" data-aos="fade-up">
                    <h3 class="display-3 text-center fw-normal col-lg-12 offset-lg-12">Pilihan Tepat Untuk Anda
                    </h3>
                    <div class="row align-items-start mt-3 mt-lg-5">
                        <div class="col-6">
                            <div class="p-5">
                                <p>Selamat datang di {{ $setting->name }}, tempat tinggal yang mengutamakan kenyamanan dan
                                    lokasi
                                    strategis.
                                    Cocok untuk mahasiswa, pekerja, atau siapa saja yang mencari hunian modern dengan
                                    fasilitas lengkap.
                                    Nikmati suasana yang nyaman dan tenang untuk mendukung produktivitas Anda.</p>
                                <a href="/" class="btn btn-arrow btn-primary mt-3">
                                    <span>Pelajari Lebih Lanjut <svg width="18" height="18">
                                            <use xlink:href="#arrow-right"></use>
                                        </svg></span>
                                </a>
                            </div>
                        </div>
                        <img src="https://images.oyoroomscdn.com/uploads/hotel_image/199499/large/74fa41eef0df8984.jpg"
                            alt="img" class="img-fluid rounded-4 mt-4 col-6">
                    </div>
                </div>
            </section>

            <section id="room">
                <div class="container-fluid padding-side" data-aos="fade-up">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div>
                            <h3 class="display-3 fw-normal text-center">Jelajahi Kamar</h3>
                        </div>
                        <a href="/" class="btn btn-arrow btn-primary mt-3">
                            <span>Lihat Lebih<svg width="18" height="18">
                                    <use xlink:href="#arrow-right"></use>
                                </svg></span>
                        </a>
                    </div>

                    <div class="swiper room-swiper mt-5">
                        <div class="swiper-wrapper">
                            @foreach ($rooms as $room)
                                <div class="swiper-slide">
                                    <div class="room-item position-relative bg-black rounded-4 overflow-hidden">
                                        <img src="{{ Storage::url($room->images->first()->image_path) }}" alt="img"
                                            class="post-image img-fluid w-100 rounded-4">
                                        <div class="product-description position-absolute p-5 text-start">
                                            <p class="product-paragraph text-white">
                                                {{ $room->description }}
                                            </p>
                                            <table>
                                                <tbody>
                                                    <tr class="text-white">
                                                        <td class="pe-2">Perhari:</td>
                                                        <td class="price">{{ formatRupiah($room->daily_price) }} /Perhari
                                                        </td>
                                                    </tr>
                                                    <tr class="text-white">
                                                        <td class="pe-2">Perbulan:</td>
                                                        <td class="price">{{ formatRupiah($room->monthly_price) }} /Perhari
                                                        </td>
                                                    </tr>
                                                    <tr class="text-white">
                                                        <td class="pe-2">Fasilitas:</td>
                                                        <td>
                                                            @foreach ($room->facilities->take(4) as $facility)
                                                                {{ $facility->name }}{{ !$loop->last ? ',' : '...' }}
                                                            @endforeach
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <a href="/">
                                                <p class="text-decoration-underline text-white m-0 mt-2">
                                                    Jelajahi Sekarang
                                                </p>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="room-content text-center mt-3">
                                        <h4 class="display-6 fw-normal">
                                            <a href="/">
                                                {{ $setting->name }}
                                            </a>
                                        </h4>

                                    </div>
                                </div>
                            @endforeach

                        </div>
                        <div class="swiper-pagination room-pagination position-relative mt-5"></div>
                    </div>
                </div>
            </section>

            <section id="gallery" data-aos="fade-up">
                <h3 class="display-3 fw-normal text-center">Galeri Kami</h3>
                <p class="text-center col-lg-4 offset-lg-4 mb-5">Jelajahi gambar-gambar fasilitas kami yang dirancang
                    modern dengan dekorasi yang nyaman untuk membuat pengalaman Anda tak terlupakan. </p>
                <div class="container position-relative">
                    <div class="row">
                        <div class="swiper gallery-swiper offset-1 col-10">
                            <div class="swiper-wrapper">
                                @foreach ($images as $image)
                                    <div class="swiper-slide">
                                        <img src="{{ Storage::url($image->image_path) }}" alt="gambar 1"
                                            class="img-fluid w-100 rounded-4">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div
                        class="position-absolute top-50 start-0 translate-middle-y main-slider-button-prev d-none d-md-block">
                        <svg class="bg-secondary rounded-circle p-3" width="70" height="70">
                            <use xlink:href="#arrow-left"></use>
                        </svg>
                    </div>
                    <div
                        class="position-absolute top-50 end-0 translate-middle-y main-slider-button-next d-none d-md-block">
                        <svg class="bg-secondary rounded-circle p-3" width="70" height="70">
                            <use xlink:href="#arrow-right"></use>
                        </svg>
                    </div>
                </div>
            </section>

            <section id="services" class="padding-medium">
                <div class="container-fluid padding-side" data-aos="fade-up">
                    <h3 class="display-3 text-center fw-normal col-lg-4 offset-lg-4">Fasilitas & Layanan Kami</h3>
                    <div class="row mt-5">
                        <div class="col-md-6 col-xl-4">
                            <div class="service mb-4 text-center rounded-4 p-5">
                                <div class="position-relative">
                                    {{-- icons --}}
                                </div>
                                <h4 class="display-6 fw-normal my-3">AC</h4>
                                <p>Kami menyediakan fasilitas pendingin ruangan (AC) di setiap kamar untuk memastikan
                                    kenyamanan Anda selama menginap.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <div class="service mb-4 text-center rounded-4 p-5">
                                <div class="position-relative">
                                    {{-- icons --}}
                                </div>
                                <h4 class="display-6 fw-normal my-3">TV</h4>
                                <p>Setiap kamar dilengkapi dengan TV modern untuk hiburan Anda, menawarkan berbagai saluran
                                    lokal dan internasional.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <div class="service mb-4 text-center rounded-4 p-5">
                                <div class="position-relative">
                                    {{-- icons --}}
                                </div>
                                <h4 class="display-6 fw-normal my-3">Reception</h4>
                                <p>Resepsionis kami siap melayani Anda selama 24 jam untuk memenuhi segala kebutuhan dan
                                    pertanyaan Anda selama menginap.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    @endvolt
</x-guest-layout>
