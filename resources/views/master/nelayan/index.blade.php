@extends('layouts.app')

@section('title', 'Data Nelayan')

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header & Breadcrumbs -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Master Data</li>
                    <li class="breadcrumb-item active" aria-current="page">Data Nelayan</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-people-fill text-primary"></i> Data Nelayan Terdaftar
            </h4>
        </div>
        <div>
            <a href="{{ route('nelayan.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center gap-2 px-3 py-2">
                <i class="bi bi-person-plus-fill"></i>
                <span class="fw-semibold">Tambah Nelayan Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="card card-custom mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('nelayan.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama, NIK, kode, kelompok..." value="{{ request('search') }}">
                    </div>
                </div>

                @if(!auth()->user()?->tpi_id)
                <div class="col-md-3">
                    <select name="tpi_id" class="form-select">
                        <option value="">-- Semua Pangkalan TPI --</option>
                        @foreach($tpiList as $tpi)
                            <option value="{{ $tpi->id }}" {{ request('tpi_id') == $tpi->id ? 'selected' : '' }}>
                                {{ $tpi->nama_tpi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @else
                <div class="col-md-3">
                    <input type="text" class="form-control bg-light text-muted" value="{{ auth()->user()->tpi->nama_tpi ?? 'TPI Ditugaskan' }}" readonly disabled>
                </div>
                @endif

                <div class="col-md-3">
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
                    @if(request()->hasAny(['search', 'tpi_id', 'status']))
                        <a href="{{ route('nelayan.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
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
                <i class="bi bi-person-lines-fill text-primary"></i> Daftar Nelayan
                @if(auth()->user()?->role === 'admin_tpi' && auth()->user()->tpi)
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1">
                        {{ auth()->user()->tpi->nama_tpi }}
                    </span>
                @endif
            </h6>
            <span class="badge bg-light text-secondary border px-2.5 py-1.5">
                Total: {{ $nelayans->total() }} Nelayan
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th style="width: 120px;">Kode</th>
                        <th>Nama Nelayan & NIK</th>
                        <th>Pangkalan TPI</th>
                        <th>Kelompok Nelayan (KUB)</th>
                        <th class="text-center" style="width: 110px;">Status</th>
                        <th class="text-center" style="width: 140px;">Kepemilikan</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nelayans as $index => $nelayan)
                        <tr>
                            <td class="text-center text-muted fw-semibold">
                                {{ $nelayans->firstItem() + $index }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace fw-bold">
                                    {{ $nelayan->kode_nelayan }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $nelayan->nama }}</div>
                                <div class="text-muted small">
                                    <i class="bi bi-card-heading me-1"></i>{{ $nelayan->nik ?: 'NIK belum diisi' }}
                                </div>
                                @if($nelayan->alamat)
                                    <small class="text-secondary d-block text-truncate mt-0.5" style="max-width: 250px;">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $nelayan->alamat }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($nelayan->tpi)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        {{ $nelayan->tpi->nama_tpi }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-secondary fw-semibold small">
                                    {{ $nelayan->kelompok_nelayan ?: '-' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($nelayan->is_active)
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
                                <div class="d-flex justify-content-center gap-2 small">
                                    <span class="badge bg-info-subtle text-info-emphasis" title="Jumlah Kapal Dimiliki">
                                        <i class="bi bi-tsunami me-1"></i>{{ $nelayan->kapal_count }} Kapal
                                    </span>
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis" title="Riwayat Pendaratan">
                                        <i class="bi bi-journal-check me-1"></i>{{ $nelayan->pendaratan_count }} PDR
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('nelayan.edit', $nelayan) }}" class="btn btn-sm btn-outline-warning text-dark" title="Edit Data">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('nelayan.destroy', $nelayan) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus nelayan \'{{ $nelayan->nama }}\'? Data yang memiliki riwayat transaksi pendaratan atau kepemilikan kapal tidak dapat dihapus.');">
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
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-people display-6 d-block mb-2"></i>
                                    <span class="fw-semibold">Tidak ada data nelayan ditemukan.</span>
                                    <p class="small text-secondary mb-0">Silakan tambahkan nelayan baru atau ubah kriteria filter.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($nelayans->hasPages())
            <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    Menampilkan {{ $nelayans->firstItem() }} sampai {{ $nelayans->lastItem() }} dari {{ $nelayans->total() }} data
                </div>
                <div>
                    {{ $nelayans->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
