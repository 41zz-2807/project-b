<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'nama_sekolah' => '',
            'nama_kelas' => '',
            'iuran_kas' => '0',
            'iuran_komite' => '0',
            'nama_bank' => '',
            'no_rekening' => '',
            'nama_pemilik' => '',
        ];

        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}