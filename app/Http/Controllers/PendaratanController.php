<?php

namespace App\Http\Controllers;

use App\Models\AlatTangkap;
use App\Models\DetailPendaratan;
use App\Models\JenisIkan;
use App\Models\Kapal;
use App\Models\Nelayan;
use App\Models\Pendaratan;
use App\Models\Tpi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PendaratanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Base query with eager loaded relationships
        $query = Pendaratan::with([
            'tpi',
            'kapal',
            'nelayan',
            'alatTangkap',
            'detailPendaratan.jenisIkan',
        ]);

        // Filter by TPI: Enforce or default for admin_tpi, filterable for others
        if ($user && $user->role === 'admin_tpi' && $user->tpi_id) {
            $query->where('tpi_id', $user->tpi_id);
        } elseif ($request->filled('tpi_id')) {
            $query->where('tpi_id', $request->tpi_id);
        }

        // Filter by Status
        if ($request->filled('status_verifikasi') && in_array($request->status_verifikasi, ['draft', 'diajukan', 'diverifikasi', 'ditolak'])) {
            $query->where('status_verifikasi', $request->status_verifikasi);
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

        // Calculate summary counters for the user's accessible scope
        $statsBase = Pendaratan::query();
        if ($user && $user->role === 'admin_tpi' && $user->tpi_id) {
            $statsBase->where('tpi_id', $user->tpi_id);
        }

        $summary = [
            'total' => (clone $statsBase)->count(),
            'draft' => (clone $statsBase)->where('status_verifikasi', 'draft')->count(),
            'diajukan' => (clone $statsBase)->where('status_verifikasi', 'diajukan')->count(),
            'diverifikasi' => (clone $statsBase)->where('status_verifikasi', 'diverifikasi')->count(),
            'ditolak' => (clone $statsBase)->where('status_verifikasi', 'ditolak')->count(),
            'total_volume' => (float) DetailPendaratan::whereIn('pendaratan_id', (clone $statsBase)->select('id'))->sum('volume_kg'),
            'total_nilai' => (float) DetailPendaratan::whereIn('pendaratan_id', (clone $statsBase)->select('id'))->sum('nilai_produksi'),
        ];

        // Paginate results ordered by date descending
        $pendaratan = $query->orderBy('tanggal_pendaratan', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $tpiList = Tpi::aktif()->orderBy('nama_tpi')->get();

        return view('pendaratan.index', compact('pendaratan', 'summary', 'tpiList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = Auth::user();

        $tpiList = Tpi::aktif()->orderBy('nama_tpi')->get();
        $kapalList = Kapal::aktif()->orderBy('nama_kapal')->get();
        $nelayanList = Nelayan::aktif()->orderBy('nama')->get();
        $alatTangkapList = AlatTangkap::aktif()->orderBy('nama_alat')->get();
        $jenisIkanList = JenisIkan::aktif()->orderBy('nama_resmi')->get();

        $defaultTpiId = ($user && $user->role === 'admin_tpi') ? $user->tpi_id : null;

        return view('pendaratan.create', compact(
            'tpiList',
            'kapalList',
            'nelayanList',
            'alatTangkapList',
            'jenisIkanList',
            'defaultTpiId'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // If admin_tpi with assigned tpi_id, enforce tpi_id
        if ($user && $user->role === 'admin_tpi' && $user->tpi_id && !$request->filled('tpi_id')) {
            $request->merge(['tpi_id' => $user->tpi_id]);
        }

        $validated = $request->validate([
            'tpi_id' => ['required', 'exists:tpi,id'],
            'tanggal_pendaratan' => ['required', 'date', 'before_or_equal:today'],
            'kapal_id' => ['nullable', 'exists:kapal,id'],
            'nelayan_id' => ['nullable', 'exists:nelayan,id'],
            'alat_tangkap_id' => ['nullable', 'exists:alat_tangkap,id'],
            'action' => ['required', 'in:draft,diajukan'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.jenis_ikan_id' => ['required', 'exists:jenis_ikan,id'],
            'items.*.volume_kg' => ['required', 'numeric', 'min:0.01'],
            'items.*.harga_per_kg' => ['nullable', 'numeric', 'min:0'],
            'items.*.keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'tpi_id.required' => 'Pangkalan TPI wajib dipilih.',
            'tpi_id.exists' => 'Pangkalan TPI yang dipilih tidak valid.',
            'tanggal_pendaratan.required' => 'Tanggal pendaratan wajib diisi.',
            'tanggal_pendaratan.before_or_equal' => 'Tanggal pendaratan tidak boleh melebihi tanggal hari ini.',
            'action.required' => 'Tindakan simpan wajib ditentukan.',
            'action.in' => 'Tindakan simpan harus berupa draft atau diajukan.',
            'items.required' => 'Minimal satu jenis ikan wajib dicatat.',
            'items.min' => 'Minimal satu jenis ikan wajib dicatat.',
            'items.*.jenis_ikan_id.required' => 'Komoditas jenis ikan wajib dipilih.',
            'items.*.jenis_ikan_id.exists' => 'Jenis ikan yang dipilih tidak valid.',
            'items.*.volume_kg.required' => 'Volume (Kg) wajib diisi.',
            'items.*.volume_kg.min' => 'Volume minimal 0.01 Kg.',
            'items.*.harga_per_kg.min' => 'Harga per Kg tidak boleh negatif.',
        ]);

        $pendaratan = DB::transaction(function () use ($validated, $user) {
            $pendaratan = Pendaratan::create([
                'tpi_id' => $validated['tpi_id'],
                'tanggal_pendaratan' => $validated['tanggal_pendaratan'],
                'kapal_id' => $validated['kapal_id'] ?? null,
                'nelayan_id' => $validated['nelayan_id'] ?? null,
                'alat_tangkap_id' => $validated['alat_tangkap_id'] ?? null,
                'status_verifikasi' => $validated['action'],
                'created_by' => $user ? $user->id : auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                $volume = (float) $item['volume_kg'];
                $harga = isset($item['harga_per_kg']) && $item['harga_per_kg'] !== '' ? (float) $item['harga_per_kg'] : 0;
                $nilai = $volume * $harga;

                $pendaratan->detailPendaratan()->create([
                    'jenis_ikan_id' => $item['jenis_ikan_id'],
                    'volume_kg' => $volume,
                    'harga_per_kg' => $harga,
                    'nilai_produksi' => $nilai,
                    'keterangan' => $item['keterangan'] ?? null,
                ]);
            }

            return $pendaratan;
        });

        $message = $validated['action'] === 'diajukan'
            ? "Data pendaratan {$pendaratan->kode_pendaratan} berhasil disimpan dan diajukan untuk verifikasi."
            : "Data pendaratan {$pendaratan->kode_pendaratan} berhasil disimpan sebagai draft.";

        return redirect()->route('pendaratan.show', $pendaratan)->with('success', $message);
    }

    /**
     * Display the specified resource.
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

        return view('pendaratan.show', compact('pendaratan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pendaratan $pendaratan): View|RedirectResponse
    {
        // Only draft or ditolak records can be edited
        if (!in_array($pendaratan->status_verifikasi, ['draft', 'ditolak'])) {
            return redirect()->route('pendaratan.show', $pendaratan)
                ->with('error', 'Data pendaratan yang sedang diajukan atau sudah diverifikasi tidak dapat diubah.');
        }

        $user = Auth::user();
        if ($user && $user->role === 'admin_tpi' && $user->tpi_id && $pendaratan->tpi_id !== $user->tpi_id) {
            abort(403, 'Anda tidak memiliki hak untuk mengubah data pendaratan dari TPI lain.');
        }

        $pendaratan->load(['detailPendaratan.jenisIkan', 'tpi', 'verifier']);

        $tpiList = Tpi::aktif()->orderBy('nama_tpi')->get();
        $kapalList = Kapal::aktif()->orderBy('nama_kapal')->get();
        $nelayanList = Nelayan::aktif()->orderBy('nama')->get();
        $alatTangkapList = AlatTangkap::aktif()->orderBy('nama_alat')->get();
        $jenisIkanList = JenisIkan::aktif()->orderBy('nama_resmi')->get();

        $defaultTpiId = ($user && $user->role === 'admin_tpi') ? $user->tpi_id : $pendaratan->tpi_id;

        return view('pendaratan.edit', compact(
            'pendaratan',
            'tpiList',
            'kapalList',
            'nelayanList',
            'alatTangkapList',
            'jenisIkanList',
            'defaultTpiId'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pendaratan $pendaratan): RedirectResponse
    {
        // Check business rule
        if (!in_array($pendaratan->status_verifikasi, ['draft', 'ditolak'])) {
            return redirect()->route('pendaratan.show', $pendaratan)
                ->with('error', 'Data pendaratan yang sedang diajukan atau sudah diverifikasi tidak dapat diubah.');
        }

        $user = Auth::user();
        if ($user && $user->role === 'admin_tpi' && $user->tpi_id && $pendaratan->tpi_id !== $user->tpi_id) {
            abort(403, 'Anda tidak memiliki hak untuk memperbarui data pendaratan dari TPI lain.');
        }

        if ($user && $user->role === 'admin_tpi' && $user->tpi_id && !$request->filled('tpi_id')) {
            $request->merge(['tpi_id' => $user->tpi_id]);
        }

        $validated = $request->validate([
            'tpi_id' => ['required', 'exists:tpi,id'],
            'tanggal_pendaratan' => ['required', 'date', 'before_or_equal:today'],
            'kapal_id' => ['nullable', 'exists:kapal,id'],
            'nelayan_id' => ['nullable', 'exists:nelayan,id'],
            'alat_tangkap_id' => ['nullable', 'exists:alat_tangkap,id'],
            'action' => ['required', 'in:draft,diajukan'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.jenis_ikan_id' => ['required', 'exists:jenis_ikan,id'],
            'items.*.volume_kg' => ['required', 'numeric', 'min:0.01'],
            'items.*.harga_per_kg' => ['nullable', 'numeric', 'min:0'],
            'items.*.keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'tpi_id.required' => 'Pangkalan TPI wajib dipilih.',
            'tpi_id.exists' => 'Pangkalan TPI yang dipilih tidak valid.',
            'tanggal_pendaratan.required' => 'Tanggal pendaratan wajib diisi.',
            'tanggal_pendaratan.before_or_equal' => 'Tanggal pendaratan tidak boleh melebihi tanggal hari ini.',
            'action.required' => 'Tindakan simpan wajib ditentukan.',
            'action.in' => 'Tindakan simpan harus berupa draft atau diajukan.',
            'items.required' => 'Minimal satu jenis ikan wajib dicatat.',
            'items.min' => 'Minimal satu jenis ikan wajib dicatat.',
            'items.*.jenis_ikan_id.required' => 'Komoditas jenis ikan wajib dipilih.',
            'items.*.jenis_ikan_id.exists' => 'Jenis ikan yang dipilih tidak valid.',
            'items.*.volume_kg.required' => 'Volume (Kg) wajib diisi.',
            'items.*.volume_kg.min' => 'Volume minimal 0.01 Kg.',
            'items.*.harga_per_kg.min' => 'Harga per Kg tidak boleh negatif.',
        ]);

        DB::transaction(function () use ($validated, $pendaratan) {
            $pendaratan->update([
                'tpi_id' => $validated['tpi_id'],
                'tanggal_pendaratan' => $validated['tanggal_pendaratan'],
                'kapal_id' => $validated['kapal_id'] ?? null,
                'nelayan_id' => $validated['nelayan_id'] ?? null,
                'alat_tangkap_id' => $validated['alat_tangkap_id'] ?? null,
                'status_verifikasi' => $validated['action'],
            ]);

            // Replace existing items
            $pendaratan->detailPendaratan()->delete();

            foreach ($validated['items'] as $item) {
                $volume = (float) $item['volume_kg'];
                $harga = isset($item['harga_per_kg']) && $item['harga_per_kg'] !== '' ? (float) $item['harga_per_kg'] : 0;
                $nilai = $volume * $harga;

                $pendaratan->detailPendaratan()->create([
                    'jenis_ikan_id' => $item['jenis_ikan_id'],
                    'volume_kg' => $volume,
                    'harga_per_kg' => $harga,
                    'nilai_produksi' => $nilai,
                    'keterangan' => $item['keterangan'] ?? null,
                ]);
            }
        });

        $message = $validated['action'] === 'diajukan'
            ? "Data pendaratan {$pendaratan->kode_pendaratan} berhasil diperbarui dan diajukan untuk verifikasi."
            : "Data pendaratan {$pendaratan->kode_pendaratan} berhasil diperbarui sebagai draft.";

        return redirect()->route('pendaratan.show', $pendaratan)->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pendaratan $pendaratan): RedirectResponse
    {
        // Strict business rule: Only draft records can be deleted
        if ($pendaratan->status_verifikasi !== 'draft') {
            return redirect()->route('pendaratan.index')
                ->with('error', 'Hanya data pendaratan berstatus draft yang dapat dihapus.');
        }

        $user = Auth::user();
        if ($user && $user->role === 'admin_tpi' && $user->tpi_id && $pendaratan->tpi_id !== $user->tpi_id) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus data pendaratan dari TPI lain.');
        }

        $kode = $pendaratan->kode_pendaratan;
        $pendaratan->delete();

        return redirect()->route('pendaratan.index')
            ->with('success', "Data pendaratan {$kode} berhasil dihapus.");
    }

    /**
     * Submit draft or rejected landing for verification.
     */
    public function ajukan(Pendaratan $pendaratan): RedirectResponse
    {
        if (!in_array($pendaratan->status_verifikasi, ['draft', 'ditolak'])) {
            return redirect()->back()
                ->with('error', 'Hanya data pendaratan berstatus draft atau ditolak yang dapat diajukan untuk verifikasi.');
        }

        $user = Auth::user();
        if ($user && $user->role === 'admin_tpi' && $user->tpi_id && $pendaratan->tpi_id !== $user->tpi_id) {
            abort(403, 'Anda tidak berhak mengajukan data pendaratan dari TPI lain.');
        }

        $pendaratan->update([
            'status_verifikasi' => 'diajukan',
        ]);

        return redirect()->back()
            ->with('success', "Data pendaratan {$pendaratan->kode_pendaratan} berhasil diajukan untuk verifikasi dinas.");
    }
}
