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
        $totalRooms = 20; // Jumlah kamar yang ingin dibuat

        for ($no = 1; $no <= $totalRooms; $no++) {
            $room = Room::create([
                'number' => $no,
                'room_status' => 'available',
            ]);

            $this->command->info('Tambah Kamar ' . $room->id);
        }
    }
}
