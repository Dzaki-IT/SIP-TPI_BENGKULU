<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Kapal;
use App\Models\Nelayan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KapalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Kapal::class);

        $user = $request->user();
        $query = Kapal::with('nelayan.tpi')->withCount('pendaratan');

        if ($user?->isAdminTpi()) {
            $query->whereHas('nelayan', fn ($nelayanQuery) => $nelayanQuery->where('tpi_id', $user->tpi_id ?? 0));
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_kapal', 'like', "%{$search}%")
                    ->orWhere('kode_kapal', 'like', "%{$search}%")
                    ->orWhere('no_sipi', 'like', "%{$search}%")
                    ->orWhereHas('nelayan', function ($nq) use ($search) {
                        $nq->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('jenis_kapal')) {
            $query->where('jenis_kapal', $request->jenis_kapal);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif' ? 1 : 0);
        }

        $kapals = $query->orderBy('nama_kapal')->paginate(10)->withQueryString();

        return view('master.kapal.index', compact('kapals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Kapal::class);

        $user = request()->user();
        $nelayanList = Nelayan::aktif()
            ->when($user?->isAdminTpi(), fn ($query) => $query->where('tpi_id', $user->tpi_id))
            ->orderBy('nama')
            ->get();

        return view('master.kapal.create', compact('nelayanList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Kapal::class);

        $user = $request->user();
        $validated = $request->validate([
            'kode_kapal' => ['required', 'string', 'max:20', 'unique:kapal,kode_kapal'],
            'nama_kapal' => ['required', 'string', 'max:100'],
            'nelayan_id' => [Rule::requiredIf($user?->isAdminTpi()), 'nullable', 'exists:nelayan,id'],
            'jenis_kapal' => ['required', 'in:tanpa_motor,motor_tempel,kapal_motor'],
            'gt' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'no_sipi' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'kode_kapal.required' => 'Kode kapal wajib diisi.',
            'kode_kapal.unique' => 'Kode kapal sudah terdaftar.',
            'nama_kapal.required' => 'Nama kapal wajib diisi.',
            'jenis_kapal.required' => 'Jenis kapal wajib dipilih.',
            'jenis_kapal.in' => 'Pilihan jenis kapal tidak valid.',
            'gt.numeric' => 'Gross Tonnage (GT) harus berupa angka valid.',
            'nelayan_id.exists' => 'Pemilik kapal yang dipilih tidak valid.',
        ]);

        if ($user?->isAdminTpi() && ! Nelayan::whereKey($validated['nelayan_id'])->where('tpi_id', $user->tpi_id)->exists()) {
            abort(403, 'Kapal hanya dapat dikaitkan dengan nelayan pada TPI Anda.');
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        Kapal::create($validated);

        return redirect()->route('kapal.index')->with('success', 'Data kapal berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kapal $kapal): RedirectResponse
    {
        $this->authorize('view', $kapal);

        return redirect()->route('kapal.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kapal $kapal): View
    {
        $this->authorize('update', $kapal);

        $user = request()->user();
        $nelayanList = Nelayan::aktif()
            ->when($user?->isAdminTpi(), fn ($query) => $query->where('tpi_id', $user->tpi_id))
            ->orderBy('nama')
            ->get();

        return view('master.kapal.edit', compact('kapal', 'nelayanList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kapal $kapal): RedirectResponse
    {
        $this->authorize('update', $kapal);

        $user = $request->user();
        $validated = $request->validate([
            'kode_kapal' => ['required', 'string', 'max:20', Rule::unique('kapal', 'kode_kapal')->ignore($kapal->id)],
            'nama_kapal' => ['required', 'string', 'max:100'],
            'nelayan_id' => [Rule::requiredIf($user?->isAdminTpi()), 'nullable', 'exists:nelayan,id'],
            'jenis_kapal' => ['required', 'in:tanpa_motor,motor_tempel,kapal_motor'],
            'gt' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'no_sipi' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'kode_kapal.required' => 'Kode kapal wajib diisi.',
            'kode_kapal.unique' => 'Kode kapal sudah terdaftar.',
            'nama_kapal.required' => 'Nama kapal wajib diisi.',
            'jenis_kapal.required' => 'Jenis kapal wajib dipilih.',
            'jenis_kapal.in' => 'Pilihan jenis kapal tidak valid.',
            'gt.numeric' => 'Gross Tonnage (GT) harus berupa angka valid.',
            'nelayan_id.exists' => 'Pemilik kapal yang dipilih tidak valid.',
        ]);

        if ($user?->isAdminTpi() && ! Nelayan::whereKey($validated['nelayan_id'])->where('tpi_id', $user->tpi_id)->exists()) {
            abort(403, 'Kapal hanya dapat dikaitkan dengan nelayan pada TPI Anda.');
        }

        $validated['is_active'] = $request->boolean('is_active');

        $kapal->update($validated);

        return redirect()->route('kapal.index')->with('success', 'Data kapal berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kapal $kapal): RedirectResponse
    {
        $this->authorize('delete', $kapal);

        if ($kapal->pendaratan()->exists()) {
            return redirect()->route('kapal.index')->with('error', "Kapal '{$kapal->nama_kapal}' tidak dapat dihapus karena memiliki riwayat transaksi pendaratan.");
        }

        $nama = $kapal->nama_kapal;
        $kapal->delete();

        return redirect()->route('kapal.index')->with('success', "Data kapal '{$nama}' berhasil dihapus.");
    }
}
