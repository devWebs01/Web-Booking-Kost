<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Setting;
use Illuminate\Database\Seeder;

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
                'phone' => '628978301766',
                'daily_price' => 200000,
                'monthly_price' => 1500000,
            ]
        );

        $facilities = [
            'Wi-Fi Gratis',
            'Kamar Mandi Dalam',
            'Air Panas',
            'AC',
            'Kasur Nyaman',
            'Meja Belajar',
        ];

        foreach ($facilities as $facility) {
            Facility::create([
                'setting_id' => $setting->id,
                'name' => $facility,
            ]);
        }
    }
}
