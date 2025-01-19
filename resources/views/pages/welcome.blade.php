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
]);

?>

<x-guest-layout>
    <x-slot name="title">Selamat Datang</x-slot>
    {{-- @include('layouts.fancybox') --}}
    @volt
    <div>

        <!-- Carousel Start -->
        <div class="header-carousel owl-carousel">
            <div class="header-carousel-item bg-primary">
                <div class="carousel-caption">
                    <div class="container">
                        <div class="row g-4 align-items-center">
                            <div class="col-lg-7 animated fadeInLeft">
                                <div class="text-sm-center text-md-start">
                                    <h4 class="text-white text-uppercase fw-bold mb-4">Selamat Datang di KostKu</h4>
                                    <h1 class="display-1 text-white mb-4">Kamar Kost Nyaman untuk Anda</h1>
                                    <p class="mb-5 fs-5">Temukan kamar kost terbaik yang sesuai dengan kebutuhan Anda.
                                        Kami menyediakan berbagai pilihan dengan harga terjangkau dan fasilitas lengkap.
                                    </p>

                                </div>
                            </div>
                            <div class="col-lg-5 animated fadeInRight">
                                <div class="calrousel-img" style="object-fit: cover;">
                                    <img src="/guest/img/carousel-2.png" class="img-fluid w-100" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-carousel-item bg-primary">
                <div class="carousel-caption">
                    <div class="container">
                        <div class="row gy-4 gy-lg-0 gx-0 gx-lg-5 align-items-center">
                            <div class="col-lg-5 animated fadeInLeft">
                                <div class="calrousel-img">
                                    <img src="/guest/img/carousel-2.png" class="img-fluid w-100" alt="">
                                </div>
                            </div>
                            <div class="col-lg-7 animated fadeInRight">
                                <div class="text-sm-center text-md-end">
                                    <h4 class="text-white text-uppercase fw-bold mb-4">Selamat Datang di KostKu</h4>
                                    <h1 class="display-1 text-white mb-4">Kamar Kost Nyaman untuk Anda</h1>
                                    <p class="mb-5 fs-5">Temukan kamar kost terbaik yang sesuai dengan kebutuhan Anda.
                                        Kami menyediakan berbagai pilihan dengan harga terjangkau dan fasilitas lengkap.
                                    </p>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Carousel End -->

        <!-- Feature Start -->
        <div class="container-fluid feature bg-light py-5">
            <div class="container py-5">
                <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
                    <h4 class="text-primary">Fitur Kami</h4>
                    <h1 class="display-4 mb-4">Kost yang Memberikan Kenyamanan dan Keamanan</h1>
                    <p class="mb-0">Kami menyediakan berbagai fasilitas untuk memastikan kenyamanan Anda selama tinggal
                        di kost kami. Temukan fitur-fitur unggulan yang kami tawarkan.
                    </p>
                </div>
                <div class="row g-4">
                    <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="feature-item p-4 pt-0">
                            <div class="feature-icon p-4 mb-4">
                                <i class="far fa-handshake fa-3x"></i>
                            </div>
                            <h4 class="mb-4">Kost Terpercaya</h4>
                            <p class="mb-4">Kami adalah penyedia kost yang telah berpengalaman dan terpercaya di
                                bidangnya.</p>
                            <a class="btn btn-primary rounded-pill py-2 px-4" href="#">Pelajari Lebih Lanjut</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.4s">
                        <div class="feature-item p-4 pt-0">
                            <div class="feature-icon p-4 mb-4">
                                <i class="fa fa-dollar-sign fa-3x"></i>
                            </div>
                            <h4 class="mb-4">Harga Terjangkau</h4>
                            <p class="mb-4">Kami menawarkan harga yang bersaing untuk semua jenis kamar kost.</p>
                            <a class="btn btn-primary rounded-pill py-2 px-4" href="#">Pelajari Lebih Lanjut</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.6s">
                        <div class="feature-item p-4 pt-0">
                            <div class="feature-icon p-4 mb-4">
                                <i class="fa fa-bullseye fa-3x"></i>
                            </div>
                            <h4 class="mb-4">Rencana Fleksibel</h4>
                            <p class="mb-4">Kami menawarkan berbagai pilihan rencana sewa yang fleksibel sesuai
                                kebutuhan Anda.</p>
                            <a class="btn btn-primary rounded-pill py-2 px-4" href="#">Pelajari Lebih Lanjut</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.8s">
                        <div class="feature-item p-4 pt-0">
                            <div class="feature-icon p-4 mb-4">
                                <i class="fa fa-headphones fa-3x"></i>
                            </div>
                            <h4 class="mb-4">Dukungan 24/7</h4>
                            <p class="mb-4">Tim kami siap membantu Anda kapan saja dengan layanan pelanggan yang
                                responsif.</p>
                            <a class="btn btn-primary rounded-pill py-2 px-4" href="#">Pelajari Lebih Lanjut</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Feature End -->

        <!-- About Start -->
        <div class="container-fluid bg-light about pb-5">
            <div class="container pb-5">
                <div class="row g-5">
                    <div class="col-xl-6 wow fadeInLeft" data-wow-delay="0.2s">
                        <div class="about-item-content bg-white rounded p-5 h-100">
                            <h4 class="text-primary">Tentang Kami</h4>
                            <h1 class="display-4 mb-4">Kost yang Nyaman dan Aman</h1>
                            <p>Kami menyediakan berbagai pilihan kamar kost yang nyaman dan aman untuk Anda. Dengan
                                fasilitas lengkap dan lokasi strategis, kami siap memenuhi kebutuhan Anda.</p>
                            <p>Kami berkomitmen untuk memberikan pelayanan terbaik dan pengalaman tinggal yang
                                menyenangkan.</p>
                            <p class="text-dark">
                                <i class="fa fa-check text-primary me-3"></i>Kami dapat menghemat uang Anda.
                            </p>
                            <p class="text-dark">
                                <i class="fa fa-check text-primary me-3"></i>Produksi atau perdagangan barang
                            </p>
                            <p class="text-dark mb-4">
                                <i class="fa fa-check text-primary me-3"></i>Kost kami fleksibel
                            </p>
                            <a class="btn btn-primary rounded-pill py-3 px-5" href="#">Informasi Lebih Lanjut</a>
                        </div>
                    </div>
                    <div class="col-xl-6 wow fadeInRight" data-wow-delay="0.2s">
                        <div class="bg-white rounded p-5 h-100">
                            <div class="row g-4 justify-content-center">
                                <div class="col-12">
                                    <div class="rounded bg-light">
                                        <img src="/guest/img/about-1.png" class="img-fluid rounded w-100" alt="">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="counter-item bg-light rounded p-3 h-100">
                                        <div class="counter-counting">
                                            <span class="text-primary fs-2 fw-bold" data-toggle="counter-up">129</span>
                                            <span class="h1 fw-bold text-primary">+</span>
                                        </div>
                                        <h4 class="mb-0 text-dark">Kamar Kost Tersedia</h4>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="counter-item bg-light rounded p-3 h-100">
                                        <div class="counter-counting">
                                            <span class="text-primary fs-2 fw-bold" data-toggle="counter-up">99</span>
                                            <span class="h1 fw-bold text-primary">+</span>
                                        </div>
                                        <h4 class="mb-0 text-dark">Penghargaan Diterima</h4>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="counter-item bg-light rounded p-3 h-100">
                                        <div class="counter-counting">
                                            <span class="text-primary fs-2 fw-bold" data-toggle="counter-up">556</span>
                                            <span class="h1 fw-bold text-primary">+</span>
                                        </div>
                                        <h4 class="mb-0 text-dark">Agen Terampil</h4>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="counter-item bg-light rounded p-3 h-100">
                                        <div class="counter-counting">
                                            <span class="text-primary fs-2 fw-bold" data-toggle="counter-up">967</span>
                                            <span class="h1 fw-bold text-primary">+</span>
                                        </div>
                                        <h4 class="mb-0 text-dark">Anggota Tim</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- About End -->

        <!-- Service Start -->
        <div class="container-fluid service py-5">
            <div class="container py-5">
                <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
                    <h4 class="text-primary">Layanan Kami</h4>
                    <h1 class="display-4 mb-4">Kami Menyediakan Layanan Terbaik</h1>
                    <p class="mb-0">Kami berkomitmen untuk memberikan layanan terbaik kepada semua penghuni kost kami.
                        Temukan layanan-layanan unggulan yang kami tawarkan.
                    </p>
                </div>
                <div class="row g-4 justify-content-center">
                    <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="service-item">
                            <div class="service-img">
                                <img src="/guest/img/blog-1.png" class="img-fluid rounded-top w-100" alt="">
                                <div class="service-icon p-3">
                                    <i class="fa fa-users fa-2x"></i>
                                </div>
                            </div>
                            <div class="service-content p-4">
                                <div class="service-content-inner">
                                    <a href="#" class="d-inline-block h4 mb-4">Kamar Kost Pribadi</a>
                                    <p class="mb-4">Kamar kost pribadi dengan fasilitas lengkap untuk kenyamanan Anda.
                                    </p>
                                    <a class="btn btn-primary rounded-pill py-2 px-4" href="#">Baca Selengkapnya</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.4s">
                        <div class="service-item">
                            <div class="service-img">
                                <img src="/guest/img/blog-2.png" class="img-fluid rounded-top w-100" alt="">
                                <div class="service-icon p-3">
                                    <i class="fa fa-hospital fa-2x"></i>
                                </div>
                            </div>
                            <div class="service-content p-4">
                                <div class="service-content-inner">
                                    <a href="#" class="d-inline-block h4 mb-4">Kamar Kost Bersih dan Sehat</a>
                                    <p class="mb-4">Kami menjaga kebersihan dan kesehatan kamar kost untuk kenyamanan
                                        Anda.</p>
                                    <a class="btn btn-primary rounded-pill py-2 px-4" href="#">Baca Selengkapnya</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.6s">
                        <div class="service-item">
                            <div class="service-img">
                                <img src="/guest/img/blog-3.png" class="img-fluid rounded-top w-100" alt="">
                                <div class="service-icon p-3">
                                    <i class="fa fa-car fa-2x"></i>
                                </div>
                            </div>
                            <div class="service-content p-4">
                                <div class="service-content-inner">
                                    <a href="#" class="d-inline-block h4 mb-4">Akses Mudah ke Transportasi</a>
                                    <p class="mb-4">Kost kami dekat dengan berbagai akses transportasi umum.</p>
                                    <a class="btn btn-primary rounded-pill py-2 px-4" href="#">Baca Selengkapnya</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.8s">
                        <div class="service-item">
                            <div class="service-img">
                                <img src="/guest/img/blog-4.png" class="img-fluid rounded-top w-100" alt="">
                                <div class="service-icon p-3">
                                    <i class="fa fa-home fa-2x"></i>
                                </div>
                            </div>
                            <div class="service-content p-4">
                                <div class="service-content-inner">
                                    <a href="#" class="d-inline-block h4 mb-4">Lingkungan Aman dan Nyaman</a>
                                    <p class="mb-4">Kami menjamin lingkungan yang aman dan nyaman untuk semua penghuni.
                                    </p>
                                    <a class="btn btn-primary rounded-pill py-2 px-4" href="#">Baca Selengkapnya</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 text-center wow fadeInUp" data-wow-delay="0.2s">
                        <a class="btn btn-primary rounded-pill py-3 px-5" href="#">Layanan Lebih Banyak</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Service End -->

        <!-- FAQs Start -->
        <div class="container-fluid faq-section bg-light py-5">
            <div class="container py-5">
                <div class="row g-5 align-items-center">
                    <div class="col-xl-6 wow fadeInLeft" data-wow-delay="0.2s">
                        <div class="h-100">
                            <div class="mb-5">
                                <h4 class="text-primary">Beberapa FAQ Penting</h4>
                                <h1 class="display-4 mb-0">Pertanyaan yang Sering Diajukan</h1>
                            </div>
                            <div class="accordion" id="accordionExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button border-0" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true"
                                            aria-controls="collapseOne">
                                            Q: Apa yang harus saya lakukan saat mendaftar?
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show active"
                                        aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                        <div class="accordion-body rounded">
                                            A: Anda hanya perlu mengisi formulir pendaftaran dan data yang
                                            diperlukan.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingTwo">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                            aria-expanded="false" aria-controls="collapseTwo">
                                            Q: Bagaimana cara membayar sewa kamar?
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse"
                                        aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            A: Pembayaran sewa dapat dilakukan melalui transfer bank.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingThree">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                            aria-expanded="false" aria-controls="collapseThree">
                                            Q: Apakah bisa menambah fasilitas lain?
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse"
                                        aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            A: Ya, kami menyediakan tambahan kasur jika memungkinkan.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 wow fadeInRight" data-wow-delay="0.4s">
                        <img src="/guest/img/carousel-2.png" class="img-fluid w-100" alt="">
                    </div>
                </div>
            </div>
        </div>
        <!-- FAQs End -->

        <!-- Testimonial Start -->
        <div class="container-fluid testimonial py-5">
            <div class="container pb-5">
                <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
                    <h4 class="text-primary">Testimonial</h4>
                    <h1 class="display-4 mb-4">Apa Kata Penghuni Kami</h1>
                    <p class="mb-0">Kami bangga dengan layanan kami dan senang mendengar umpan balik dari penghuni kami.
                        Berikut adalah beberapa testimonial dari mereka.
                    </p>
                </div>
                <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.2s">
                    <div class="testimonial-item bg-light rounded">
                        <div class="row g-0">
                            <div class="col-4 col-lg-4 col-xl-3">
                                <div class="h-100">
                                    <img src="/guest/img/testimonial-1.jpg" class="img-fluid h-100 rounded"
                                        style="object-fit: cover;" alt="">
                                </div>
                            </div>
                            <div class="col-8 col-lg-8 col-xl-9">
                                <div class="d-flex flex-column my-auto text-start p-4">
                                    <h4 class="text-dark mb-0">Nama Penghuni</h4>
                                    <p class="mb-3">Mahasiswa</p>
                                    <div class="d-flex text-primary mb-3">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <p class="mb-0">Kost ini sangat nyaman dan aman. Fasilitasnya lengkap dan
                                        pelayanannya sangat baik.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-item bg-light rounded">
                        <div class="row g-0">
                            <div class="col-4 col-lg-4 col-xl-3">
                                <div class="h-100">
                                    <img src="/guest/img/testimonial-2.jpg" class="img-fluid h-100 rounded"
                                        style="object-fit: cover;" alt="">
                                </div>
                            </div>
                            <div class="col-8 col-lg-8 col-xl-9">
                                <div class="d-flex flex-column my-auto text-start p-4">
                                    <h4 class="text-dark mb-0">Nama Penghuni</h4>
                                    <p class="mb-3">Karyawan</p>
                                    <div class="d-flex text-primary mb-3">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star text-body"></i>
                                    </div>
                                    <p class="mb-0">Saya sangat puas tinggal di sini. Lingkungannya aman dan nyaman.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-item bg-light rounded">
                        <div class="row g-0">
                            <div class="col-4 col-lg-4 col-xl-3">
                                <div class="h-100">
                                    <img src="/guest/img/testimonial-3.jpg" class="img-fluid h-100 rounded"
                                        style="object-fit: cover;" alt="">
                                </div>
                            </div>
                            <div class="col-8 col-lg-8 col-xl-9">
                                <div class="d-flex flex-column my-auto text-start p-4">
                                    <h4 class="text-dark mb-0">Nama Penghuni</h4>
                                    <p class="mb-3">Mahasiswa</p>
                                    <div class="d-flex text-primary mb-3">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star text-body"></i>
                                        <i class="fas fa-star text-body"></i>
                                    </div>
                                    <p class="mb-0">Kost ini sangat direkomendasikan. Fasilitasnya lengkap dan
                                        pelayanannya ramah.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Testimonial End -->

    </div>
    @endvolt
</x-guest-layout>
