<?php

use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use function Livewire\Volt\{state};

state([]);

?>
<x-admin-layout>
    <x-slot name="title">Beranda</x-slot>
    @include('layouts.datatables')

    @volt
        <div>
            <div class="container-fluid">
                <div class="card text-bg-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-7">
                                <div class="d-flex flex-column h-100">
                                    <div class="hstack gap-3 mb-2">
                                        <span
                                            class="d-flex align-items-center justify-content-center round-48 bg-primary rounded flex-shrink-0">
                                            <iconify-icon icon="solar:course-up-outline"
                                                class="fs-7 text-white"></iconify-icon>
                                        </span>
                                        <h5 class="text-primary fs-6 mb-0 text-wrap text-capitalize fw-bold">Welcome Back
                                            {{ auth()->user()->name }}
                                        </h5>
                                    </div>
                                    <div class="mt-4 mt-sm-auto">
                                        <div class="row">
                                            <div class="col-auto">
                                                <span class="opacity-75 text-dark">Pendapatan</span>
                                                <h4 class="mb-0 text-primary mt-1 text-nowrap">

                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-5 text-center text-md-end">
                                <img src="https://img.freepik.com/free-vector/digital-designers-team-drawing-with-pen-computer-monitor_74855-10586.jpg?w=740&t=st=1717604543~exp=1717605143~hmac=93b861c201e629763f37656be25404002beab0b469e78a487c7f2f97918e1d74"
                                    alt="welcome" class="img-fluid mb-n7 mt-2" width="180">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endvolt
</x-admin-layout>
