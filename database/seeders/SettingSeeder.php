<?php

namespace Database\Seeders;

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
                'phone' => '628978301711',
            ]
        );
    }
}
