@extends('layouts.app')

@section('title', 'Data Alat Tangkap')

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header & Breadcrumbs -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Master Data</li>
                    <li class="breadcrumb-item active" aria-current="page">Alat Tangkap</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-tools text-primary"></i> Data Alat Penangkapan Ikan (API)
            </h4>
        </div>
        <div>
            <a href="{{ route('alat-tangkap.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center gap-2 px-3 py-2">
                <i class="bi bi-plus-lg"></i>
                <span class="fw-semibold">Tambah Alat Tangkap</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="card card-custom mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('alat-tangkap.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama atau kode alat tangkap..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="jenis_kategori" class="form-select">
                        <option value="">-- Semua Kategori API --</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat }}" {{ request('jenis_kategori') === $kat ? 'selected' : '' }}>
                                {{ $kat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">-- Semua Status --</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-dark w-100 fw-semibold">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'jenis_kategori', 'status']))
                        <a href="{{ route('alat-tangkap.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card card-custom">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-gear-wide-connected text-primary"></i> Klasifikasi Alat Penangkapan Ikan
            </h6>
            <span class="badge bg-light text-secondary border px-2.5 py-1.5">
                Total: {{ $alatTangkaps->total() }} Alat Tangkap
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th style="width: 140px;">Kode Alat</th>
                        <th>Nama Alat Penangkapan Ikan</th>
                        <th>Kategori / Golongan</th>
                        <th class="text-center" style="width: 150px;">Frekuensi Digunakan</th>
                        <th class="text-center" style="width: 120px;">Status</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alatTangkaps as $index => $alat)
                        <tr>
                            <td class="text-center text-muted fw-semibold">
                                {{ $alatTangkaps->firstItem() + $index }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace fw-bold">
                                    {{ $alat->kode_alat }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $alat->nama_alat }}</div>
                            </td>
                            <td>
                                @if($alat->jenis_kategori)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        {{ $alat->jenis_kategori }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary-emphasis" title="Digunakan dalam pendaratan">
                                    <i class="bi bi-journal-check me-1"></i>{{ $alat->pendaratan_count }}x Pendaratan
                                </span>
                            </td>
                            <td class="text-center">
                                @if($alat->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1">
                                        <i class="bi bi-check-circle me-1"></i>Aktif
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border px-2.5 py-1">
                                        <i class="bi bi-dash-circle me-1"></i>Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('alat-tangkap.edit', $alat) }}" class="btn btn-sm btn-outline-warning text-dark" title="Edit Data">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('alat-tangkap.destroy', $alat) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus alat tangkap \'{{ $alat->nama_alat }}\'? Data yang sedang digunakan dalam pendaratan tidak dapat dihapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Data">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-tools display-6 d-block mb-2"></i>
                                    <span class="fw-semibold">Tidak ada data alat tangkap ditemukan.</span>
                                    <p class="small text-secondary mb-0">Silakan tambahkan data alat tangkap baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($alatTangkaps->hasPages())
            <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    Menampilkan {{ $alatTangkaps->firstItem() }} sampai {{ $alatTangkaps->lastItem() }} dari {{ $alatTangkaps->total() }} data
                </div>
                <div>
                    {{ $alatTangkaps->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
