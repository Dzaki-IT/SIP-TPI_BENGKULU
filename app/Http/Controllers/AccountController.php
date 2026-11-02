<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Tpi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $accounts = User::with('tpi')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = trim($request->string('search')->toString());

                $query->where(function ($accountQuery) use ($search): void {
                    $accountQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->string('role')->toString()))
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('akun.index', compact('accounts'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        $tpiList = Tpi::aktif()->orderBy('nama_tpi')->get();

        return view('akun.create', compact('tpiList'));
    }

    public function store(StoreAccountRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validated();
        $validated['tpi_id'] = $validated['role'] === 'admin_tpi' ? $validated['tpi_id'] : null;

        unset($validated['password_confirmation']);

        User::create($validated);

        return redirect()->route('akun.index')->with('success', 'Akun baru berhasil dibuat dan dapat langsung digunakan untuk login.');
    }

    public function show(User $akun): RedirectResponse
    {
        $this->authorize('view', $akun);

        return redirect()->route('akun.edit', $akun);
    }

    public function edit(User $akun): View
    {
        $this->authorize('update', $akun);

        $tpiList = Tpi::aktif()->orderBy('nama_tpi')->get();

        return view('akun.edit', compact('akun', 'tpiList'));
    }

    public function update(UpdateAccountRequest $request, User $akun): RedirectResponse
    {
        $this->authorize('update', $akun);

        $validated = $request->validated();

        if ($this->wouldRemoveLastActivePimpinan($akun, $validated)) {
            return back()->withInput()->with('error', 'Akun Pimpinan aktif terakhir tidak dapat dinonaktifkan atau diubah role-nya. Buat akun Pimpinan aktif lain terlebih dahulu.');
        }

        if ($request->user()->is($akun) && ($validated['role'] !== 'pimpinan' || ! $validated['is_active'])) {
            return back()->withInput()->with('error', 'Anda tidak dapat menonaktifkan atau mengubah role akun Pimpinan yang sedang digunakan.');
        }

        $accountData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'tpi_id' => $validated['role'] === 'admin_tpi' ? $validated['tpi_id'] : null,
            'is_active' => $validated['is_active'],
        ];

        if (! empty($validated['password'])) {
            $accountData['password'] = $validated['password'];
        }

        $akun->update($accountData);

        return redirect()->route('akun.index')->with('success', 'Data akun berhasil diperbarui.');
    }

    /**
     * @param  array{role: string, is_active: bool}  $accountData
     */
    private function wouldRemoveLastActivePimpinan(User $akun, array $accountData): bool
    {
        return $akun->isPimpinan()
            && $akun->is_active
            && ($accountData['role'] !== 'pimpinan' || ! $accountData['is_active'])
            && User::where('role', 'pimpinan')->where('is_active', true)->count() === 1;
    }
}
