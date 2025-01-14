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

            $this->command->info(string: 'Tambah Kamar ' . $room->id);
        }
    }
}
