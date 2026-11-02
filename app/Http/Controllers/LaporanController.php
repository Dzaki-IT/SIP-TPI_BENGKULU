<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportFilterRequest;
use App\Models\DetailPendaratan;
use App\Models\JenisIkan;
use App\Models\Pendaratan;
use App\Models\Tpi;
use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class LaporanController extends Controller
{
    /**
     * Display the reporting and aggregation hub.
     */
    public function index(ReportFilterRequest $request): View
    {
        $filters = $this->resolveFilters($request);
        $activeTab = $request->input('tab', 'tpi');

        // Master data options for filter selects
        $availableYears = $this->getAvailableYears();
        $tpis = $this->getAllowedTpis();
        $kategoriList = ['Pelagis Besar', 'Pelagis Kecil', 'Demersal', 'Non-Ikan', 'Lainnya'];
        $reportGeneratedAt = Carbon::now();

        // Calculate data for all tabs to ensure responsive tab switching
        $rekapTpiData = $this->calculateRekapTpi($filters);
        $rekapIkanData = $this->calculateRekapIkan($filters);
        $rekapDkpData = $this->calculateRekapDkp($filters);
        $rekapTriwulanData = $this->calculateRekapTriwulan($filters);

        return view('laporan.index', compact(
            'filters',
            'activeTab',
            'availableYears',
            'tpis',
            'kategoriList',
            'rekapTpiData',
            'rekapIkanData',
            'rekapDkpData',
            'rekapTriwulanData',
            'reportGeneratedAt'
        ));
    }

    /**
     * Dedicated rekap per TPI view or data endpoint.
     */
    public function rekapTpi(ReportFilterRequest $request): View
    {
        $filters = $this->resolveFilters($request);
        $data = $this->calculateRekapTpi($filters);
        $availableYears = $this->getAvailableYears();
        $tpis = $this->getAllowedTpis();

        return view('laporan.rekap_tpi', compact('filters', 'data', 'availableYears', 'tpis'));
    }

    /**
     * Dedicated rekap per Jenis Ikan view or data endpoint.
     */
    public function rekapIkan(ReportFilterRequest $request): View
    {
        $filters = $this->resolveFilters($request);
        $data = $this->calculateRekapIkan($filters);
        $availableYears = $this->getAvailableYears();
        $tpis = $this->getAllowedTpis();
        $kategoriList = ['Pelagis Besar', 'Pelagis Kecil', 'Demersal', 'Non-Ikan', 'Lainnya'];

        return view('laporan.rekap_ikan', compact('filters', 'data', 'availableYears', 'tpis', 'kategoriList'));
    }

    /**
     * Dedicated rekap standar DKP Bengkulu per Kabupaten/Kota.
     */
    public function rekapDkp(ReportFilterRequest $request): View
    {
        $filters = $this->resolveFilters($request);
        $data = $this->calculateRekapDkp($filters);
        $availableYears = $this->getAvailableYears();

        return view('laporan.rekap_dkp', compact('filters', 'data', 'availableYears'));
    }

    /**
     * Export a formatted Excel-compatible spreadsheet.
     */
    public function exportExcel(ReportFilterRequest $request): Response
    {
        $filters = $this->resolveFilters($request);
        $tipe = $request->input('tipe', 'tpi');
        $tahun = $filters['tahun'];

        $filename = sprintf('Laporan_%s_%s_%s.xls', strtoupper($tipe), $tahun, date('Ymd_His'));

        switch ($tipe) {
            case 'ikan':
                $data = $this->calculateRekapIkan($filters);
                $title = 'LAPORAN REKAPITULASI PRODUKSI PERIKANAN TANGKAP PER KOMODITAS IKAN';
                $columns = ['No', 'Kode Ikan', 'Nama Resmi Ikan', 'Nama Lokal / Latin', 'Kategori', 'Volume Tangkapan (Kg)', 'Kontribusi (%)', 'Rata-rata Harga / Kg (Rp)', 'Total Nilai Produksi (Rp)'];
                $rows = [];
                foreach ($data['rows'] as $idx => $r) {
                    $rows[] = [
                        $idx + 1,
                        $r['kode_ikan'],
                        $r['nama_resmi'],
                        $r['nama_lokal'],
                        $r['kategori'],
                        number_format($r['volume_kg'], 2, '.', ''),
                        number_format($r['persentase'], 2, '.', '').'%',
                        number_format($r['rata_rata_harga'], 0, '.', ''),
                        number_format($r['total_nilai'], 0, '.', ''),
                    ];
                }
                $totalsRow = [
                    'TOTAL',
                    '',
                    $data['totals']['count'].' Komoditas',
                    '',
                    '',
                    number_format($data['totals']['volume_kg'], 2, '.', ''),
                    '100.00%',
                    number_format($data['totals']['rata_rata_harga'], 0, '.', ''),
                    number_format($data['totals']['total_nilai'], 0, '.', ''),
                ];
                break;

            case 'dkp':
                $data = $this->calculateRekapDkp($filters);
                $title = 'FORMAT PUBLIKASI RESMI DINAS KELAUTAN DAN PERIKANAN PROVINSI BENGKULU';
                $columns = ['No', 'Kabupaten / Kota Pesisir', 'Kode Wilayah', 'Jumlah TPI Terdata', 'Frekuensi Pendaratan (Trip)', 'Total Volume (Kg)', 'Total Nilai Produksi (Rp)', 'Komoditas Unggulan', 'Kontribusi Wilayah (%)'];
                $rows = [];
                foreach ($data['rows'] as $idx => $r) {
                    $rows[] = [
                        $idx + 1,
                        $r['nama_kabupaten_kota'],
                        $r['kode_wilayah'],
                        $r['jumlah_tpi'],
                        $r['frekuensi_trip'],
                        number_format($r['total_volume'], 2, '.', ''),
                        number_format($r['total_nilai'], 0, '.', ''),
                        $r['komoditas_unggulan'],
                        number_format($r['kontribusi_persen'], 2, '.', '').'%',
                    ];
                }
                $totalsRow = [
                    'TOTAL',
                    '7 Kabupaten / Kota',
                    '',
                    $data['totals']['jumlah_tpi'],
                    $data['totals']['frekuensi_trip'],
                    number_format($data['totals']['total_volume'], 2, '.', ''),
                    number_format($data['totals']['total_nilai'], 0, '.', ''),
                    '',
                    '100.00%',
                ];
                break;

            case 'triwulan':
                $data = $this->calculateRekapTriwulan($filters);
                $title = 'FORMAT PELAPORAN TRIWULANAN PRODUKSI TPI (STANDAR BPS PP-TPI)';
                $columns = ['No', 'Nama TPI', 'Kabupaten / Kota', 'TW I Volume (Kg)', 'TW I Nilai (Rp)', 'TW II Volume (Kg)', 'TW II Nilai (Rp)', 'TW III Volume (Kg)', 'TW III Nilai (Rp)', 'TW IV Volume (Kg)', 'TW IV Nilai (Rp)', 'Total Tahunan Volume (Kg)', 'Total Tahunan Nilai (Rp)'];
                $rows = [];
                foreach ($data['rows'] as $idx => $r) {
                    $rows[] = [
                        $idx + 1,
                        $r['nama_tpi'],
                        $r['kabupaten_kota'],
                        number_format($r['tw1_volume'], 2, '.', ''),
                        number_format($r['tw1_nilai'], 0, '.', ''),
                        number_format($r['tw2_volume'], 2, '.', ''),
                        number_format($r['tw2_nilai'], 0, '.', ''),
                        number_format($r['tw3_volume'], 2, '.', ''),
                        number_format($r['tw3_nilai'], 0, '.', ''),
                        number_format($r['tw4_volume'], 2, '.', ''),
                        number_format($r['tw4_nilai'], 0, '.', ''),
                        number_format($r['total_volume'], 2, '.', ''),
                        number_format($r['total_nilai'], 0, '.', ''),
                    ];
                }
                $totalsRow = [
                    'TOTAL',
                    $data['totals']['count'].' TPI',
                    '',
                    number_format($data['totals']['tw1_volume'], 2, '.', ''),
                    number_format($data['totals']['tw1_nilai'], 0, '.', ''),
                    number_format($data['totals']['tw2_volume'], 2, '.', ''),
                    number_format($data['totals']['tw2_nilai'], 0, '.', ''),
                    number_format($data['totals']['tw3_volume'], 2, '.', ''),
                    number_format($data['totals']['tw3_nilai'], 0, '.', ''),
                    number_format($data['totals']['tw4_volume'], 2, '.', ''),
                    number_format($data['totals']['tw4_nilai'], 0, '.', ''),
                    number_format($data['totals']['total_volume'], 2, '.', ''),
                    number_format($data['totals']['total_nilai'], 0, '.', ''),
                ];
                break;

            case 'tpi':
            default:
                $data = $this->calculateRekapTpi($filters);
                $title = 'LAPORAN REKAPITULASI PRODUKSI PERIKANAN TANGKAP PER TEMPAT PELELANGAN IKAN (TPI)';
                $columns = ['No', 'Kode TPI', 'Nama TPI', 'Kabupaten / Kota', 'Frekuensi Pendaratan (Trip)', 'Total Volume Tangkapan (Kg)', 'Rata-rata Tangkapan / Trip (Kg)', 'Total Nilai Produksi (Rp)'];
                $rows = [];
                foreach ($data['rows'] as $idx => $r) {
                    $rows[] = [
                        $idx + 1,
                        $r['kode_tpi'],
                        $r['nama_tpi'],
                        $r['kabupaten_kota'],
                        $r['frekuensi_pendaratan'],
                        number_format($r['total_volume'], 2, '.', ''),
                        number_format($r['rata_rata_per_trip'], 2, '.', ''),
                        number_format($r['total_nilai'], 0, '.', ''),
                    ];
                }
                $totalsRow = [
                    'TOTAL',
                    '',
                    $data['totals']['count'].' TPI',
                    '',
                    $data['totals']['frekuensi_pendaratan'],
                    number_format($data['totals']['total_volume'], 2, '.', ''),
                    number_format($data['totals']['rata_rata_per_trip'], 2, '.', ''),
                    number_format($data['totals']['total_nilai'], 0, '.', ''),
                ];
                break;
        }

        return response()->view('laporan.export_excel', compact('title', 'filters', 'columns', 'rows', 'totalsRow'), 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    /**
     * Dedicated printable view with official government header and signature blocks.
     */
    public function cetak(ReportFilterRequest $request): View
    {
        $filters = $this->resolveFilters($request);
        $tipe = $request->input('tipe', 'tpi');
        $user = Auth::user();
        $tanggalCetak = Carbon::now();

        switch ($tipe) {
            case 'ikan':
                $data = $this->calculateRekapIkan($filters);
                $judulLaporan = 'REKAPITULASI PRODUKSI PERIKANAN TANGKAP MENURUT KOMODITAS IKAN';
                break;
            case 'dkp':
                $data = $this->calculateRekapDkp($filters);
                $judulLaporan = 'STANDAR PUBLIKASI RESMI DINAS KELAUTAN DAN PERIKANAN MENURUT KABUPATEN/KOTA';
                break;
            case 'triwulan':
                $data = $this->calculateRekapTriwulan($filters);
                $judulLaporan = 'LAPORAN TRIWULANAN AKTIVITAS PENDARATAN TPI (STANDAR BPS PP-TPI)';
                break;
            case 'tpi':
            default:
                $tipe = 'tpi';
                $data = $this->calculateRekapTpi($filters);
                $judulLaporan = 'REKAPITULASI PRODUKSI DAN AKTIVITAS TEMPAT PELELANGAN IKAN (TPI)';
                break;
        }

        return view('laporan.cetak', compact(
            'tipe',
            'judulLaporan',
            'filters',
            'data',
            'user',
            'tanggalCetak'
        ));
    }

    // =========================================================================
    // CALCULATION LOGIC & HELPER METHODS
    // =========================================================================

    /**
     * Calculate Rekapitulasi per TPI.
     *
     * @return array{rows: array<int, array>, totals: array}
     */
    public function calculateRekapTpi(array $filters): array
    {
        $tpiQuery = Tpi::with('wilayah')->where('status_operasi', 'aktif');

        if (! empty($filters['tpi_id'])) {
            $tpiQuery->where('id', $filters['tpi_id']);
        }

        $tpis = $tpiQuery->orderBy('id')->get();
        $rows = [];

        $totalFrekuensi = 0;
        $totalVolume = 0.0;
        $totalNilai = 0.0;

        foreach ($tpis as $tpi) {
            $landingQuery = $this->baseLandingQuery($filters)->where('tpi_id', $tpi->id);
            $landingIds = $landingQuery->pluck('id');
            $frekuensi = $landingIds->count();

            $detailQuery = DetailPendaratan::whereIn('pendaratan_id', $landingIds);
            if (! empty($filters['kategori_ikan'])) {
                $detailQuery->whereHas('jenisIkan', function ($q) use ($filters) {
                    $q->where('kategori', $filters['kategori_ikan']);
                });
            }

            $volume = (float) $detailQuery->sum('volume_kg');
            $nilai = (float) $detailQuery->sum('nilai_produksi');
            $rataPerTrip = $frekuensi > 0 ? $volume / $frekuensi : 0.0;

            $rows[] = [
                'tpi_id' => $tpi->id,
                'kode_tpi' => $tpi->kode_tpi,
                'nama_tpi' => $tpi->nama_tpi,
                'kabupaten_kota' => $tpi->wilayah->nama_kabupaten_kota ?? 'Provinsi Bengkulu',
                'frekuensi_pendaratan' => $frekuensi,
                'total_volume' => round($volume, 2),
                'rata_rata_per_trip' => round($rataPerTrip, 2),
                'total_nilai' => round($nilai, 2),
            ];

            $totalFrekuensi += $frekuensi;
            $totalVolume += $volume;
            $totalNilai += $nilai;
        }

        $totals = [
            'count' => count($rows),
            'frekuensi_pendaratan' => $totalFrekuensi,
            'total_volume' => round($totalVolume, 2),
            'rata_rata_per_trip' => $totalFrekuensi > 0 ? round($totalVolume / $totalFrekuensi, 2) : 0.0,
            'total_nilai' => round($totalNilai, 2),
        ];

        return ['rows' => $rows, 'totals' => $totals];
    }

    /**
     * Calculate Rekapitulasi per Komoditas Ikan.
     *
     * @return array{rows: array<int, array>, totals: array}
     */
    public function calculateRekapIkan(array $filters): array
    {
        $ikanQuery = JenisIkan::query();
        if (! empty($filters['kategori_ikan'])) {
            $ikanQuery->where('kategori', $filters['kategori_ikan']);
        }
        $ikans = $ikanQuery->orderBy('kode_ikan')->get();

        $landingIds = $this->baseLandingQuery($filters)->pluck('id');

        // Total volume of all species under this filter for percentage base
        $totalAllVolume = (float) DetailPendaratan::whereIn('pendaratan_id', $landingIds)
            ->when(! empty($filters['kategori_ikan']), function ($q) use ($filters) {
                $q->whereHas('jenisIkan', fn ($iq) => $iq->where('kategori', $filters['kategori_ikan']));
            })
            ->sum('volume_kg');

        $aggregatedCatches = DetailPendaratan::whereIn('pendaratan_id', $landingIds)
            ->groupBy('jenis_ikan_id')
            ->select([
                'jenis_ikan_id',
                DB::raw('COALESCE(SUM(volume_kg), 0) as total_volume'),
                DB::raw('COALESCE(SUM(nilai_produksi), 0) as total_nilai'),
            ])
            ->get()
            ->keyBy('jenis_ikan_id');

        $rows = [];
        $grandVolume = 0.0;
        $grandNilai = 0.0;

        foreach ($ikans as $ikan) {
            $item = $aggregatedCatches->get($ikan->id);
            $vol = (float) ($item->total_volume ?? 0);
            $val = (float) ($item->total_nilai ?? 0);
            $avgPrice = $vol > 0 ? $val / $vol : 0.0;
            $persen = $totalAllVolume > 0 ? ($vol / $totalAllVolume) * 100 : 0.0;

            $rows[] = [
                'ikan_id' => $ikan->id,
                'kode_ikan' => $ikan->kode_ikan,
                'nama_resmi' => $ikan->nama_resmi,
                'nama_lokal' => $ikan->nama_lokal ?? '-',
                'kategori' => $ikan->kategori,
                'satuan' => $ikan->satuan ?? 'Kg',
                'volume_kg' => round($vol, 2),
                'persentase' => round($persen, 2),
                'rata_rata_harga' => round($avgPrice, 2),
                'total_nilai' => round($val, 2),
            ];

            $grandVolume += $vol;
            $grandNilai += $val;
        }

        // Sort rows by volume_kg descending
        usort($rows, fn ($a, $b) => $b['volume_kg'] <=> $a['volume_kg']);

        $totals = [
            'count' => count($rows),
            'volume_kg' => round($grandVolume, 2),
            'persentase' => $grandVolume > 0 ? 100.0 : 0.0,
            'rata_rata_harga' => $grandVolume > 0 ? round($grandNilai / $grandVolume, 2) : 0.0,
            'total_nilai' => round($grandNilai, 2),
        ];

        return ['rows' => $rows, 'totals' => $totals];
    }

    /**
     * Calculate Standar Format Publikasi DKP Bengkulu (per Kabupaten/Kota pesisir).
     *
     * @return array{rows: array<int, array>, totals: array}
     */
    public function calculateRekapDkp(array $filters): array
    {
        // 7 Coastal Kabupaten/Kota in Bengkulu Province
        $coastalCodes = ['1701', '1703', '1704', '1705', '1706', '1709', '1771'];
        $wilayahs = Wilayah::whereIn('kode_wilayah', $coastalCodes)->orderBy('kode_wilayah')->get();

        // Calculate total province volume for contribution percentage
        $allProvinceLandingIds = $this->baseLandingQuery($filters)->pluck('id');
        $provinceTotalVolume = (float) DetailPendaratan::whereIn('pendaratan_id', $allProvinceLandingIds)
            ->when(! empty($filters['kategori_ikan']), function ($q) use ($filters) {
                $q->whereHas('jenisIkan', fn ($iq) => $iq->where('kategori', $filters['kategori_ikan']));
            })
            ->sum('volume_kg');

        $rows = [];
        $totalTpiCount = 0;
        $totalTripCount = 0;
        $totalVolumeSum = 0.0;
        $totalNilaiSum = 0.0;

        foreach ($wilayahs as $wilayah) {
            $tpiList = Tpi::where('wilayah_id', $wilayah->id)->get();
            $tpiIds = $tpiList->pluck('id');
            $jumlahTpi = $tpiList->count();

            // Filter landings for this wilayah
            $wilayahLandingQuery = $this->baseLandingQuery($filters)->whereIn('tpi_id', $tpiIds);
            $wilayahLandingIds = $wilayahLandingQuery->pluck('id');
            $frekuensiTrip = $wilayahLandingIds->count();

            $detailQuery = DetailPendaratan::whereIn('pendaratan_id', $wilayahLandingIds);
            if (! empty($filters['kategori_ikan'])) {
                $detailQuery->whereHas('jenisIkan', fn ($q) => $q->where('kategori', $filters['kategori_ikan']));
            }

            $vol = (float) $detailQuery->sum('volume_kg');
            $val = (float) $detailQuery->sum('nilai_produksi');
            $kontribusi = $provinceTotalVolume > 0 ? ($vol / $provinceTotalVolume) * 100 : 0.0;

            // Find top dominant fish species in this wilayah
            $topSpecies = DetailPendaratan::whereIn('pendaratan_id', $wilayahLandingIds)
                ->join('jenis_ikan', 'detail_pendaratan.jenis_ikan_id', '=', 'jenis_ikan.id')
                ->groupBy('jenis_ikan.nama_resmi')
                ->select(['jenis_ikan.nama_resmi', DB::raw('SUM(detail_pendaratan.volume_kg) as total_vol')])
                ->orderByDesc('total_vol')
                ->first();

            $komoditasUnggulan = $topSpecies ? $topSpecies->nama_resmi : '-';

            $rows[] = [
                'wilayah_id' => $wilayah->id,
                'kode_wilayah' => $wilayah->kode_wilayah,
                'nama_kabupaten_kota' => $wilayah->nama_kabupaten_kota,
                'jumlah_tpi' => $jumlahTpi,
                'frekuensi_trip' => $frekuensiTrip,
                'total_volume' => round($vol, 2),
                'total_nilai' => round($val, 2),
                'komoditas_unggulan' => $komoditasUnggulan,
                'kontribusi_persen' => round($kontribusi, 2),
            ];

            $totalTpiCount += $jumlahTpi;
            $totalTripCount += $frekuensiTrip;
            $totalVolumeSum += $vol;
            $totalNilaiSum += $val;
        }

        $totals = [
            'count' => count($rows),
            'jumlah_tpi' => $totalTpiCount,
            'frekuensi_trip' => $totalTripCount,
            'total_volume' => round($totalVolumeSum, 2),
            'total_nilai' => round($totalNilaiSum, 2),
            'kontribusi_persen' => $totalVolumeSum > 0 ? 100.0 : 0.0,
        ];

        return ['rows' => $rows, 'totals' => $totals];
    }

    /**
     * Calculate Triwulanan Report (Standar BPS PP-TPI).
     *
     * @return array{rows: array<int, array>, totals: array}
     */
    public function calculateRekapTriwulan(array $filters): array
    {
        $tpiQuery = Tpi::with('wilayah')->where('status_operasi', 'aktif');
        if (! empty($filters['tpi_id'])) {
            $tpiQuery->where('id', $filters['tpi_id']);
        }
        $tpis = $tpiQuery->orderBy('id')->get();

        $rows = [];
        $totTw1Vol = 0.0;
        $totTw1Nil = 0.0;
        $totTw2Vol = 0.0;
        $totTw2Nil = 0.0;
        $totTw3Vol = 0.0;
        $totTw3Nil = 0.0;
        $totTw4Vol = 0.0;
        $totTw4Nil = 0.0;
        $totYearVol = 0.0;
        $totYearNil = 0.0;

        foreach ($tpis as $tpi) {
            $qData = [];
            $tpiYearVol = 0.0;
            $tpiYearNil = 0.0;

            for ($quarter = 1; $quarter <= 4; $quarter++) {
                $startMonth = ($quarter - 1) * 3 + 1;
                $endMonth = $startMonth + 2;

                $qLandingIds = Pendaratan::where('status_verifikasi', 'diverifikasi')
                    ->where('tpi_id', $tpi->id)
                    ->whereYear('tanggal_pendaratan', $filters['tahun'])
                    ->whereMonth('tanggal_pendaratan', '>=', $startMonth)
                    ->whereMonth('tanggal_pendaratan', '<=', $endMonth)
                    ->pluck('id');

                $qDetailQuery = DetailPendaratan::whereIn('pendaratan_id', $qLandingIds);
                if (! empty($filters['kategori_ikan'])) {
                    $qDetailQuery->whereHas('jenisIkan', fn ($q) => $q->where('kategori', $filters['kategori_ikan']));
                }

                $qVol = (float) $qDetailQuery->sum('volume_kg');
                $qNil = (float) $qDetailQuery->sum('nilai_produksi');

                $qData["tw{$quarter}_volume"] = round($qVol, 2);
                $qData["tw{$quarter}_nilai"] = round($qNil, 2);

                $tpiYearVol += $qVol;
                $tpiYearNil += $qNil;

                if ($quarter === 1) {
                    $totTw1Vol += $qVol;
                    $totTw1Nil += $qNil;
                } elseif ($quarter === 2) {
                    $totTw2Vol += $qVol;
                    $totTw2Nil += $qNil;
                } elseif ($quarter === 3) {
                    $totTw3Vol += $qVol;
                    $totTw3Nil += $qNil;
                } else {
                    $totTw4Vol += $qVol;
                    $totTw4Nil += $qNil;
                }
            }

            $rows[] = array_merge([
                'tpi_id' => $tpi->id,
                'kode_tpi' => $tpi->kode_tpi,
                'nama_tpi' => $tpi->nama_tpi,
                'kabupaten_kota' => $tpi->wilayah->nama_kabupaten_kota ?? 'Provinsi Bengkulu',
                'total_volume' => round($tpiYearVol, 2),
                'total_nilai' => round($tpiYearNil, 2),
            ], $qData);

            $totYearVol += $tpiYearVol;
            $totYearNil += $tpiYearNil;
        }

        $totals = [
            'count' => count($rows),
            'tw1_volume' => round($totTw1Vol, 2),
            'tw1_nilai' => round($totTw1Nil, 2),
            'tw2_volume' => round($totTw2Vol, 2),
            'tw2_nilai' => round($totTw2Nil, 2),
            'tw3_volume' => round($totTw3Vol, 2),
            'tw3_nilai' => round($totTw3Nil, 2),
            'tw4_volume' => round($totTw4Vol, 2),
            'tw4_nilai' => round($totTw4Nil, 2),
            'total_volume' => round($totYearVol, 2),
            'total_nilai' => round($totYearNil, 2),
        ];

        return ['rows' => $rows, 'totals' => $totals];
    }

    /**
     * Resolve and validate request filter parameters.
     *
     * @return array<string, mixed>
     */
    protected function resolveFilters(Request $request): array
    {
        $user = Auth::user();
        $isAdminTpi = $user && $user->role === 'admin_tpi' && ! empty($user->tpi_id);

        $tahun = (int) $request->input('tahun', Carbon::now()->year);
        $periodeTipe = $request->input('periode_tipe', 'semua');
        $bulan = $request->filled('bulan') ? (int) $request->bulan : null;
        $triwulan = $request->filled('triwulan') ? (int) $request->triwulan : null;

        // Enforce TPI restriction for admin_tpi
        $tpiId = $isAdminTpi ? $user->tpi_id : ($request->filled('tpi_id') ? (int) $request->tpi_id : null);
        $kategoriIkan = $request->filled('kategori_ikan') ? $request->kategori_ikan : null;

        // Human-readable period label
        $periodeLabel = "Tahun {$tahun}";
        if ($periodeTipe === 'bulanan' && $bulan) {
            $monthNames = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            ];
            $periodeLabel = ($monthNames[$bulan] ?? "Bulan {$bulan}")." {$tahun}";
        } elseif ($periodeTipe === 'triwulanan' && $triwulan) {
            $triwulanLabels = [
                1 => 'Triwulan I (Jan - Mar)',
                2 => 'Triwulan II (Apr - Jun)',
                3 => 'Triwulan III (Jul - Sep)',
                4 => 'Triwulan IV (Okt - Des)',
            ];
            $periodeLabel = ($triwulanLabels[$triwulan] ?? "Triwulan {$triwulan}")." {$tahun}";
        }

        // TPI Name label for display
        $selectedTpi = null;
        if ($tpiId) {
            $selectedTpi = Tpi::find($tpiId);
        }

        return [
            'tahun' => $tahun,
            'periode_tipe' => $periodeTipe,
            'bulan' => $bulan,
            'triwulan' => $triwulan,
            'tpi_id' => $tpiId,
            'selected_tpi' => $selectedTpi,
            'kategori_ikan' => $kategoriIkan,
            'periode_label' => $periodeLabel,
            'is_admin_tpi' => $isAdminTpi,
        ];
    }

    /**
     * Build base landing query based on resolved filters.
     */
    protected function baseLandingQuery(array $filters): Builder
    {
        $query = Pendaratan::where('status_verifikasi', 'diverifikasi')
            ->whereYear('tanggal_pendaratan', $filters['tahun']);

        if (! empty($filters['tpi_id'])) {
            $query->where('tpi_id', $filters['tpi_id']);
        }

        if ($filters['periode_tipe'] === 'bulanan' && ! empty($filters['bulan'])) {
            $query->whereMonth('tanggal_pendaratan', $filters['bulan']);
        } elseif ($filters['periode_tipe'] === 'triwulanan' && ! empty($filters['triwulan'])) {
            $startMonth = ($filters['triwulan'] - 1) * 3 + 1;
            $endMonth = $startMonth + 2;
            $query->whereMonth('tanggal_pendaratan', '>=', $startMonth)
                ->whereMonth('tanggal_pendaratan', '<=', $endMonth);
        }

        return $query;
    }

    /**
     * Get list of allowed TPIs based on user role.
     */
    protected function getAllowedTpis()
    {
        $user = Auth::user();
        if ($user && $user->role === 'admin_tpi' && ! empty($user->tpi_id)) {
            return Tpi::where('id', $user->tpi_id)->get();
        }

        return Tpi::where('status_operasi', 'aktif')->orderBy('nama_tpi')->get();
    }

    /**
     * Get available years for filtering dropdown.
     *
     * @return array<int>
     */
    protected function getAvailableYears(): array
    {
        $currentYear = (int) Carbon::now()->year;
        $years = range($currentYear, $currentYear - 4);

        return $years;
    }
}
