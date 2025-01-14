<?php

namespace Database\Seeders;

use App\Models\Image;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $imageUrls = [

            [
                'image' => 'https://images.oyoroomscdn.com/uploads/hotel_image/199499/medium/cbb917481eb9fcc5.jpg'
            ],
            [
                'image' => 'https://images.oyoroomscdn.com/uploads/hotel_image/199499/medium/fbb6e1841f3c77e1.jpg'
            ],
            [
                'image' => 'https://images.oyoroomscdn.com/uploads/hotel_image/199499/medium/ef0cf0c2ba614bc6.jpg'
            ],
            [
                'image' => 'https://images.oyoroomscdn.com/uploads/hotel_image/199499/medium/4feb0abef9423dd5.jpg'
            ],
            [
                'image' => 'https://images.oyoroomscdn.com/uploads/hotel_image/199499/medium/74fa41eef0df8984.jpg'
            ],
            [
                'image' => 'https://images.oyoroomscdn.com/uploads/hotel_image/199499/medium/bb05307240a70e6e.jpg'
            ],
        ];

        foreach ($imageUrls as $imageUrl) {
            $imageContents = file_get_contents($imageUrl['image']);
            $imageName = basename($imageUrl['image']);
            $storagePath = 'images/' . $imageName;

            // Simpan gambar ke folder public storage
            Storage::disk('public')->put($storagePath, $imageContents);

            // Simpan path gambar ke database
            $image = Image::create([
                'image_path' => $storagePath,
            ]);

            $this->command->info(string: 'Tambah Galleri ' . $image->image_path);
        }
    }
}
