<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Nelayan;
use App\Models\Tpi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NelayanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Nelayan::class);

        $user = Auth::user();
        $query = Nelayan::with('tpi')->withCount(['kapal', 'alatTangkap', 'pendaratan']);

        // Admin TPI can only view data from their assigned TPI.
        if ($user?->isAdminTpi()) {
            $query->where('tpi_id', $user->tpi_id ?? 0);
        } elseif ($request->filled('tpi_id')) {
            $query->where('tpi_id', $request->tpi_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode_nelayan', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('kelompok_nelayan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif' ? 1 : 0);
        }

        $nelayans = $query->orderBy('nama')->paginate(10)->withQueryString();
        $tpiList = Tpi::aktif()->orderBy('nama_tpi')->get();

        return view('master.nelayan.index', compact('nelayans', 'tpiList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Nelayan::class);

        $user = Auth::user();
        $tpiList = Tpi::aktif()
            ->when($user?->isAdminTpi(), fn ($query) => $query->whereKey($user->tpi_id))
            ->orderBy('nama_tpi')
            ->get();
        $defaultTpiId = $user?->isAdminTpi() ? $user->tpi_id : null;

        return view('master.nelayan.create', compact('tpiList', 'defaultTpiId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Nelayan::class);

        $user = Auth::user();

        if ($user?->isAdminTpi()) {
            $request->merge(['tpi_id' => $user->tpi_id]);
        }

        $validated = $request->validate([
            'kode_nelayan' => ['required', 'string', 'max:20', 'unique:nelayan,kode_nelayan'],
            'nama' => ['required', 'string', 'max:100'],
            'nik' => ['nullable', 'string', 'size:16', 'unique:nelayan,nik'],
            'alamat' => ['nullable', 'string'],
            'kelompok_nelayan' => ['nullable', 'string', 'max:100'],
            'tpi_id' => ['nullable', 'exists:tpi,id'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'kode_nelayan.required' => 'Kode nelayan wajib diisi.',
            'kode_nelayan.unique' => 'Kode nelayan sudah terdaftar.',
            'nama.required' => 'Nama lengkap nelayan wajib diisi.',
            'nik.size' => 'NIK harus berjumlah 16 digit angka.',
            'nik.unique' => 'NIK sudah terdaftar pada nelayan lain.',
            'tpi_id.exists' => 'Pangkalan TPI yang dipilih tidak valid.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Nelayan::create($validated);

        return redirect()->route('nelayan.index')->with('success', 'Data nelayan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Nelayan $nelayan): RedirectResponse
    {
        $this->authorize('view', $nelayan);

        return redirect()->route('nelayan.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Nelayan $nelayan): View
    {
        $this->authorize('update', $nelayan);

        $user = Auth::user();
        $tpiList = Tpi::aktif()
            ->when($user?->isAdminTpi(), fn ($query) => $query->whereKey($user->tpi_id))
            ->orderBy('nama_tpi')
            ->get();

        return view('master.nelayan.edit', compact('nelayan', 'tpiList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Nelayan $nelayan): RedirectResponse
    {
        $this->authorize('update', $nelayan);

        $user = Auth::user();
        if ($user?->isAdminTpi()) {
            $request->merge(['tpi_id' => $user->tpi_id]);
        }

        $validated = $request->validate([
            'kode_nelayan' => ['required', 'string', 'max:20', Rule::unique('nelayan', 'kode_nelayan')->ignore($nelayan->id)],
            'nama' => ['required', 'string', 'max:100'],
            'nik' => ['nullable', 'string', 'size:16', Rule::unique('nelayan', 'nik')->ignore($nelayan->id)],
            'alamat' => ['nullable', 'string'],
            'kelompok_nelayan' => ['nullable', 'string', 'max:100'],
            'tpi_id' => ['nullable', 'exists:tpi,id'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'kode_nelayan.required' => 'Kode nelayan wajib diisi.',
            'kode_nelayan.unique' => 'Kode nelayan sudah terdaftar.',
            'nama.required' => 'Nama lengkap nelayan wajib diisi.',
            'nik.size' => 'NIK harus berjumlah 16 digit angka.',
            'nik.unique' => 'NIK sudah terdaftar pada nelayan lain.',
            'tpi_id.exists' => 'Pangkalan TPI yang dipilih tidak valid.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $nelayan->update($validated);

        return redirect()->route('nelayan.index')->with('success', 'Data nelayan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nelayan $nelayan): RedirectResponse
    {
        $this->authorize('delete', $nelayan);

        if ($nelayan->pendaratan()->exists()) {
            return redirect()->route('nelayan.index')->with('error', "Nelayan '{$nelayan->nama}' tidak dapat dihapus karena memiliki riwayat pendaratan aktif.");
        }

        if ($nelayan->kapal()->exists()) {
            return redirect()->route('nelayan.index')->with('error', "Nelayan '{$nelayan->nama}' tidak dapat dihapus karena masih terdaftar sebagai pemilik kapal.");
        }

        if ($nelayan->alatTangkap()->exists()) {
            return redirect()->route('nelayan.index')->with('error', "Nelayan '{$nelayan->nama}' tidak dapat dihapus karena masih terdaftar sebagai pemilik alat tangkap.");
        }

        $nama = $nelayan->nama;
        $nelayan->delete();

        return redirect()->route('nelayan.index')->with('success', "Data nelayan '{$nama}' berhasil dihapus.");
    }
}
