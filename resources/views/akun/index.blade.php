@extends('layouts.app')

@section('title', 'Manajemen Akun')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Manajemen Akun</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-person-gear text-primary"></i> Manajemen Akun
            </h4>
            <p class="text-muted mb-0 mt-1 small">Tambah, perbarui, aktifkan, atau nonaktifkan akun pengguna sistem.</p>
        </div>
        <a href="{{ route('akun.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center gap-2 px-3 py-2">
            <i class="bi bi-person-plus-fill"></i>
            <span class="fw-semibold">Tambah Akun</span>
        </a>
    </div>

    <div class="card card-custom mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('akun.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="search" name="search" class="form-control border-start-0" placeholder="Cari nama atau email akun..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">-- Semua Role --</option>
                        <option value="admin_tpi" @selected(request('role') === 'admin_tpi')>Admin TPI</option>
                        <option value="verifikator" @selected(request('role') === 'verifikator')>Verifikator</option>
                        <option value="pimpinan" @selected(request('role') === 'pimpinan')>Pimpinan</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-dark px-4 fw-semibold">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'role']))
                        <a href="{{ route('akun.index') }}" class="btn btn-outline-secondary" title="Reset filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card card-custom">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-people-fill text-primary me-1"></i> Daftar Akun Pengguna</h6>
            <span class="badge bg-light text-secondary border px-2.5 py-1.5">Total: {{ $accounts->total() }} Akun</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">No</th>
                        <th>Pengguna</th>
                        <th style="width: 180px;">Role</th>
                        <th>Pangkalan TPI</th>
                        <th class="text-center" style="width: 110px;">Status</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $index => $account)
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $accounts->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $account->name }}</div>
                                <div class="text-muted small"><i class="bi bi-envelope me-1"></i>{{ $account->email }}</div>
                            </td>
                            <td>
                                @php
                                    $roleBadgeClass = match ($account->role) {
                                        'admin_tpi' => 'badge-role-admin',
                                        'verifikator' => 'badge-role-verifikator',
                                        default => 'badge-role-pimpinan',
                                    };
                                @endphp
                                <span class="badge {{ $roleBadgeClass }} px-2 py-1">{{ $account->role_label }}</span>
                            </td>
                            <td>
                                @if($account->isAdminTpi() && $account->tpi)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $account->tpi->nama_tpi }}</span>
                                @elseif($account->isAdminTpi())
                                    <span class="text-danger small"><i class="bi bi-exclamation-triangle me-1"></i>Belum ditugaskan</span>
                                @else
                                    <span class="text-muted small">Tidak diperlukan</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($account->is_active)
                                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">Aktif</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis border">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('akun.edit', $account) }}" class="btn btn-sm btn-outline-warning text-dark" title="Edit akun">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-person-x display-6 d-block mb-2"></i>
                                    <span class="fw-semibold">Belum ada akun yang sesuai.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($accounts->hasPages())
            <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">Menampilkan {{ $accounts->firstItem() }} sampai {{ $accounts->lastItem() }} dari {{ $accounts->total() }} akun</div>
                <div>{{ $accounts->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
