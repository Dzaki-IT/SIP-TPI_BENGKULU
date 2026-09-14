<?php

namespace Database\Seeders;

use App\Models\Tpi;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class TpiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tpis = [
            [
                'kode_tpi' => 'TPI-001',
                'nama' => 'TPI Pulau Baai',
                'kode_wilayah' => '1771',
                'status' => 'aktif',
                'pengelola' => 'UPTD PPP Pulau Baai',
            ],
            [
                'kode_tpi' => 'TPI-002',
                'nama' => 'TPI Bina Karya',
                'kode_wilayah' => '1704',
                'status' => 'aktif',
                'pengelola' => null,
            ],
            [
                'kode_tpi' => 'TPI-003',
                'nama' => 'TPI Keramat Dagang',
                'kode_wilayah' => '1704',
                'status' => 'aktif',
                'pengelola' => null,
            ],
            [
                'kode_tpi' => 'TPI-004',
                'nama' => 'TPI Lanap Dalam',
                'kode_wilayah' => '1704',
                'status' => 'aktif',
                'pengelola' => null,
            ],
            [
                'kode_tpi' => 'TPI-005',
                'nama' => 'TPI Ketapang Baru',
                'kode_wilayah' => '1705',
                'status' => 'aktif',
                'pengelola' => null,
            ],
        ];

        foreach ($tpis as $tpiData) {
            $wilayah = Wilayah::where('kode_wilayah', $tpiData['kode_wilayah'])->first();

            if ($wilayah) {
                Tpi::updateOrCreate(
                    ['kode_tpi' => $tpiData['kode_tpi']],
                    [
                        'nama_tpi' => $tpiData['nama'],
                        'wilayah_id' => $wilayah->id,
                        'status_operasi' => $tpiData['status'],
                        'instansi_pengelola' => $tpiData['pengelola'],
                    ]
                );
            }
        }
    }
}
