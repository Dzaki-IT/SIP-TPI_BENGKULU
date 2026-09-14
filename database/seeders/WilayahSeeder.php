<?php

namespace Database\Seeders;

use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wilayahs = [
            ['kode_wilayah' => '1701', 'nama_kabupaten_kota' => 'Bengkulu Selatan'],
            ['kode_wilayah' => '1703', 'nama_kabupaten_kota' => 'Bengkulu Utara'],
            ['kode_wilayah' => '1704', 'nama_kabupaten_kota' => 'Kaur'],
            ['kode_wilayah' => '1705', 'nama_kabupaten_kota' => 'Seluma'],
            ['kode_wilayah' => '1706', 'nama_kabupaten_kota' => 'Mukomuko'],
            ['kode_wilayah' => '1709', 'nama_kabupaten_kota' => 'Bengkulu Tengah'],
            ['kode_wilayah' => '1771', 'nama_kabupaten_kota' => 'Kota Bengkulu'],
        ];

        foreach ($wilayahs as $wilayah) {
            Wilayah::updateOrCreate(
                ['kode_wilayah' => $wilayah['kode_wilayah']],
                ['nama_kabupaten_kota' => $wilayah['nama_kabupaten_kota']]
            );
        }
    }
}
