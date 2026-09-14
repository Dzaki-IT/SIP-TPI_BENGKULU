<?php

namespace Database\Seeders;

use App\Models\AlatTangkap;
use App\Models\DetailPendaratan;
use App\Models\JenisIkan;
use App\Models\Kapal;
use App\Models\Nelayan;
use App\Models\Pendaratan;
use App\Models\Tpi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SamplePendaratanSeeder extends Seeder
{
    /**
     * Run the database seeds for rich analytical dashboard and reporting data.
     */
    public function run(): void
    {
        // 1. Ensure or find users
        $adminUser = User::where('email', 'admin@tpi-bengkulu.test')->first();
        $verifikatorUser = User::where('email', 'verifikator@tpi-bengkulu.test')->first();

        $adminId = $adminUser ? $adminUser->id : 1;
        $verifikatorId = $verifikatorUser ? $verifikatorUser->id : 2;

        // 2. Find TPIs
        $tpiBaai = Tpi::where('kode_tpi', 'TPI-001')->first();
        $tpiBina = Tpi::where('kode_tpi', 'TPI-002')->first();
        $tpiKeramat = Tpi::where('kode_tpi', 'TPI-003')->first();
        $tpiLanap = Tpi::where('kode_tpi', 'TPI-004')->first();
        $tpiKetapang = Tpi::where('kode_tpi', 'TPI-005')->first();

        if (! $tpiBaai || ! $tpiBina) {
            return;
        }

        // 3. Ensure Realistic Nelayan and Kapal
        $nelayanList = [
            [
                'kode' => 'NEL-001', 'nama' => 'Pak Buyung', 'nik' => '1771012304750001',
                'alamat' => 'Kampung Melayu, Kota Bengkulu', 'kelompok' => 'KUB Bahari Mandiri',
                'tpi_id' => $tpiBaai->id, 'kapal_nama' => 'KM Bahari Samudera', 'gt' => 28.5, 'jenis' => 'kapal_motor',
            ],
            [
                'kode' => 'NEL-002', 'nama' => 'Pak Syamsul Bahri', 'nik' => '1771021405820002',
                'alamat' => 'Teluk Segara, Kota Bengkulu', 'kelompok' => 'KUB Samudera Sejahtera',
                'tpi_id' => $tpiBaai->id, 'kapal_nama' => 'KM Laut Biru', 'gt' => 18.0, 'jenis' => 'kapal_motor',
            ],
            [
                'kode' => 'NEL-003', 'nama' => 'Pak Joni Hermanto', 'nik' => '1704031508780003',
                'alamat' => 'Bintuhan, Kaur', 'kelompok' => 'KUB Sinar Pesisir',
                'tpi_id' => $tpiBina->id, 'kapal_nama' => 'KM Sinar Kaur', 'gt' => 12.0, 'jenis' => 'kapal_motor',
            ],
            [
                'kode' => 'NEL-004', 'nama' => 'Pak Rustam Efendi', 'nik' => '1704041903800004',
                'alamat' => 'Nasal, Kaur', 'kelompok' => 'KUB Nelayan Keramat',
                'tpi_id' => $tpiKeramat?->id ?? $tpiBina->id, 'kapal_nama' => 'KM Berkah Nelayan', 'gt' => 9.5, 'jenis' => 'kapal_motor',
            ],
            [
                'kode' => 'NEL-005', 'nama' => 'Pak Herman Basri', 'nik' => '1704052511840005',
                'alamat' => 'Maje, Kaur', 'kelompok' => 'KUB Lanap Berjaya',
                'tpi_id' => $tpiLanap?->id ?? $tpiBina->id, 'kapal_nama' => 'KM Samudera Jaya', 'gt' => 8.0, 'jenis' => 'motor_tempel',
            ],
            [
                'kode' => 'NEL-006', 'nama' => 'Pak Sudirman', 'nik' => '1705061206860006',
                'alamat' => 'Ketapang Baru, Seluma', 'kelompok' => 'KUB Harapan Laut',
                'tpi_id' => $tpiKetapang?->id ?? $tpiBaai->id, 'kapal_nama' => 'KM Bintang Seluma', 'gt' => 7.5, 'jenis' => 'motor_tempel',
            ],
        ];

        $seededNelayans = [];
        $seededKapals = [];

        foreach ($nelayanList as $idx => $nData) {
            $nelayan = Nelayan::updateOrCreate(
                ['kode_nelayan' => $nData['kode']],
                [
                    'nama' => $nData['nama'],
                    'nik' => $nData['nik'],
                    'alamat' => $nData['alamat'],
                    'kelompok_nelayan' => $nData['kelompok'],
                    'tpi_id' => $nData['tpi_id'],
                    'is_active' => true,
                ]
            );
            $seededNelayans[$idx] = $nelayan;

            $kapal = Kapal::updateOrCreate(
                ['kode_kapal' => 'KPL-00' . ($idx + 1)],
                [
                    'nama_kapal' => $nData['kapal_nama'],
                    'nelayan_id' => $nelayan->id,
                    'jenis_kapal' => $nData['jenis'],
                    'gt' => $nData['gt'],
                    'no_sipi' => 'SIPI-BKL-2026-00' . ($idx + 1),
                    'is_active' => true,
                ]
            );
            $seededKapals[$idx] = $kapal;
        }

        // 4. Reference Species and Gear
        $ikanTuna = JenisIkan::where('kode_ikan', 'IKN-001')->first();
        $ikanTongkol = JenisIkan::where('kode_ikan', 'IKN-002')->first();
        $ikanCakalang = JenisIkan::where('kode_ikan', 'IKN-003')->first();
        $ikanTenggiri = JenisIkan::where('kode_ikan', 'IKN-004')->first();
        $ikanKembung = JenisIkan::where('kode_ikan', 'IKN-005')->first();
        $ikanKakap = JenisIkan::where('kode_ikan', 'IKN-010')->first();
        $ikanKerapu = JenisIkan::where('kode_ikan', 'IKN-011')->first();
        $ikanBawal = JenisIkan::where('kode_ikan', 'IKN-012')->first();
        $ikanCumi = JenisIkan::where('kode_ikan', 'IKN-015')->first();
        $ikanUdang = JenisIkan::where('kode_ikan', 'IKN-016')->first();
        $ikanRajungan = JenisIkan::where('kode_ikan', 'IKN-017')->first();
        $ikanTeri = JenisIkan::where('kode_ikan', 'IKN-006')->first();

        $alatPurse = AlatTangkap::where('kode_alat', 'AT-001')->first() ?? AlatTangkap::first();
        $alatGill = AlatTangkap::where('kode_alat', 'AT-003')->first() ?? $alatPurse;
        $alatHand = AlatTangkap::where('kode_alat', 'AT-007')->first() ?? $alatPurse;
        $alatTroll = AlatTangkap::where('kode_alat', 'AT-008')->first() ?? $alatPurse;
        $alatLong = AlatTangkap::where('kode_alat', 'AT-010')->first() ?? $alatPurse;

        // 5. Landings Blueprint (18 Verified Landings throughout 2026)
        $verifiedLandings = [
            // Bulan 1: Januari
            [
                'kode' => 'PDR-20260114-0001', 'tpi' => $tpiBaai, 'nelayan' => $seededNelayans[0], 'kapal' => $seededKapals[0],
                'alat' => $alatLong, 'tanggal' => '2026-01-14',
                'details' => [
                    ['ikan' => $ikanTuna, 'vol' => 1250, 'hrg' => 45000],
                    ['ikan' => $ikanCakalang, 'vol' => 650, 'hrg' => 35000],
                ],
            ],
            [
                'kode' => 'PDR-20260128-0002', 'tpi' => $tpiBina, 'nelayan' => $seededNelayans[2], 'kapal' => $seededKapals[2],
                'alat' => $alatGill, 'tanggal' => '2026-01-28',
                'details' => [
                    ['ikan' => $ikanTongkol, 'vol' => 850, 'hrg' => 25000],
                    ['ikan' => $ikanTenggiri, 'vol' => 380, 'hrg' => 65000],
                ],
            ],
            // Bulan 2: Februari
            [
                'kode' => 'PDR-20260210-0003', 'tpi' => $tpiBaai, 'nelayan' => $seededNelayans[1], 'kapal' => $seededKapals[1],
                'alat' => $alatPurse, 'tanggal' => '2026-02-10',
                'details' => [
                    ['ikan' => $ikanTuna, 'vol' => 1600, 'hrg' => 48000],
                    ['ikan' => $ikanTongkol, 'vol' => 520, 'hrg' => 24000],
                ],
            ],
            [
                'kode' => 'PDR-20260222-0004', 'tpi' => $tpiKeramat ?? $tpiBina, 'nelayan' => $seededNelayans[3], 'kapal' => $seededKapals[3],
                'alat' => $alatHand, 'tanggal' => '2026-02-22',
                'details' => [
                    ['ikan' => $ikanKakap, 'vol' => 420, 'hrg' => 75000],
                    ['ikan' => $ikanKerapu, 'vol' => 260, 'hrg' => 80000],
                ],
            ],
            // Bulan 3: Maret
            [
                'kode' => 'PDR-20260308-0005', 'tpi' => $tpiLanap ?? $tpiBina, 'nelayan' => $seededNelayans[4], 'kapal' => $seededKapals[4],
                'alat' => $alatGill, 'tanggal' => '2026-03-08',
                'details' => [
                    ['ikan' => $ikanUdang, 'vol' => 280, 'hrg' => 85000],
                    ['ikan' => $ikanCumi, 'vol' => 340, 'hrg' => 55000],
                ],
            ],
            [
                'kode' => 'PDR-20260325-0006', 'tpi' => $tpiKetapang ?? $tpiBaai, 'nelayan' => $seededNelayans[5], 'kapal' => $seededKapals[5],
                'alat' => $alatPurse, 'tanggal' => '2026-03-25',
                'details' => [
                    ['ikan' => $ikanKembung, 'vol' => 750, 'hrg' => 22000],
                    ['ikan' => $ikanTongkol, 'vol' => 620, 'hrg' => 25000],
                ],
            ],
            // Bulan 4: April
            [
                'kode' => 'PDR-20260412-0007', 'tpi' => $tpiBaai, 'nelayan' => $seededNelayans[0], 'kapal' => $seededKapals[0],
                'alat' => $alatTroll, 'tanggal' => '2026-04-12',
                'details' => [
                    ['ikan' => $ikanCakalang, 'vol' => 1100, 'hrg' => 35000],
                    ['ikan' => $ikanTenggiri, 'vol' => 550, 'hrg' => 65000],
                ],
            ],
            [
                'kode' => 'PDR-20260429-0008', 'tpi' => $tpiBina, 'nelayan' => $seededNelayans[2], 'kapal' => $seededKapals[2],
                'alat' => $alatPurse, 'tanggal' => '2026-04-29',
                'details' => [
                    ['ikan' => $ikanTongkol, 'vol' => 950, 'hrg' => 25000],
                    ['ikan' => $ikanTeri, 'vol' => 400, 'hrg' => 18000],
                ],
            ],
            // Bulan 5: Mei
            [
                'kode' => 'PDR-20260515-0009', 'tpi' => $tpiBaai, 'nelayan' => $seededNelayans[1], 'kapal' => $seededKapals[1],
                'alat' => $alatLong, 'tanggal' => '2026-05-15',
                'details' => [
                    ['ikan' => $ikanTuna, 'vol' => 1950, 'hrg' => 50000],
                    ['ikan' => $ikanCakalang, 'vol' => 820, 'hrg' => 36000],
                ],
            ],
            [
                'kode' => 'PDR-20260527-0010', 'tpi' => $tpiKeramat ?? $tpiBina, 'nelayan' => $seededNelayans[3], 'kapal' => $seededKapals[3],
                'alat' => $alatHand, 'tanggal' => '2026-05-27',
                'details' => [
                    ['ikan' => $ikanBawal, 'vol' => 320, 'hrg' => 65000],
                    ['ikan' => $ikanKakap, 'vol' => 460, 'hrg' => 74000],
                ],
            ],
            // Bulan 6: Juni
            [
                'kode' => 'PDR-20260611-0011', 'tpi' => $tpiLanap ?? $tpiBina, 'nelayan' => $seededNelayans[4], 'kapal' => $seededKapals[4],
                'alat' => $alatGill, 'tanggal' => '2026-06-11',
                'details' => [
                    ['ikan' => $ikanUdang, 'vol' => 360, 'hrg' => 90000],
                    ['ikan' => $ikanRajungan, 'vol' => 220, 'hrg' => 70000],
                ],
            ],
            [
                'kode' => 'PDR-20260628-0012', 'tpi' => $tpiKetapang ?? $tpiBaai, 'nelayan' => $seededNelayans[5], 'kapal' => $seededKapals[5],
                'alat' => $alatPurse, 'tanggal' => '2026-06-28',
                'details' => [
                    ['ikan' => $ikanKembung, 'vol' => 680, 'hrg' => 23000],
                    ['ikan' => $ikanTongkol, 'vol' => 540, 'hrg' => 25000],
                ],
            ],
            // Bulan 7: Juli
            [
                'kode' => 'PDR-20260714-0013', 'tpi' => $tpiBaai, 'nelayan' => $seededNelayans[0], 'kapal' => $seededKapals[0],
                'alat' => $alatTroll, 'tanggal' => '2026-07-14',
                'details' => [
                    ['ikan' => $ikanTuna, 'vol' => 1750, 'hrg' => 48000],
                    ['ikan' => $ikanTenggiri, 'vol' => 620, 'hrg' => 68000],
                ],
            ],
            [
                'kode' => 'PDR-20260730-0014', 'tpi' => $tpiBina, 'nelayan' => $seededNelayans[2], 'kapal' => $seededKapals[2],
                'alat' => $alatPurse, 'tanggal' => '2026-07-30',
                'details' => [
                    ['ikan' => $ikanTongkol, 'vol' => 1100, 'hrg' => 26000],
                    ['ikan' => $ikanCakalang, 'vol' => 530, 'hrg' => 36000],
                ],
            ],
            // Bulan 8: Agustus
            [
                'kode' => 'PDR-20260810-0015', 'tpi' => $tpiBaai, 'nelayan' => $seededNelayans[1], 'kapal' => $seededKapals[1],
                'alat' => $alatLong, 'tanggal' => '2026-08-10',
                'details' => [
                    ['ikan' => $ikanTuna, 'vol' => 1450, 'hrg' => 46000],
                    ['ikan' => $ikanKakap, 'vol' => 380, 'hrg' => 75000],
                ],
            ],
            [
                'kode' => 'PDR-20260822-0016', 'tpi' => $tpiKeramat ?? $tpiBina, 'nelayan' => $seededNelayans[3], 'kapal' => $seededKapals[3],
                'alat' => $alatHand, 'tanggal' => '2026-08-22',
                'details' => [
                    ['ikan' => $ikanKerapu, 'vol' => 290, 'hrg' => 82000],
                    ['ikan' => $ikanUdang, 'vol' => 240, 'hrg' => 88000],
                ],
            ],
            // Bulan 9: September
            [
                'kode' => 'PDR-20260902-0017', 'tpi' => $tpiLanap ?? $tpiBina, 'nelayan' => $seededNelayans[4], 'kapal' => $seededKapals[4],
                'alat' => $alatGill, 'tanggal' => '2026-09-02',
                'details' => [
                    ['ikan' => $ikanTongkol, 'vol' => 780, 'hrg' => 25000],
                    ['ikan' => $ikanCumi, 'vol' => 310, 'hrg' => 56000],
                ],
            ],
            [
                'kode' => 'PDR-20260905-0018', 'tpi' => $tpiKetapang ?? $tpiBaai, 'nelayan' => $seededNelayans[5], 'kapal' => $seededKapals[5],
                'alat' => $alatPurse, 'tanggal' => '2026-09-05',
                'details' => [
                    ['ikan' => $ikanKembung, 'vol' => 590, 'hrg' => 22000],
                    ['ikan' => $ikanTeri, 'vol' => 370, 'hrg' => 18000],
                ],
            ],
        ];

        // Seed 18 Verified Landings
        foreach ($verifiedLandings as $landingData) {
            $tgl = Carbon::parse($landingData['tanggal']);

            $pendaratan = Pendaratan::updateOrCreate(
                ['kode_pendaratan' => $landingData['kode']],
                [
                    'tpi_id' => $landingData['tpi']->id,
                    'kapal_id' => $landingData['kapal']->id,
                    'nelayan_id' => $landingData['nelayan']->id,
                    'alat_tangkap_id' => $landingData['alat']->id,
                    'tanggal_pendaratan' => $tgl->toDateString(),
                    'status_verifikasi' => 'diverifikasi',
                    'verified_by' => $verifikatorId,
                    'verified_at' => $tgl->copy()->addHours(3),
                    'created_by' => $adminId,
                ]
            );

            // Re-create details
            DetailPendaratan::where('pendaratan_id', $pendaratan->id)->delete();
            foreach ($landingData['details'] as $det) {
                if ($det['ikan']) {
                    DetailPendaratan::create([
                        'pendaratan_id' => $pendaratan->id,
                        'jenis_ikan_id' => $det['ikan']->id,
                        'volume_kg' => $det['vol'],
                        'harga_per_kg' => $det['hrg'],
                        'nilai_produksi' => $det['vol'] * $det['hrg'],
                    ]);
                }
            }
        }

        // 6. Seed 3 Landings with status 'diajukan' (Waiting verification)
        $pendingLandings = [
            [
                'kode' => 'PDR-20260907-0019', 'tpi' => $tpiBaai, 'nelayan' => $seededNelayans[0], 'kapal' => $seededKapals[0],
                'alat' => $alatLong, 'tanggal' => '2026-09-07',
                'details' => [
                    ['ikan' => $ikanTuna, 'vol' => 1150, 'hrg' => 45000],
                    ['ikan' => $ikanCakalang, 'vol' => 420, 'hrg' => 35000],
                ],
            ],
            [
                'kode' => 'PDR-20260908-0020', 'tpi' => $tpiBina, 'nelayan' => $seededNelayans[2], 'kapal' => $seededKapals[2],
                'alat' => $alatGill, 'tanggal' => '2026-09-08',
                'details' => [
                    ['ikan' => $ikanTongkol, 'vol' => 640, 'hrg' => 25000],
                    ['ikan' => $ikanTenggiri, 'vol' => 260, 'hrg' => 65000],
                ],
            ],
            [
                'kode' => 'PDR-20260908-0021', 'tpi' => $tpiKetapang ?? $tpiBaai, 'nelayan' => $seededNelayans[5], 'kapal' => $seededKapals[5],
                'alat' => $alatPurse, 'tanggal' => '2026-09-08',
                'details' => [
                    ['ikan' => $ikanUdang, 'vol' => 190, 'hrg' => 85000],
                ],
            ],
        ];

        foreach ($pendingLandings as $pData) {
            $pendaratan = Pendaratan::updateOrCreate(
                ['kode_pendaratan' => $pData['kode']],
                [
                    'tpi_id' => $pData['tpi']->id,
                    'kapal_id' => $pData['kapal']->id,
                    'nelayan_id' => $pData['nelayan']->id,
                    'alat_tangkap_id' => $pData['alat']->id,
                    'tanggal_pendaratan' => $pData['tanggal'],
                    'status_verifikasi' => 'diajukan',
                    'verified_by' => null,
                    'verified_at' => null,
                    'created_by' => $adminId,
                ]
            );

            DetailPendaratan::where('pendaratan_id', $pendaratan->id)->delete();
            foreach ($pData['details'] as $det) {
                if ($det['ikan']) {
                    DetailPendaratan::create([
                        'pendaratan_id' => $pendaratan->id,
                        'jenis_ikan_id' => $det['ikan']->id,
                        'volume_kg' => $det['vol'],
                        'harga_per_kg' => $det['hrg'],
                        'nilai_produksi' => $det['vol'] * $det['hrg'],
                    ]);
                }
            }
        }

        // 7. Seed 1 Landing with status 'ditolak'
        $rejectedLanding = Pendaratan::updateOrCreate(
            ['kode_pendaratan' => 'PDR-20260818-0022'],
            [
                'tpi_id' => $tpiBina->id,
                'kapal_id' => $seededKapals[2]->id,
                'nelayan_id' => $seededNelayans[2]->id,
                'alat_tangkap_id' => $alatPurse->id,
                'tanggal_pendaratan' => '2026-08-18',
                'status_verifikasi' => 'ditolak',
                'catatan_verifikasi' => 'Volume fisik ikan di bak penampungan tidak sesuai dengan manifes pelayaran kapal',
                'verified_by' => $verifikatorId,
                'verified_at' => Carbon::parse('2026-08-19 10:15:00'),
                'created_by' => $adminId,
            ]
        );

        DetailPendaratan::where('pendaratan_id', $rejectedLanding->id)->delete();
        if ($ikanTongkol) {
            DetailPendaratan::create([
                'pendaratan_id' => $rejectedLanding->id,
                'jenis_ikan_id' => $ikanTongkol->id,
                'volume_kg' => 450,
                'harga_per_kg' => 25000,
                'nilai_produksi' => 450 * 25000,
            ]);
        }
    }
}
