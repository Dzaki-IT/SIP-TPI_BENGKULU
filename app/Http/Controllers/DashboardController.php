<?php

namespace App\Http\Controllers;

use App\Models\DetailPendaratan;
use App\Models\JenisIkan;
use App\Models\Kapal;
use App\Models\Nelayan;
use App\Models\Pendaratan;
use App\Models\Tpi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the application dashboard with relevant statistics, recent activities, and analytical charts.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $isAdminTpi = $user->role === 'admin_tpi' && ! empty($user->tpi_id);
        $tpiId = $isAdminTpi ? $user->tpi_id : null;
        $currentYear = (int) Carbon::now()->year;

        if ($isAdminTpi) {
            $totalTpiAktif = Tpi::where('id', $tpiId)->where('status_operasi', 'aktif')->count();
            $totalNelayan = Nelayan::where('tpi_id', $tpiId)->count();
            $totalKapal = Kapal::whereHas('nelayan', function ($query) use ($tpiId) {
                $query->where('tpi_id', $tpiId);
            })->count();
            $totalPendaratanDiverifikasi = Pendaratan::where('tpi_id', $tpiId)
                ->where('status_verifikasi', 'diverifikasi')
                ->count();
            $pendaratanMenungguVerifikasi = Pendaratan::where('tpi_id', $tpiId)
                ->where('status_verifikasi', 'diajukan')
                ->count();
            $totalVolumeIkan = (float) DetailPendaratan::whereHas('pendaratan', function ($query) use ($tpiId) {
                $query->where('tpi_id', $tpiId)->where('status_verifikasi', 'diverifikasi');
            })->sum('volume_kg');
            $totalNilaiProduksi = (float) DetailPendaratan::whereHas('pendaratan', function ($query) use ($tpiId) {
                $query->where('tpi_id', $tpiId)->where('status_verifikasi', 'diverifikasi');
            })->sum('nilai_produksi');

            $recentPendaratan = Pendaratan::with(['tpi', 'kapal', 'nelayan'])
                ->where('tpi_id', $tpiId)
                ->latest('tanggal_pendaratan')
                ->latest('id')
                ->take(5)
                ->get();
        } else {
            $totalTpiAktif = Tpi::where('status_operasi', 'aktif')->count();
            $totalNelayan = Nelayan::count();
            $totalKapal = Kapal::count();
            $totalPendaratanDiverifikasi = Pendaratan::where('status_verifikasi', 'diverifikasi')->count();
            $pendaratanMenungguVerifikasi = Pendaratan::where('status_verifikasi', 'diajukan')->count();
            $totalVolumeIkan = (float) DetailPendaratan::whereHas('pendaratan', function ($query) {
                $query->where('status_verifikasi', 'diverifikasi');
            })->sum('volume_kg');
            $totalNilaiProduksi = (float) DetailPendaratan::whereHas('pendaratan', function ($query) {
                $query->where('status_verifikasi', 'diverifikasi');
            })->sum('nilai_produksi');

            $recentPendaratan = Pendaratan::with(['tpi', 'kapal', 'nelayan'])
                ->latest('tanggal_pendaratan')
                ->latest('id')
                ->take(5)
                ->get();
        }

        // Global count for verifikator notification
        $pendingVerificationCount = Pendaratan::where('status_verifikasi', 'diajukan')->count();
        $assignedTpi = $user->tpi;

        // -------------------------------------------------------------
        // 1. Chart: Tren Produksi Bulanan (Tahun Berjalan)
        // -------------------------------------------------------------
        $monthlyQuery = DetailPendaratan::query()
            ->join('pendaratan', 'detail_pendaratan.pendaratan_id', '=', 'pendaratan.id')
            ->where('pendaratan.status_verifikasi', 'diverifikasi')
            ->whereYear('pendaratan.tanggal_pendaratan', $currentYear);

        if ($isAdminTpi) {
            $monthlyQuery->where('pendaratan.tpi_id', $tpiId);
        }

        $monthlyDetails = $monthlyQuery->select([
            'pendaratan.tanggal_pendaratan',
            'detail_pendaratan.volume_kg',
            'detail_pendaratan.nilai_produksi',
        ])->get();

        $monthVolumes = array_fill(1, 12, 0.0);
        $monthValues = array_fill(1, 12, 0.0);

        foreach ($monthlyDetails as $row) {
            $monthNum = (int) Carbon::parse($row->tanggal_pendaratan)->format('n');
            if ($monthNum >= 1 && $monthNum <= 12) {
                $monthVolumes[$monthNum] += (float) $row->volume_kg;
                $monthValues[$monthNum] += (float) $row->nilai_produksi;
            }
        }

        $trendBulanan = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'full_labels' => ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            'volumes' => array_values(array_map(fn ($v) => round($v, 2), $monthVolumes)),
            'values' => array_values(array_map(fn ($v) => round($v, 2), $monthValues)),
            'tahun' => $currentYear,
        ];

        // -------------------------------------------------------------
        // 2. Chart: Kontribusi Produksi per TPI
        // -------------------------------------------------------------
        $tpiQuery = Tpi::query()->where('status_operasi', 'aktif');
        if ($isAdminTpi) {
            $tpiQuery->where('id', $tpiId);
        }
        $activeTpis = $tpiQuery->orderBy('id')->get();

        $tpiLabels = [];
        $tpiVolumes = [];
        $tpiValues = [];

        foreach ($activeTpis as $tpiItem) {
            $tpiAgg = DetailPendaratan::query()
                ->join('pendaratan', 'detail_pendaratan.pendaratan_id', '=', 'pendaratan.id')
                ->where('pendaratan.status_verifikasi', 'diverifikasi')
                ->where('pendaratan.tpi_id', $tpiItem->id)
                ->selectRaw('COALESCE(SUM(detail_pendaratan.volume_kg), 0) as total_volume, COALESCE(SUM(detail_pendaratan.nilai_produksi), 0) as total_nilai')
                ->first();

            $tpiLabels[] = $tpiItem->nama_tpi;
            $tpiVolumes[] = round((float) ($tpiAgg->total_volume ?? 0), 2);
            $tpiValues[] = round((float) ($tpiAgg->total_nilai ?? 0), 2);
        }

        $produksiPerTpi = [
            'labels' => $tpiLabels,
            'volumes' => $tpiVolumes,
            'values' => $tpiValues,
        ];

        // -------------------------------------------------------------
        // 3. Chart: Top 7 Komoditas Ikan Tangkapan Tertinggi
        // -------------------------------------------------------------
        $topQuery = DetailPendaratan::query()
            ->join('pendaratan', 'detail_pendaratan.pendaratan_id', '=', 'pendaratan.id')
            ->join('jenis_ikan', 'detail_pendaratan.jenis_ikan_id', '=', 'jenis_ikan.id')
            ->where('pendaratan.status_verifikasi', 'diverifikasi');

        if ($isAdminTpi) {
            $topQuery->where('pendaratan.tpi_id', $tpiId);
        }

        $topKomoditasRecords = $topQuery
            ->groupBy('jenis_ikan.id', 'jenis_ikan.nama_resmi')
            ->select([
                'jenis_ikan.nama_resmi',
                DB::raw('COALESCE(SUM(detail_pendaratan.volume_kg), 0) as total_volume'),
                DB::raw('COALESCE(SUM(detail_pendaratan.nilai_produksi), 0) as total_nilai'),
            ])
            ->orderByDesc('total_volume')
            ->take(7)
            ->get();

        $topKomoditas = [
            'labels' => $topKomoditasRecords->pluck('nama_resmi')->toArray(),
            'volumes' => $topKomoditasRecords->map(fn ($item) => round((float) $item->total_volume, 2))->toArray(),
            'values' => $topKomoditasRecords->map(fn ($item) => round((float) $item->total_nilai, 2))->toArray(),
        ];

        // -------------------------------------------------------------
        // 4. Chart: Komposisi Kategori Ikan
        // -------------------------------------------------------------
        $standardCategories = ['Pelagis Besar', 'Pelagis Kecil', 'Demersal', 'Non-Ikan'];
        $catQuery = DetailPendaratan::query()
            ->join('pendaratan', 'detail_pendaratan.pendaratan_id', '=', 'pendaratan.id')
            ->join('jenis_ikan', 'detail_pendaratan.jenis_ikan_id', '=', 'jenis_ikan.id')
            ->where('pendaratan.status_verifikasi', 'diverifikasi');

        if ($isAdminTpi) {
            $catQuery->where('pendaratan.tpi_id', $tpiId);
        }

        $catRecords = $catQuery
            ->groupBy('jenis_ikan.kategori')
            ->select([
                'jenis_ikan.kategori',
                DB::raw('COALESCE(SUM(detail_pendaratan.volume_kg), 0) as total_volume'),
            ])
            ->pluck('total_volume', 'kategori')
            ->toArray();

        $kategoriLabels = [];
        $kategoriVolumes = [];

        foreach ($standardCategories as $catName) {
            $kategoriLabels[] = $catName;
            $kategoriVolumes[] = round((float) ($catRecords[$catName] ?? 0), 2);
        }

        // Add any extra categories if present (e.g. 'Lainnya')
        foreach ($catRecords as $catName => $vol) {
            if (! in_array($catName, $standardCategories, true) && (float) $vol > 0) {
                $kategoriLabels[] = $catName;
                $kategoriVolumes[] = round((float) $vol, 2);
            }
        }

        $kategoriIkan = [
            'labels' => $kategoriLabels,
            'volumes' => $kategoriVolumes,
        ];

        return view('dashboard.index', compact(
            'user',
            'isAdminTpi',
            'assignedTpi',
            'totalTpiAktif',
            'totalNelayan',
            'totalKapal',
            'totalPendaratanDiverifikasi',
            'pendaratanMenungguVerifikasi',
            'totalVolumeIkan',
            'totalNilaiProduksi',
            'recentPendaratan',
            'pendingVerificationCount',
            'trendBulanan',
            'produksiPerTpi',
            'topKomoditas',
            'kategoriIkan'
        ));
    }
}
