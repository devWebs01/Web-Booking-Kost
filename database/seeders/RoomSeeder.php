<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Facility;
use App\Models\Image;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Storage;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            [
                'daily_price' => 200000,
                'monthly_price' => 1500000,
                'description' => 'Kamar luas dengan fasilitas lengkap.',
                'room_status' => 'available',
            ],
            [
                'daily_price' => 200000,
                'monthly_price' => 1500000,
                'description' => 'Kamar nyaman dekat fasilitas umum.',
                'room_status' => 'available',
            ],
            [
                'daily_price' => 200000,
                'monthly_price' => 1500000,
                'description' => 'Kamar minimalis dengan harga terjangkau.',
                'room_status' => 'available',
            ],
            [
                'daily_price' => 200000,
                'monthly_price' => 1500000,
                'description' => 'Kamar luas dengan fasilitas lengkap.',
                'room_status' => 'available',
            ],
            [
                'daily_price' => 200000,
                'monthly_price' => 1500000,
                'description' => 'Kamar nyaman dekat fasilitas umum.',
                'room_status' => 'available',
            ],
            [
                'daily_price' => 200000,
                'monthly_price' => 1500000,
                'description' => 'Kamar minimalis dengan harga terjangkau.',
                'room_status' => 'available',
            ],
            [
                'daily_price' => 200000,
                'monthly_price' => 1500000,
                'description' => 'Kamar luas dengan fasilitas lengkap.',
                'room_status' => 'available',
            ],
            [
                'daily_price' => 200000,
                'monthly_price' => 1500000,
                'description' => 'Kamar nyaman dekat fasilitas umum.',
                'room_status' => 'available',
            ],
            [
                'daily_price' => 200000,
                'monthly_price' => 1500000,
                'description' => 'Kamar minimalis dengan harga terjangkau.',
                'room_status' => 'available',
            ],
            // Tambahkan 7 data lainnya dengan variasi deskripsi
        ];

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

        foreach ($rooms as $roomData) {
            $room = Room::create($roomData);

            // Tambahkan fasilitas ke kamar
            foreach ($facilities as $facility) {
                Facility::create([
                    'room_id' => $room->id,
                    'name' => $facility,
                ]);
            }

            // Tambahkan gambar ke kamar

            foreach ($imageUrls as $imageUrl) {
                $imageContents = file_get_contents($imageUrl);
                $imageName = basename($room->id.$imageUrl);
                $storagePath = 'rooms/' . $imageName;

                // Simpan gambar ke folder public storage
                Storage::disk('public')->put($storagePath, $imageContents);

                // Simpan path gambar ke database
                Image::create([
                    'room_id' => $room->id,
                    'image_path' => $storagePath,
                ]);
            }

            $this->command->info(string: 'Tambah Kamar ' . $room->id);
        }
    }
}
