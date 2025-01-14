<?php

use App\Models\Room;
use App\Models\Image;
use App\Models\Setting;
use function Livewire\Volt\{state, rules, computed};
use function Laravel\Folio\name;

name('welcome');

state([
    'images' => fn() => Image::limit(10)->get('image_path'),
    'setting' => fn() => Setting::first(),
    'contactName',
    'contactMessage',
]);

$contactUs = function () {
    return 'https://wa.me/' . $this->setting->phone . '?text=Hallo%20Admin%0Asaya%20' . $this->contactName . ',%20ingin%20bertanya%20tentang%20' . $this->contactMessage . '!';
};

?>

<x-guest-layout>
    <x-slot name="title">Selamat Datang</x-slot>
    {{-- @include('layouts.fancybox') --}}
    @volt
        <div>
            <section id="slider" data-aos="fade-up">
                <div class="container-fluid padding-side">
                    <div class="d-flex rounded-5"
                        style="background-image: url(/guest/images/slider-image.jpg); background-size: cover; background-repeat: no-repeat; height: 85vh; background-position: center;">
                        <div class="row align-items-center m-auto pt-5 px-4 px-lg-0">
                            <div class="text-start col-md-6 col-lg-5 col-xl-6 offset-lg-1">
                                <h2 class="display-1 fw-bold">{{ $setting->name }}, Solusi Hunian Nyaman dan Strategis.
                                </h2>
                            </div>
                          
                        </div>
                    </div>
                </div>
            </section>

            <section id="services" class="padding-medium">
                <div class="container-fluid padding-side" data-aos="fade-up">
                    <h3 class="display-3 text-center fw-bold col-lg-4 offset-lg-4">Fasilitas & Layanan Kami</h3>
                    <div class="row mt-5">
                        <div class="col-md-6 col-xl-4">
                            <div class="service mb-4 text-center rounded-4 p-5">
                                <div class="position-relative">
                                    {{-- icons --}}
                                </div>
                                <h4 class="display-6 fw-bold my-3">AC</h4>
                                <p>Kami menyediakan fasilitas pendingin ruangan (AC) di setiap kamar untuk memastikan
                                    kenyamanan Anda selama menginap.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <div class="service mb-4 text-center rounded-4 p-5">
                                <div class="position-relative">
                                    {{-- icons --}}
                                </div>
                                <h4 class="display-6 fw-bold my-3">TV</h4>
                                <p>Setiap kamar dilengkapi dengan TV modern untuk hiburan Anda, menawarkan berbagai saluran
                                    lokal dan internasional.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <div class="service mb-4 text-center rounded-4 p-5">
                                <div class="position-relative">
                                    {{-- icons --}}
                                </div>
                                <h4 class="display-6 fw-bold my-3">Reception</h4>
                                <p>Resepsionis kami siap melayani Anda selama 24 jam untuk memenuhi segala kebutuhan dan
                                    pertanyaan Anda selama menginap.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="gallery" data-aos="fade-up" class="mb-5 pb-5">
                <h3 class="display-3 fw-bold text-center">Galeri Kami</h3>
                <p class="text-center col-lg-4 offset-lg-4 mb-5">Jelajahi gambar-gambar fasilitas kami yang dirancang
                    modern dengan dekorasi yang nyaman untuk membuat pengalaman Anda tak terlupakan. </p>

                <div class="container-fluid padding-side">
                    <div class="swiper room-swiper mt-5">
                        <div class="swiper-wrapper">
                            @foreach ($images as $image)
                                <div class="swiper-slide">
                                    <a data-fancybox="gallery" data-src="{{ Storage::url($image->image_path) }}">
                                        <div class="room-item position-relative bg-black rounded-4 overflow-hidden">
                                            <img src="{{ Storage::url($image->image_path) }}" alt="img"
                                                class="img-fluid w-100 rounded-4">
                                        </div>
                                    </a>
                                </div>
                            @endforeach

                        </div>
                        <div class="swiper-pagination room-pagination position-relative mt-5">

                        </div>
                    </div>
                </div>
            </section>


            <section id="room" class="py-5">
                <div class="container-fluid padding-side" data-aos="fade-up">
                    <h3 class="display-3 fw-bold text-center">Alamat Kami</h3>
                    <p class="text-center col-lg-4 offset-lg-4 mb-5">Temukan kami di lokasi strategis yang mudah dijangkau.
                        Dengan suasana yang nyaman, kami siap menyambut Anda untuk pengalaman terbaik.</p>


                    <div class="position-relative py-5">
                        <!-- Map Section -->
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="z-index: 0;">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31905.916563537892!2d103.56199747431639!3d-1.6118775999999932!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e2589bd7c57eff7%3A0xbd594eab55a6424d!2sOYO%2092054%20Gala%20Residence!5e0!3m2!1sid!2sid!4v1736704357730!5m2!1sid!2sid"
                                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>

                        <!-- Contact Form Section -->
                        <div class="position-relative" style="z-index: 1;">
                            <div class="container">
                                <div class="row justify-content-end">
                                    <div class="col-lg-6">
                                        <div class="bg-white p-5 rounded shadow mt-5">
                                            <h2 class="display-6 fw-bold text-center mb-4">Kontak Kami</h2>
                                            <form class="rounded">
                                                <div class="row">
                                                    <!-- Input Nama -->
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <input wire:model.live="contactName"
                                                                class="form-control bg-light" placeholder="Nama Lengkap"
                                                                type="text" required>
                                                        </div>
                                                    </div>
                                                    <!-- Input Pesan -->
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <textarea wire:model.live="contactMessage" class="form-control bg-light" placeholder="Tulis Pesan..." rows="4"
                                                                required></textarea>
                                                        </div>
                                                    </div>
                                                    <!-- Tombol Kirim -->
                                                    <div class="col-md-12">
                                                        <div class="d-grid">
                                                            <a class="btn btn-primary" target="_blank"
                                                                href="https://wa.me/{{ $setting->phone }}?text=Hallo%20Admin%0A%0ASaya%20{{ urlencode($this->contactName) }}%0A%0AIngin%20bertanya%20tentang%20{{ urlencode($this->contactMessage) }}">
                                                                Send message
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>

        </div>
    @endvolt
</x-guest-layout>
