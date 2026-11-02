<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\AlatTangkap;
use App\Models\Nelayan;
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
        $this->authorize('viewAny', AlatTangkap::class);

        $user = $request->user();
        $query = AlatTangkap::with('nelayan')->withCount('pendaratan');

        if ($user?->isAdminTpi()) {
            $query->whereHas('nelayan', fn ($nelayanQuery) => $nelayanQuery->where('tpi_id', $user->tpi_id ?? 0));
        }

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
        $this->authorize('create', AlatTangkap::class);

        $existingCategories = AlatTangkap::select('jenis_kategori')
            ->whereNotNull('jenis_kategori')
            ->distinct()
            ->pluck('jenis_kategori');

        $user = request()->user();
        $nelayanList = Nelayan::with('tpi')
            ->aktif()
            ->when($user?->isAdminTpi(), fn ($query) => $query->where('tpi_id', $user->tpi_id))
            ->orderBy('nama')
            ->get();

        return view('master.alat-tangkap.create', compact('existingCategories', 'nelayanList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', AlatTangkap::class);

        $user = $request->user();
        $validated = $request->validate([
            'kode_alat' => ['required', 'string', 'max:20', 'unique:alat_tangkap,kode_alat'],
            'nama_alat' => ['required', 'string', 'max:100'],
            'jenis_kategori' => ['nullable', 'string', 'max:100'],
            'nelayan_id' => [Rule::requiredIf($user?->isAdminTpi()), 'nullable', 'exists:nelayan,id'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'kode_alat.required' => 'Kode alat tangkap wajib diisi.',
            'kode_alat.unique' => 'Kode alat tangkap sudah terdaftar.',
            'nama_alat.required' => 'Nama alat tangkap wajib diisi.',
            'nelayan_id.exists' => 'Nelayan pemilik yang dipilih tidak valid.',
        ]);

        if ($user?->isAdminTpi() && ! Nelayan::whereKey($validated['nelayan_id'])->where('tpi_id', $user->tpi_id)->exists()) {
            abort(403, 'Alat tangkap hanya dapat dikaitkan dengan nelayan pada TPI Anda.');
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        AlatTangkap::create($validated);

        return redirect()->route('alat-tangkap.index')->with('success', 'Data alat tangkap berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AlatTangkap $alatTangkap): RedirectResponse
    {
        $this->authorize('view', $alatTangkap);

        return redirect()->route('alat-tangkap.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AlatTangkap $alatTangkap): View
    {
        $this->authorize('update', $alatTangkap);

        $existingCategories = AlatTangkap::select('jenis_kategori')
            ->whereNotNull('jenis_kategori')
            ->distinct()
            ->pluck('jenis_kategori');

        $user = request()->user();
        $nelayanList = Nelayan::with('tpi')
            ->aktif()
            ->when($user?->isAdminTpi(), fn ($query) => $query->where('tpi_id', $user->tpi_id))
            ->orderBy('nama')
            ->get();

        return view('master.alat-tangkap.edit', compact('alatTangkap', 'existingCategories', 'nelayanList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AlatTangkap $alatTangkap): RedirectResponse
    {
        $this->authorize('update', $alatTangkap);

        $user = $request->user();
        $validated = $request->validate([
            'kode_alat' => ['required', 'string', 'max:20', Rule::unique('alat_tangkap', 'kode_alat')->ignore($alatTangkap->id)],
            'nama_alat' => ['required', 'string', 'max:100'],
            'jenis_kategori' => ['nullable', 'string', 'max:100'],
            'nelayan_id' => [Rule::requiredIf($user?->isAdminTpi()), 'nullable', 'exists:nelayan,id'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'kode_alat.required' => 'Kode alat tangkap wajib diisi.',
            'kode_alat.unique' => 'Kode alat tangkap sudah terdaftar.',
            'nama_alat.required' => 'Nama alat tangkap wajib diisi.',
            'nelayan_id.exists' => 'Nelayan pemilik yang dipilih tidak valid.',
        ]);

        if ($user?->isAdminTpi() && ! Nelayan::whereKey($validated['nelayan_id'])->where('tpi_id', $user->tpi_id)->exists()) {
            abort(403, 'Alat tangkap hanya dapat dikaitkan dengan nelayan pada TPI Anda.');
        }

        $validated['is_active'] = $request->boolean('is_active');

        $alatTangkap->update($validated);

        return redirect()->route('alat-tangkap.index')->with('success', 'Data alat tangkap berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AlatTangkap $alatTangkap): RedirectResponse
    {
        $this->authorize('delete', $alatTangkap);

        if ($alatTangkap->pendaratan()->exists()) {
            return redirect()->route('alat-tangkap.index')->with('error', "Alat tangkap '{$alatTangkap->nama_alat}' tidak dapat dihapus karena digunakan dalam data pendaratan.");
        }

        $nama = $alatTangkap->nama_alat;
        $alatTangkap->delete();

        return redirect()->route('alat-tangkap.index')->with('success', "Data alat tangkap '{$nama}' berhasil dihapus.");
    }
}
