<?php

namespace Database\Seeders;

use App\Models\AlatTangkap;
use Illuminate\Database\Seeder;

class AlatTangkapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $alats = [
            ['kode_alat' => 'AT-001', 'nama_alat' => 'Pukat Cincin (Purse Seine)', 'jenis_kategori' => 'Pukat'],
            ['kode_alat' => 'AT-002', 'nama_alat' => 'Pukat Pantai (Beach Seine)', 'jenis_kategori' => 'Pukat'],
            ['kode_alat' => 'AT-003', 'nama_alat' => 'Jaring Insang Hanyut (Drift Gill Net)', 'jenis_kategori' => 'Jaring'],
            ['kode_alat' => 'AT-004', 'nama_alat' => 'Jaring Insang Tetap (Set Gill Net)', 'jenis_kategori' => 'Jaring'],
            ['kode_alat' => 'AT-005', 'nama_alat' => 'Jaring Insang Lingkar (Encircling Gill Net)', 'jenis_kategori' => 'Jaring'],
            ['kode_alat' => 'AT-006', 'nama_alat' => 'Trammel Net', 'jenis_kategori' => 'Jaring'],
            ['kode_alat' => 'AT-007', 'nama_alat' => 'Pancing Ulur (Hand Line)', 'jenis_kategori' => 'Pancing'],
            ['kode_alat' => 'AT-008', 'nama_alat' => 'Pancing Tonda (Troll Line)', 'jenis_kategori' => 'Pancing'],
            ['kode_alat' => 'AT-009', 'nama_alat' => 'Rawai Dasar (Bottom Long Line)', 'jenis_kategori' => 'Pancing'],
            ['kode_alat' => 'AT-010', 'nama_alat' => 'Rawai Tuna (Tuna Long Line)', 'jenis_kategori' => 'Pancing'],
            ['kode_alat' => 'AT-011', 'nama_alat' => 'Bagan (Lift Net)', 'jenis_kategori' => 'Jaring Angkat'],
            ['kode_alat' => 'AT-012', 'nama_alat' => 'Bubu (Trap)', 'jenis_kategori' => 'Perangkap'],
            ['kode_alat' => 'AT-013', 'nama_alat' => 'Alat Tangkap Lainnya', 'jenis_kategori' => 'Lainnya'],
        ];

        foreach ($alats as $alat) {
            AlatTangkap::updateOrCreate(
                ['kode_alat' => $alat['kode_alat']],
                [
                    'nama_alat' => $alat['nama_alat'],
                    'jenis_kategori' => $alat['jenis_kategori'],
                ]
            );
        }
    }
}
