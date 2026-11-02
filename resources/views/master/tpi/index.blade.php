@extends('layouts.app')

@section('title', 'Data TPI')

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header & Breadcrumbs -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Master Data</li>
                    <li class="breadcrumb-item active" aria-current="page">Data TPI</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-building text-primary"></i> Data Tempat Pelelangan Ikan (TPI)
            </h4>
        </div>
        @can('create', \App\Models\Tpi::class)
        <div>
            <a href="{{ route('tpi.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center gap-2 px-3 py-2">
                <i class="bi bi-plus-lg"></i>
                <span class="fw-semibold">Tambah TPI Baru</span>
            </a>
        </div>
        @endcan
    </div>

    <!-- Filter & Search Card -->
    <div class="card card-custom mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('tpi.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama, kode, atau pengelola..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="wilayah_id" class="form-select">
                        <option value="">-- Semua Kabupaten/Kota --</option>
                        @foreach($wilayahList as $wil)
                            <option value="{{ $wil->id }}" {{ request('wilayah_id') == $wil->id ? 'selected' : '' }}>
                                {{ $wil->nama_kabupaten_kota }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status_operasi" class="form-select">
                        <option value="">-- Semua Status Operasi --</option>
                        <option value="aktif" {{ request('status_operasi') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="tidak_aktif" {{ request('status_operasi') === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-dark w-100 fw-semibold">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'wilayah_id', 'status_operasi']))
                        <a href="{{ route('tpi.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
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
                <i class="bi bi-list-ul text-primary"></i> Daftar TPI Provinsi Bengkulu
            </h6>
            <span class="badge bg-light text-secondary border px-2.5 py-1.5">
                Total: {{ $tpis->total() }} TPI
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th style="width: 120px;">Kode TPI</th>
                        <th>Nama TPI</th>
                        <th>Wilayah Kab/Kota</th>
                        <th>Instansi Pengelola</th>
                        <th class="text-center" style="width: 140px;">Status</th>
                        <th class="text-center" style="width: 130px;">Statistik</th>
                        @can('create', \App\Models\Tpi::class)
                        <th class="text-center" style="width: 150px;">Aksi</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse($tpis as $index => $tpi)
                        <tr>
                            <td class="text-center text-muted fw-semibold">
                                {{ $tpis->firstItem() + $index }}
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace fw-bold">
                                    {{ $tpi->kode_tpi }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $tpi->nama_tpi }}</div>
                                @if($tpi->alamat)
                                    <small class="text-muted d-block text-truncate" style="max-width: 250px;">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $tpi->alamat }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $tpi->wilayah->nama_kabupaten_kota ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="text-secondary small">
                                    {{ $tpi->instansi_pengelola ?? '-' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($tpi->status_operasi === 'aktif')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1">
                                        <i class="bi bi-check-circle me-1"></i>Aktif
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1">
                                        <i class="bi bi-x-circle me-1"></i>Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2 small">
                                    <span class="badge bg-info-subtle text-info-emphasis" title="Jumlah Nelayan Terdaftar">
                                        <i class="bi bi-people me-1"></i>{{ $tpi->nelayan_count }}
                                    </span>
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis" title="Jumlah Pendaratan">
                                        <i class="bi bi-journal-check me-1"></i>{{ $tpi->pendaratan_count }}
                                    </span>
                                </div>
                            </td>
                            @can('create', \App\Models\Tpi::class)
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    @can('update', $tpi)
                                    <a href="{{ route('tpi.edit', $tpi) }}" class="btn btn-sm btn-outline-warning text-dark" title="Edit Data">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    @endcan
                                    @can('delete', $tpi)
                                    <form action="{{ route('tpi.destroy', $tpi) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus TPI \'{{ $tpi->nama_tpi }}\'? Tindakan ini tidak dapat dibatalkan.');">
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
                            <td colspan="{{ auth()->user()?->can('create', \App\Models\Tpi::class) ? 8 : 7 }}" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-building-slash display-6 d-block mb-2"></i>
                                    <span class="fw-semibold">Tidak ada data TPI ditemukan.</span>
                                    <p class="small text-secondary mb-0">
                                        @can('create', \App\Models\Tpi::class)
                                            Coba ubah kata kunci pencarian atau tambahkan data TPI baru.
                                        @else
                                            Coba ubah kata kunci pencarian atau filter yang digunakan.
                                        @endcan
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tpis->hasPages())
            <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    Menampilkan {{ $tpis->firstItem() }} sampai {{ $tpis->lastItem() }} dari {{ $tpis->total() }} data
                </div>
                <div>
                    {{ $tpis->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
