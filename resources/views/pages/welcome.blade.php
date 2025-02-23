<?php

use App\Models\Room;
use App\Models\Image;
use App\Models\Setting;
use function Livewire\Volt\{state, rules, computed};
use function Laravel\Folio\name;

name('welcome');

state([
    'images' => fn() => Image::limit(4)->get('image_path'),
    'setting' => fn() => Setting::first(),
]);

?>

<x-guest-layout>
    <x-slot name="title">Selamat Datang di Kost Syariah Gala Residence</x-slot>
    @volt
        <div>

            <!-- Carousel Start -->
            <section class="py-5 d-flex align-items-center position-relative wow fadeInUp" data-wow-delay="0.2s"
                style="height: 500px;">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="text-center">
                                <h1 class="display-3 fw-bold">Selamat Datang di <span class="text-primary">Kost Syariah Gala
                                        Residence</span>
                                </h1>
                                <p class="lead py-3 py-md-4">Kost syariah yang nyaman dan aman untuk Anda. Nikmati fasilitas
                                    lengkap dan lingkungan yang mendukung.</p>
                            </div>
                        </div>
                    </div>
                    <a class="position-absolute bottom-0 start-50 translate-middle text-primary" href="#features">
                        <i class='display-6 bx bx-down-arrow-alt'>
                        </i>
                    </a>
                </div>
            </section>
            <!-- Carousel End -->

            <section class="py-5 wow fadeInUp" data-wow-delay="0.2s">
                <div class="container">
                    <div class="row gx-4 align-items-center">
                        <div class="col-md-6">
                            <div class="me-md-2 me-lg-5">
                                <img class="img-fluid rounded-3"
                                    src="https://cf.bstatic.com/xdata/images/hotel/max1024x768/416419334.jpg?k=98cd936656590d0e21b91de910cdfc28e77ee337768b7b99be056ca1608051c0&o="
                                    style="object-fit: cover;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="ms-md-2 ms-lg-5 mt-5 mt-md-0">
                                <span class="text-muted">Tentang Kami</span>
                                <h2 class="display-5 fw-bold">Kost Syariah Gala Residence</h2>
                                <p class="lead">Kami menyediakan tempat tinggal yang nyaman dan sesuai dengan prinsip
                                    syariah. Dengan fasilitas yang lengkap, kami berkomitmen untuk memberikan pengalaman
                                    tinggal yang terbaik.</p>
                                <p class="lead">Kami mengutamakan kenyamanan dan keamanan penghuni kami. Bergabunglah
                                    dengan komunitas kami yang harmonis.</p>
                                <a class="btn btn-primary" href="#">Pelajari lebih lanjut</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="features" class="bg-light py-5 wow fadeInUp" data-wow-delay="0.2s">
                <div class="container mt-5 pt-5">
                    <div class="row justify-content-center text-center mb-3">
                        <div class="col-lg-8 col-xl-7">
                            <span class="text-muted">Fasilitas</span>
                            <h2 class="display-5 fw-bold">Fasilitas Kost Syariah</h2>
                            <p class="lead">Kami menyediakan fasilitas lengkap untuk mendukung kenyamanan dan keamanan penghuni sesuai dengan prinsip syariah.</p>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-md-4 order-2 order-md-1">
                            <div class="d-flex mb-4 mt-4 mt-md-0">
                                <div class="text-muted">
                                    
                                </div>
                                <div class="ms-4">
                                    <h5 class="fw-bold">Lingkungan Islami</h5>
                                    <p class="fw-light">Kost dengan aturan syariah yang menjamin suasana islami dan nyaman.</p>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="text-muted">
                                    
                                </div>
                                <div class="ms-4">
                                    <h5 class="fw-bold">Internet Gratis</h5>
                                    <p class="fw-light">Wi-Fi cepat dan stabil untuk menunjang aktivitas sehari-hari.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 pe-lg-5 order-1 order-md-2">
                            <img class="img rounded" style="width:100%; height:300px; object-fit:cover;" 
                                 src="https://cf.bstatic.com/xdata/images/hotel/max1024x768/416419331.jpg?k=f47c9b054754cdc3e991c0d122ce0fd85ed23aa46bf37460d0f0834b0422ad1c&o=&hp=1">
                        </div>
                        <div class="col-md-4 ps-lg-5 order-3 order-md-3">
                            <div class="d-flex mb-4">
                                <div class="text-muted">
                                    
                                </div>
                                <div class="ms-4">
                                    <h5 class="fw-bold">Listrik & Air 24 Jam</h5>
                                    <p class="fw-light">Pasokan listrik dan air yang stabil untuk kenyamanan penghuni.</p>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="text-muted">
                                    
                                </div>
                                <div class="ms-4">
                                    <h5 class="fw-bold">Privasi Terjaga</h5>
                                    <p class="fw-light">Pemilahan area laki-laki dan perempuan untuk menjaga kenyamanan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <section class="bg-light py-5 wow fadeInUp" data-wow-delay="0.2s">
                <div class="container">
                    <div class="row justify-content-center text-center mb-2 mb-lg-4">
                        <div class="col-12 col-lg-8 col-xxl-7 text-center mx-auto">
                            <span class="text-muted">Galeri</span>
                            <h2 class="display-5 fw-bold">Galeri Kami</h2>
                            <p class="lead">Lihat berbagai fasilitas dan suasana di Kost Syariah Gala Residence.</p>
                        </div>
                    </div>

                    <!-- Bagian Gambar Utama -->
                    @if ($images->count() > 0)
                        <div class="row py-3 align-items-center">
                            <div class="col-md-6 mt-md-0 mt-4">
                                <div class="mb-5 mb-lg-3">
                                    <img class="img w-100 rounded shadow" style="height:400px; object-fit: cover;"
                                        src="{{ Storage::url($images->first()->image_path) }}" alt="Galeri Utama">
                                </div>
                            </div>
                            <div class="col-md-6 ps-md-5">
                                <div class="mb-5 mb-lg-3">
                                    <h4 class="fw-bold">Hunian Nyaman & Islami</h4>
                                    <p>Kami menyediakan lingkungan yang nyaman dan aman untuk Anda. Bergabunglah dengan kami
                                        dan nikmati pengalaman tinggal yang menyenangkan.</p>
                                    <p>Jelajahi galeri kami untuk melihat lebih banyak tentang Kost Syariah Gala Residence.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Bagian Thumbnail Galeri -->
                    <div class="row mt-2">
                        @foreach ($images as $image)
                            <div class="col-lg-3 col-md-6 mb-3">
                                <img class="img w-100 rounded shadow" src="{{ Storage::url($image->image_path) }}"
                                    style="height:400px; object-fit: cover;" alt="Galeri Kost">
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="py-5 wow fadeInUp" data-wow-delay="0.2s">
                <div class="container">
                    <div class="row justify-content-center text-center mb-2 mb-lg-4">
                        <div class="col-12 col-lg-8 col-xxl-7 text-center mx-auto">
                            <span class="text-muted">Harga</span>
                            <h2 class="display-5 fw-bold">Harga Kami</h2>
                            <p class="lead">Kami menawarkan harga yang kompetitif untuk fasilitas yang kami sediakan.</p>
                        </div>
                    </div>
                    <div class="row justify-content-center g-0">
                        <div class="col-md-5">
                            <div class="card text-center border-0">
                                <div class="card-body bg-light py-5">
                                    <div class="mb-3 mx-auto">
                                        <i class='display-5 bx bxs-calendar'>

                                        </i>
                                    </div>
                                    <h5 class="fw-bold">Harian</h5>
                                    <div class="display-3 fw-bold mt-4 text-primary">
                                        {{ formatRupiah($setting->daily_price) }}
                                    </div>
                                    <a class="btn btn-primary btn-lg mt-4" href="">Pelajari lebih lanjut</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 mt-md-0 mt-5">
                            <div class="card text-center border-0">
                                <div class="card-body bg-primary text-white py-5">
                                    <div class="mb-3 mx-auto">
                                        <i class='display-5 bx bx-calendar'>

                                        </i>
                                    </div>
                                    <h5 class="fw-bold text-white">Bulanan</h5>
                                    <div class="display-3 fw-bold mt-4">
                                        {{ formatRupiah($setting->monthly_price) }}
                                    </div>
                                    <a class="btn btn-light btn-lg mt-4" href="">Pelajari lebih lanjut</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="py-5 wow fadeInUp" data-wow-delay="0.2s">
                <div class="container">
                    <div class="row justify-content-center text-center">
                        <div class="col-lg-8">
                            <span class="text-muted">Kost Syariah</span>
                            <h2 class="fw-bold py-2 text-capitalize">
                                Kunjungi kami langsung dan rasakan pelayanan terbaik kami.
                            </h2>
                        </div>
                    </div>

                    <div class="card rounded">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31905.916563537892!2d103.56405741083984!3d-1.6118775999999932!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e2589bd7c57eff7%3A0xbd594eab55a6424d!2sOYO%2092054%20Gala%20Residence!5e0!3m2!1sid!2sid!4v1740199069490!5m2!1sid!2sid"
                            width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </section>
        </div>
    @endvolt
</x-guest-layout>
