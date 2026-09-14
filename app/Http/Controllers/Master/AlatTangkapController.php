<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\AlatTangkap;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AlatTangkapController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = AlatTangkap::withCount('pendaratan');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_alat', 'like', "%{$search}%")
                  ->orWhere('kode_alat', 'like', "%{$search}%")
                  ->orWhere('jenis_kategori', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jenis_kategori')) {
            $query->where('jenis_kategori', $request->jenis_kategori);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif' ? 1 : 0);
        }

        $alatTangkaps = $query->orderBy('kode_alat')->paginate(10)->withQueryString();

        $kategoriList = AlatTangkap::select('jenis_kategori')
            ->whereNotNull('jenis_kategori')
            ->distinct()
            ->orderBy('jenis_kategori')
            ->pluck('jenis_kategori');

        return view('master.alat-tangkap.index', compact('alatTangkaps', 'kategoriList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $existingCategories = AlatTangkap::select('jenis_kategori')
            ->whereNotNull('jenis_kategori')
            ->distinct()
            ->pluck('jenis_kategori');

        return view('master.alat-tangkap.create', compact('existingCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_alat' => ['required', 'string', 'max:20', 'unique:alat_tangkap,kode_alat'],
            'nama_alat' => ['required', 'string', 'max:100'],
            'jenis_kategori' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'kode_alat.required' => 'Kode alat tangkap wajib diisi.',
            'kode_alat.unique' => 'Kode alat tangkap sudah terdaftar.',
            'nama_alat.required' => 'Nama alat tangkap wajib diisi.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        AlatTangkap::create($validated);

        return redirect()->route('alat-tangkap.index')->with('success', 'Data alat tangkap berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AlatTangkap $alatTangkap): RedirectResponse
    {
        return redirect()->route('alat-tangkap.edit', $alatTangkap);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AlatTangkap $alatTangkap): View
    {
        $existingCategories = AlatTangkap::select('jenis_kategori')
            ->whereNotNull('jenis_kategori')
            ->distinct()
            ->pluck('jenis_kategori');

        return view('master.alat-tangkap.edit', compact('alatTangkap', 'existingCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AlatTangkap $alatTangkap): RedirectResponse
    {
        $validated = $request->validate([
            'kode_alat' => ['required', 'string', 'max:20', Rule::unique('alat_tangkap', 'kode_alat')->ignore($alatTangkap->id)],
            'nama_alat' => ['required', 'string', 'max:100'],
            'jenis_kategori' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'kode_alat.required' => 'Kode alat tangkap wajib diisi.',
            'kode_alat.unique' => 'Kode alat tangkap sudah terdaftar.',
            'nama_alat.required' => 'Nama alat tangkap wajib diisi.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $alatTangkap->update($validated);

        return redirect()->route('alat-tangkap.index')->with('success', 'Data alat tangkap berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AlatTangkap $alatTangkap): RedirectResponse
    {
        if ($alatTangkap->pendaratan()->exists()) {
            return redirect()->route('alat-tangkap.index')->with('error', "Alat tangkap '{$alatTangkap->nama_alat}' tidak dapat dihapus karena digunakan dalam data pendaratan.");
        }

        $nama = $alatTangkap->nama_alat;
        $alatTangkap->delete();

        return redirect()->route('alat-tangkap.index')->with('success', "Data alat tangkap '{$nama}' berhasil dihapus.");
    }
}
