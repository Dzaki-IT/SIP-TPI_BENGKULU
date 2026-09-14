<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Tpi;
use App\Models\Wilayah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TpiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Tpi::with('wilayah')->withCount(['nelayan', 'pendaratan']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_tpi', 'like', "%{$search}%")
                  ->orWhere('kode_tpi', 'like', "%{$search}%")
                  ->orWhere('instansi_pengelola', 'like', "%{$search}%");
            });
        }

        if ($request->filled('wilayah_id')) {
            $query->where('wilayah_id', $request->wilayah_id);
        }

        if ($request->filled('status_operasi')) {
            $query->where('status_operasi', $request->status_operasi);
        }

        $tpis = $query->orderBy('kode_tpi')->paginate(10)->withQueryString();
        $wilayahList = Wilayah::orderBy('nama_kabupaten_kota')->get();

        return view('master.tpi.index', compact('tpis', 'wilayahList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $wilayahList = Wilayah::orderBy('nama_kabupaten_kota')->get();

        return view('master.tpi.create', compact('wilayahList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_tpi' => ['required', 'string', 'max:20', 'unique:tpi,kode_tpi'],
            'nama_tpi' => ['required', 'string', 'max:100'],
            'wilayah_id' => ['required', 'exists:wilayah,id'],
            'alamat' => ['nullable', 'string'],
            'instansi_pengelola' => ['nullable', 'string', 'max:100'],
            'jenis_perairan' => ['required', 'in:laut'],
            'status_operasi' => ['required', 'in:aktif,tidak_aktif'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'kode_tpi.required' => 'Kode TPI wajib diisi.',
            'kode_tpi.unique' => 'Kode TPI sudah digunakan.',
            'nama_tpi.required' => 'Nama TPI wajib diisi.',
            'wilayah_id.required' => 'Wilayah kabupaten/kota wajib dipilih.',
            'wilayah_id.exists' => 'Wilayah yang dipilih tidak valid.',
            'status_operasi.required' => 'Status operasi wajib dipilih.',
        ]);

        Tpi::create($validated);

        return redirect()->route('tpi.index')->with('success', 'Data TPI berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tpi $tpi): RedirectResponse
    {
        return redirect()->route('tpi.edit', $tpi);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tpi $tpi): View
    {
        $wilayahList = Wilayah::orderBy('nama_kabupaten_kota')->get();

        return view('master.tpi.edit', compact('tpi', 'wilayahList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tpi $tpi): RedirectResponse
    {
        $validated = $request->validate([
            'kode_tpi' => ['required', 'string', 'max:20', Rule::unique('tpi', 'kode_tpi')->ignore($tpi->id)],
            'nama_tpi' => ['required', 'string', 'max:100'],
            'wilayah_id' => ['required', 'exists:wilayah,id'],
            'alamat' => ['nullable', 'string'],
            'instansi_pengelola' => ['nullable', 'string', 'max:100'],
            'jenis_perairan' => ['required', 'in:laut'],
            'status_operasi' => ['required', 'in:aktif,tidak_aktif'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'kode_tpi.required' => 'Kode TPI wajib diisi.',
            'kode_tpi.unique' => 'Kode TPI sudah digunakan.',
            'nama_tpi.required' => 'Nama TPI wajib diisi.',
            'wilayah_id.required' => 'Wilayah kabupaten/kota wajib dipilih.',
            'wilayah_id.exists' => 'Wilayah yang dipilih tidak valid.',
            'status_operasi.required' => 'Status operasi wajib dipilih.',
        ]);

        $tpi->update($validated);

        return redirect()->route('tpi.index')->with('success', 'Data TPI berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tpi $tpi): RedirectResponse
    {
        if ($tpi->pendaratan()->exists()) {
            return redirect()->route('tpi.index')->with('error', "TPI '{$tpi->nama_tpi}' tidak dapat dihapus karena memiliki riwayat pendaratan aktif.");
        }

        if ($tpi->nelayan()->exists()) {
            return redirect()->route('tpi.index')->with('error', "TPI '{$tpi->nama_tpi}' tidak dapat dihapus karena memiliki nelayan terdaftar.");
        }

        $nama = $tpi->nama_tpi;
        $tpi->delete();

        return redirect()->route('tpi.index')->with('success', "Data TPI '{$nama}' berhasil dihapus.");
    }
}
