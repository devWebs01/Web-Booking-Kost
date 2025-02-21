<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        for ($i = 1; $i <= 20; $i++) {
            // Menentukan posisi kamar
            $position = ($i <= 10) ? 'up' : 'down';

            $roomData = [
                'number' => str_pad($i, 3, '0', STR_PAD_LEFT), // Format nomor kamar dengan 3 digit
                'room_status' => 'active', // Status kamar default
                'position' => $position, // Posisi kamar
            ];

            $room = Room::create($roomData);

            $this->command->info('Tambah Kamar '.$room->id);
        }
    }
}
