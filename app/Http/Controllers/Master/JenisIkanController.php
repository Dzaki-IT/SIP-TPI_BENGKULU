<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\JenisIkan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JenisIkanController extends Controller
{
    /**
     * Categories list.
     */
    protected array $categories = [
        'Pelagis Besar',
        'Pelagis Kecil',
        'Demersal',
        'Non-Ikan',
        'Lainnya',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', JenisIkan::class);

        $query = JenisIkan::withCount('detailPendaratan');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_resmi', 'like', "%{$search}%")
                    ->orWhere('nama_lokal', 'like', "%{$search}%")
                    ->orWhere('kode_ikan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif' ? 1 : 0);
        }

        $jenisIkans = $query->orderBy('nama_resmi')->paginate(10)->withQueryString();
        $categories = $this->categories;

        return view('master.jenis-ikan.index', compact('jenisIkans', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', JenisIkan::class);

        $categories = $this->categories;

        return view('master.jenis-ikan.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', JenisIkan::class);

        $validated = $request->validate([
            'kode_ikan' => ['required', 'string', 'max:20', 'unique:jenis_ikan,kode_ikan'],
            'nama_resmi' => ['required', 'string', 'max:100'],
            'nama_lokal' => ['nullable', 'string', 'max:100'],
            'kategori' => ['required', 'string', 'max:100'],
            'satuan' => ['required', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'kode_ikan.required' => 'Kode jenis ikan wajib diisi.',
            'kode_ikan.unique' => 'Kode jenis ikan sudah terdaftar.',
            'nama_resmi.required' => 'Nama resmi (komersial) wajib diisi.',
            'kategori.required' => 'Kategori ikan wajib dipilih.',
            'satuan.required' => 'Satuan pengukuran wajib diisi.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        JenisIkan::create($validated);

        return redirect()->route('jenis-ikan.index')->with('success', 'Data jenis ikan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(JenisIkan $jenisIkan): RedirectResponse
    {
        $this->authorize('view', $jenisIkan);

        return redirect()->route('jenis-ikan.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JenisIkan $jenisIkan): View
    {
        $this->authorize('update', $jenisIkan);

        $categories = $this->categories;

        return view('master.jenis-ikan.edit', compact('jenisIkan', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JenisIkan $jenisIkan): RedirectResponse
    {
        $this->authorize('update', $jenisIkan);

        $validated = $request->validate([
            'kode_ikan' => ['required', 'string', 'max:20', Rule::unique('jenis_ikan', 'kode_ikan')->ignore($jenisIkan->id)],
            'nama_resmi' => ['required', 'string', 'max:100'],
            'nama_lokal' => ['nullable', 'string', 'max:100'],
            'kategori' => ['required', 'string', 'max:100'],
            'satuan' => ['required', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'kode_ikan.required' => 'Kode jenis ikan wajib diisi.',
            'kode_ikan.unique' => 'Kode jenis ikan sudah terdaftar.',
            'nama_resmi.required' => 'Nama resmi (komersial) wajib diisi.',
            'kategori.required' => 'Kategori ikan wajib dipilih.',
            'satuan.required' => 'Satuan pengukuran wajib diisi.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $jenisIkan->update($validated);

        return redirect()->route('jenis-ikan.index')->with('success', 'Data jenis ikan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JenisIkan $jenisIkan): RedirectResponse
    {
        $this->authorize('delete', $jenisIkan);

        if ($jenisIkan->detailPendaratan()->exists()) {
            return redirect()->route('jenis-ikan.index')->with('error', "Jenis ikan '{$jenisIkan->nama_resmi}' tidak dapat dihapus karena memiliki riwayat pencatatan pendaratan.");
        }

        $nama = $jenisIkan->nama_resmi;
        $jenisIkan->delete();

        return redirect()->route('jenis-ikan.index')->with('success', "Data jenis ikan '{$nama}' berhasil dihapus.");
    }
}
