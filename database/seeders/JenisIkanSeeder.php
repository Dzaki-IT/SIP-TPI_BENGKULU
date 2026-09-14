<?php

namespace Database\Seeders;

use App\Models\JenisIkan;
use Illuminate\Database\Seeder;

class JenisIkanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ikans = [
            ['kode_ikan' => 'IKN-001', 'nama_resmi' => 'Tuna', 'nama_lokal' => 'Thunnus sp.', 'kategori' => 'Pelagis Besar', 'satuan' => 'Kg'],
            ['kode_ikan' => 'IKN-002', 'nama_resmi' => 'Tongkol', 'nama_lokal' => 'Euthynnus affinis', 'kategori' => 'Pelagis Kecil', 'satuan' => 'Kg'],
            ['kode_ikan' => 'IKN-003', 'nama_resmi' => 'Cakalang', 'nama_lokal' => 'Katsuwonus pelamis', 'kategori' => 'Pelagis Besar', 'satuan' => 'Kg'],
            ['kode_ikan' => 'IKN-004', 'nama_resmi' => 'Tenggiri', 'nama_lokal' => 'Scomberomorus commerson', 'kategori' => 'Pelagis Besar', 'satuan' => 'Kg'],
            ['kode_ikan' => 'IKN-005', 'nama_resmi' => 'Kembung', 'nama_lokal' => 'Rastrelliger sp.', 'kategori' => 'Pelagis Kecil', 'satuan' => 'Kg'],
            ['kode_ikan' => 'IKN-006', 'nama_resmi' => 'Teri', 'nama_lokal' => 'Stolephorus sp.', 'kategori' => 'Pelagis Kecil', 'satuan' => 'Kg'],
            ['kode_ikan' => 'IKN-007', 'nama_resmi' => 'Lemuru/Sarden', 'nama_lokal' => 'Sardinella sp.', 'kategori' => 'Pelagis Kecil', 'satuan' => 'Kg'],
            ['kode_ikan' => 'IKN-008', 'nama_resmi' => 'Layang', 'nama_lokal' => 'Decapterus sp.', 'kategori' => 'Pelagis Kecil', 'satuan' => 'Kg'],
            ['kode_ikan' => 'IKN-009', 'nama_resmi' => 'Selar', 'nama_lokal' => 'Selaroides sp.', 'kategori' => 'Pelagis Kecil', 'satuan' => 'Kg'],
            ['kode_ikan' => 'IKN-010', 'nama_resmi' => 'Kakap Merah', 'nama_lokal' => 'Lutjanus sp.', 'kategori' => 'Demersal', 'satuan' => 'Kg'],
            ['kode_ikan' => 'IKN-011', 'nama_resmi' => 'Kerapu', 'nama_lokal' => 'Epinephelus sp.', 'kategori' => 'Demersal', 'satuan' => 'Kg'],
            ['kode_ikan' => 'IKN-012', 'nama_resmi' => 'Bawal', 'nama_lokal' => 'Pampus sp.', 'kategori' => 'Demersal', 'satuan' => 'Kg'],
            ['kode_ikan' => 'IKN-013', 'nama_resmi' => 'Manyung', 'nama_lokal' => 'Arius sp.', 'kategori' => 'Demersal', 'satuan' => 'Kg'],
            ['kode_ikan' => 'IKN-014', 'nama_resmi' => 'Pari', 'nama_lokal' => 'Dasyatis sp.', 'kategori' => 'Demersal', 'satuan' => 'Kg'],
            ['kode_ikan' => 'IKN-015', 'nama_resmi' => 'Cumi-cumi', 'nama_lokal' => 'Loligo sp.', 'kategori' => 'Non-Ikan', 'satuan' => 'Kg'],
            ['kode_ikan' => 'IKN-016', 'nama_resmi' => 'Udang', 'nama_lokal' => 'Penaeus sp.', 'kategori' => 'Non-Ikan', 'satuan' => 'Kg'],
            ['kode_ikan' => 'IKN-017', 'nama_resmi' => 'Rajungan', 'nama_lokal' => 'Portunus pelagicus', 'kategori' => 'Non-Ikan', 'satuan' => 'Kg'],
            ['kode_ikan' => 'IKN-018', 'nama_resmi' => 'Layur', 'nama_lokal' => 'Trichiurus sp.', 'kategori' => 'Demersal', 'satuan' => 'Kg'],
            ['kode_ikan' => 'IKN-019', 'nama_resmi' => 'Ekor Kuning', 'nama_lokal' => 'Caesio sp.', 'kategori' => 'Demersal', 'satuan' => 'Kg'],
            ['kode_ikan' => 'IKN-020', 'nama_resmi' => 'Ikan Lainnya', 'nama_lokal' => null, 'kategori' => 'Lainnya', 'satuan' => 'Kg'],
        ];

        foreach ($ikans as $ikan) {
            JenisIkan::updateOrCreate(
                ['kode_ikan' => $ikan['kode_ikan']],
                [
                    'nama_resmi' => $ikan['nama_resmi'],
                    'nama_lokal' => $ikan['nama_lokal'],
                    'kategori' => $ikan['kategori'],
                    'satuan' => $ikan['satuan'],
                ]
            );
        }
    }
}
