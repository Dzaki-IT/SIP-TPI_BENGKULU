<?php

namespace App\Http\Controllers;

use App\Models\Pendaratan;
use App\Models\Tpi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VerifikasiController extends Controller
{
    /**
     * Display a listing of pending landings for verification.
     */
    public function index(Request $request): View
    {
        $query = Pendaratan::with([
            'tpi',
            'kapal',
            'nelayan',
            'alatTangkap',
            'creator',
            'verifier',
            'detailPendaratan.jenisIkan',
        ]);

        // Status tab filter (default is 'diajukan' for pending queue)
        $currentStatus = $request->input('status', 'diajukan');
        if ($currentStatus !== 'semua' && in_array($currentStatus, ['diajukan', 'diverifikasi', 'ditolak', 'draft'])) {
            $query->where('status_verifikasi', $currentStatus);
        }

        // Filter by TPI
        if ($request->filled('tpi_id')) {
            $query->where('tpi_id', $request->tpi_id);
        }

        // Filter by Date Range
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_pendaratan', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_pendaratan', '<=', $request->tanggal_sampai);
        }

        // Search Query (matching kode_pendaratan, nama_kapal, or nama nelayan)
        $search = trim($request->input('q', $request->input('search', '')));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('kode_pendaratan', 'like', "%{$search}%")
                  ->orWhereHas('kapal', function ($kq) use ($search) {
                      $kq->where('nama_kapal', 'like', "%{$search}%");
                  })
                  ->orWhereHas('nelayan', function ($nq) use ($search) {
                      $nq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        // Tab counters
        $countDiajukan = Pendaratan::where('status_verifikasi', 'diajukan')->count();
        $countDiverifikasi = Pendaratan::where('status_verifikasi', 'diverifikasi')->count();
        $countDitolak = Pendaratan::where('status_verifikasi', 'ditolak')->count();
        $countSemua = Pendaratan::count();

        $pendaratanList = $query->orderBy('tanggal_pendaratan', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $tpiList = Tpi::aktif()->orderBy('nama_tpi')->get();

        return view('verifikasi.index', compact(
            'pendaratanList',
            'tpiList',
            'currentStatus',
            'countDiajukan',
            'countDiverifikasi',
            'countDitolak',
            'countSemua'
        ));
    }

    /**
     * Display the specified landing review screen.
     */
    public function show(Pendaratan $pendaratan): View
    {
        $pendaratan->load([
            'tpi.wilayah',
            'kapal',
            'nelayan',
            'alatTangkap',
            'creator',
            'verifier',
            'detailPendaratan.jenisIkan',
        ]);

        return view('verifikasi.show', compact('pendaratan'));
    }

    /**
     * Approve and verify the landing record.
     */
    public function approve(Request $request, Pendaratan $pendaratan): RedirectResponse
    {
        // Strictly verify user role
        if (!Auth::user()?->isVerifikator()) {
            abort(403, 'Akses ditolak. Hanya petugas berwenang (Verifikator) yang berhak menyetujui data pendaratan.');
        }

        // Only records with status 'diajukan' can be approved
        if ($pendaratan->status_verifikasi !== 'diajukan') {
            return redirect()->back()
                ->with('error', 'Hanya data pendaratan yang berstatus diajukan yang dapat disetujui & diverifikasi.');
        }

        $pendaratan->update([
            'status_verifikasi' => 'diverifikasi',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'catatan_verifikasi' => $request->input('catatan', null),
        ]);

        return redirect()->route('verifikasi.index')
            ->with('success', "Data pendaratan {$pendaratan->kode_pendaratan} berhasil disetujui & diverifikasi.");
    }

    /**
     * Reject landing record with revision notes.
     */
    public function reject(Request $request, Pendaratan $pendaratan): RedirectResponse
    {
        // Strictly verify user role
        if (!Auth::user()?->isVerifikator()) {
            abort(403, 'Akses ditolak. Hanya petugas berwenang (Verifikator) yang berhak menolak data pendaratan.');
        }

        // Only records with status 'diajukan' can be rejected
        if ($pendaratan->status_verifikasi !== 'diajukan') {
            return redirect()->back()
                ->with('error', 'Hanya data pendaratan yang berstatus diajukan yang dapat ditolak.');
        }

        $validated = $request->validate([
            'catatan_verifikasi' => ['required', 'string', 'min:5'],
        ], [
            'catatan_verifikasi.required' => 'Catatan alasan penolakan wajib diisi agar Admin TPI dapat melakukan perbaikan.',
            'catatan_verifikasi.min' => 'Catatan alasan penolakan minimal 5 karakter.',
        ]);

        $pendaratan->update([
            'status_verifikasi' => 'ditolak',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'catatan_verifikasi' => $validated['catatan_verifikasi'],
        ]);

        return redirect()->route('verifikasi.index')
            ->with('warning', "Data pendaratan {$pendaratan->kode_pendaratan} ditolak dengan catatan perbaikan.");
    }
}
