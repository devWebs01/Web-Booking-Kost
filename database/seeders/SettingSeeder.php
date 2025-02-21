<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Image;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = Setting::create(
            [
                'name' => 'Gala Residence',
                'description' => 'Kost Gala Residence berlokasi Jambi. penginapan bintang 2 ini memiliki WiFi gratis dan kamar ber-AC dengan kamar mandi pribadi.',
                'location' => 'Payo Lebar, Jambi, Danau Sipin, Jambi, Indonesia, Jambi',
                'phone' => '628978301711',
                'expire_time' => '10',
                'daily_price' => 200000,
                'monthly_price' => 1500000,
            ]
        );

        $imageUrls = [
            'https://images.oyoroomscdn.com/uploads/hotel_image/199499/medium/cbb917481eb9fcc5.jpg',
            'https://images.oyoroomscdn.com/uploads/hotel_image/199499/medium/fbb6e1841f3c77e1.jpg',
            'https://images.oyoroomscdn.com/uploads/hotel_image/199499/medium/ef0cf0c2ba614bc6.jpg',
            'https://images.oyoroomscdn.com/uploads/hotel_image/199499/medium/4feb0abef9423dd5.jpg',
            'https://images.oyoroomscdn.com/uploads/hotel_image/199499/medium/74fa41eef0df8984.jpg',
            'https://images.oyoroomscdn.com/uploads/hotel_image/199499/medium/bb05307240a70e6e.jpg',
        ];

        $facilities = [
            'Wi-Fi Gratis',
            'Kamar Mandi Dalam',
            'Air Panas',
            'AC',
            'Kasur Nyaman',
            'Meja Belajar',
        ];

        // Tambahkan fasilitas ke kamar
        foreach ($facilities as $facility) {
            Facility::create([
                'setting_id' => $setting->id,
                'name' => $facility,
            ]);
        }

        // Tambahkan gambar ke kamar

        foreach ($imageUrls as $imageUrl) {
            $imageContents = file_get_contents($imageUrl);
            $imageName = basename($setting->id.$imageUrl);
            $storagePath = 'rooms/'.$imageName;

            // Simpan gambar ke folder public storage
            Storage::disk('public')->put($storagePath, $imageContents);

            // Simpan path gambar ke database
            Image::create([
                'setting_id' => $setting->id,
                'image_path' => $storagePath,
            ]);
        }
    }
}
