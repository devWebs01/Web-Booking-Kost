<?php

use App\Models\Setting;
use App\Models\Image;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use function Livewire\Volt\{state, usesFileUploads, rules, uses};

usesFileUploads();
uses([LivewireAlert::class]);

state([
    "setting" => fn() => Setting::first(),
    "facilities" => fn() => $this->setting->facilities->pluck("name")->toArray(),
    "daily_price" => fn() => $this->setting->daily_price,
    "monthly_price" => fn() => $this->setting->monthly_price,
    "images" => [],
    "previmages",
]);

rules([
    "number" => "required|numeric",
    "facilities" => "required",
    "facilities.*" => "required|string|min:2",
    "images.*" => "image",
]);

$updatingImages = function ($value) {
    $this->previmages = $this->images;
};

$updatedImages = function ($value) {
    $this->images = array_merge($this->previmages, $value);
};

$removeItem = function ($key) {
    if (isset($this->images[$key])) {
        $file = $this->images[$key];
        $file->delete();
        unset($this->images[$key]);
    }

    $this->images = array_values($this->images);
};

$edit = function () {
    $setting = $this->setting;
    try {
        \DB::beginTransaction();

        $facilities = is_array($this->facilities) ? $this->facilities : explode(",", $this->facilities);

        $this->validate([
            "daily_price" => "required|numeric|min:0",
            "monthly_price" => "required|numeric|min:0",
        ]);
        $setting->update([
            "daily_price" => $this->daily_price,
            "monthly_price" => $this->monthly_price,
        ]);

        $setting->facilities()->delete();

        foreach ($facilities as $facility) {
            $setting->facilities()->create([
                "name" => $facility,
            ]);
        }

        if (count($this->images) > 0) {
            $images = Image::where("setting_id", $setting->id)->get();

            if ($images->isNotEmpty()) {
                foreach ($images as $image) {
                    Storage::delete($image->image_path);

                    $image->delete();
                }
            }

            foreach ($this->images as $image) {
                $path = $image->store("settings", "public");
                Image::create([
                    "setting_id" => $setting->id,
                    "image_path" => $path,
                ]);

                $image->delete();
            }
        }

        \DB::commit();

        $this->alert("success", "Data berhasil diedit!", [
            "position" => "center",
            "timer" => 3000,
            "toast" => true,
        ]);

        $this->redirectRoute("settings.index");
    } catch (\Exception $e) {
        \DB::rollBack();

        $this->alert("error", "Terjadi kesalahan saat menyimpan data!", [
            "position" => "center",
            "timer" => 3000,
            "toast" => true,
        ]);
    }
};

?>

@volt
    <div>
        @include("components.partials.tom-select")

        <style>
            #dropZone {
                border: 2px dashed #bbb;
                border-radius: 5px;
                padding: 50px;
                text-align: center;
                font-size: 21pt;
                font-weight: bold;
                color: #bbb;
                width: 100%;
                display: block;
            }

            /* Scrollbar kecil tapi tetap terlihat */
            .overflow-auto {
                scrollbar-width: thin;
                /* Untuk Firefox */
                -ms-overflow-style: none;
                /* Untuk Internet Explorer dan Edge */
            }

            .overflow-auto::-webkit-scrollbar {
                width: 5px;
                /* Untuk Chrome, Safari, dan Opera */
                height: 5px;
            }

            .overflow-auto::-webkit-scrollbar-thumb {
                background-color: rgba(0, 0, 0, 0.3);
                /* Warna thumb (pegangan scrollbar) */
                border-radius: 10px;
            }

            .overflow-auto::-webkit-scrollbar-track {
                background: transparent;
                /* Background track dibuat transparan */
            }
        </style>

        @if ($images)
            <div class="mb-5">
                <div class="d-flex flex-nowrap gap-3 overflow-auto" style="white-space: nowrap;">
                    @foreach ($images as $key => $image)
                        <div class="position-relative" style="width: 200px; flex: 0 0 auto;">
                            <div class="card mt-3">
                                <img src="{{ $image->temporaryUrl() }}" class="card-img-top"
                                    style="object-fit: cover; width: 200px; height: 200px;" alt="preview">
                                <a type="button" class="position-absolute top-0 start-100 translate-middle p-2"
                                    wire:click.prevent='removeItem({{ json_encode($key) }})'>
                                    <i class='bx bx-x p-2 rounded-circle ri-20px text-white bg-danger'></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @elseif ($setting->images->isNotEmpty())
            <div class="mb-5">
                <small>Gambar tersimpan
                    <span class="text-danger">(Jika tidak mengubah gambar, tidak perlu melakukan
                        input gambar)</span>
                    .
                </small>
                <div class="d-flex flex-nowrap gap-3 overflow-auto" style="white-space: nowrap;">
                    @foreach ($setting->images as $key => $image)
                        <div class="position-relative" style="width: 200px; flex: 0 0 auto;">
                            <div class="card mt-3">
                                <img src="{{ Storage::url($image->image_path) }}" class="card-img-top"
                                    style="object-fit: cover; width: 200px; height: 200px;" alt="preview">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <form wire:submit="edit">
            <div class="row">
                <div class="col-12">
                    <label class="form-label">
                        Gambar Kamar
                        <span wire:target='images' wire:loading.class.remove="d-none"
                            class="d-none spinner-border spinner-border-sm" role="status">
                        </span>
                    </label>
                    <div class="mb-3">
                        <label id="dropZone" for="images" class="form-label">Gambar Kamar</label>
                        <input type="file" class="d-none form-control @error("images") is-invalid @enderror"
                            wire:model="images" id="images" aria-describedby="imagesId" autocomplete="images"
                            accept="image/*" multiple />
                        @error("images")
                            <small id="imagesId" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="mb-3">
                        <label for="facilities" class="form-label">Fasilitas</label>
                        <div wire:ignore>
                            <input type="text" wire:model="facilities" id="input-tags" aria-describedby="facilitiesId"
                                autocomplete="facilities" value="{{ implode(",", $facilities) }}" />
                        </div>
                        @error("facilities")
                            <small id="facilitiesId" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                        <br>
                        @error("facilities.*")
                            <small id="facilitiesId" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="col-md">
                    <div class="mb-3">
                        <label for="daily_price" class="form-label">Harga Perhari</label>
                        <input type="text" class="form-control @error("daily_price") is-invalid @enderror"
                            wire:model="daily_price" id="daily_price" aria-describedby="daily_priceId"
                            placeholder="Enter daily_price" />
                        @error("daily_price")
                            <small id="daily_priceId" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="col-md">
                    <div class="mb-3">
                        <label for="monthly_price" class="form-label">Harga Perbulan</label>
                        <input type="text" class="form-control @error("monthly_price") is-invalid @enderror"
                            wire:model="monthly_price" id="monthly_price" aria-describedby="monthly_priceId"
                            placeholder="Enter monthly_price" />
                        @error("monthly_price")
                            <small id="monthly_priceId" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-12 ">
                <div class="row mb-3">
                    <div class="col-md">
                        <button type="submit" class="btn btn-primary">
                            Submit
                        </button>
                    </div>
                    <div class="col-md align-self-center text-end">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endvolt
