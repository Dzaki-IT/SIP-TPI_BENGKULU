@extends('layouts.app')

@section('title', 'Data Jenis Ikan')

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header & Breadcrumbs -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Master Data</li>
                    <li class="breadcrumb-item active" aria-current="page">Jenis Ikan</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-fingerprint text-primary"></i> Master Komoditas Ikan & Hasil Laut
            </h4>
        </div>
        @can('create', \App\Models\JenisIkan::class)
        <div>
            <a href="{{ route('jenis-ikan.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center gap-2 px-3 py-2">
                <i class="bi bi-plus-lg"></i>
                <span class="fw-semibold">Tambah Jenis Ikan</span>
            </a>
        </div>
        @endcan
    </div>

    <!-- Filter & Search Card -->
    <div class="card card-custom mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('jenis-ikan.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama resmi, nama ilmiah/lokal, atau kode..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="kategori" class="form-select">
                        <option value="">-- Semua Kategori Komoditas --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('kategori') === $cat ? 'selected' : '' }}>
                                {{ $cat }}
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
                    @if(request()->hasAny(['search', 'kategori', 'status']))
                        <a href="{{ route('jenis-ikan.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
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
                <i class="bi bi-list-stars text-primary"></i> Daftar Komoditas Hasil Tangkapan TPI
            </h6>
            <span class="badge bg-light text-secondary border px-2.5 py-1.5">
                Total: {{ $jenisIkans->total() }} Komoditas
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th style="width: 120px;">Kode Ikan</th>
                        <th>Nama Resmi (Komersial)</th>
                        <th>Nama Ilmiah / Lokal</th>
                        <th>Kategori</th>
                        <th class="text-center" style="width: 100px;">Satuan</th>
                        <th class="text-center" style="width: 140px;">Pencatatan</th>
                        <th class="text-center" style="width: 110px;">Status</th>
                        @can('create', \App\Models\JenisIkan::class)
                        <th class="text-center" style="width: 140px;">Aksi</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse($jenisIkans as $index => $ikan)
                        <tr>
                            <td class="text-center text-muted fw-semibold">
                                {{ $jenisIkans->firstItem() + $index }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace fw-bold">
                                    {{ $ikan->kode_ikan }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $ikan->nama_resmi }}</div>
                            </td>
                            <td>
                                @if($ikan->nama_lokal)
                                    <span class="fst-italic text-secondary small">{{ $ikan->nama_lokal }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                @if($ikan->kategori === 'Pelagis Besar')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        {{ $ikan->kategori }}
                                    </span>
                                @elseif($ikan->kategori === 'Pelagis Kecil')
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                                        {{ $ikan->kategori }}
                                    </span>
                                @elseif($ikan->kategori === 'Demersal')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                        {{ $ikan->kategori }}
                                    </span>
                                @elseif($ikan->kategori === 'Non-Ikan')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        {{ $ikan->kategori }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border">
                                        {{ $ikan->kategori }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    {{ $ikan->satuan }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary-emphasis" title="Frekuensi pencatatan pendaratan">
                                    <i class="bi bi-receipt me-1"></i>{{ $ikan->detail_pendaratan_count }}x Catatan
                                </span>
                            </td>
                            <td class="text-center">
                                @if($ikan->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1">
                                        <i class="bi bi-check-circle me-1"></i>Aktif
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border px-2.5 py-1">
                                        <i class="bi bi-dash-circle me-1"></i>Nonaktif
                                    </span>
                                @endif
                            </td>
                            @can('create', \App\Models\JenisIkan::class)
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    @can('update', $ikan)
                                    <a href="{{ route('jenis-ikan.edit', $ikan) }}" class="btn btn-sm btn-outline-warning text-dark" title="Edit Data">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    @endcan
                                    @can('delete', $ikan)
                                    <form action="{{ route('jenis-ikan.destroy', $ikan) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus komoditas ikan \'{{ $ikan->nama_resmi }}\'? Data yang memiliki riwayat pencatatan pendaratan tidak dapat dihapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Data">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()?->can('create', \App\Models\JenisIkan::class) ? 9 : 8 }}" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-fingerprint display-6 d-block mb-2"></i>
                                    <span class="fw-semibold">Tidak ada komoditas ikan ditemukan.</span>
                                    <p class="small text-secondary mb-0">Silakan tambahkan jenis ikan baru atau atur ulang kata kunci pencarian.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jenisIkans->hasPages())
            <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    Menampilkan {{ $jenisIkans->firstItem() }} sampai {{ $jenisIkans->lastItem() }} dari {{ $jenisIkans->total() }} data
                </div>
                <div>
                    {{ $jenisIkans->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
