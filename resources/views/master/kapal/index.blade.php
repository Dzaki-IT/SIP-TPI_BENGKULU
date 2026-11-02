@extends('layouts.app')

@section('title', 'Data Kapal')

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header & Breadcrumbs -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Master Data</li>
                    <li class="breadcrumb-item active" aria-current="page">Data Kapal</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-tsunami text-primary"></i> Data Armada Kapal & Perahu
            </h4>
        </div>
        @can('create', \App\Models\Kapal::class)
        <div>
            <a href="{{ route('kapal.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center gap-2 px-3 py-2">
                <i class="bi bi-plus-lg"></i>
                <span class="fw-semibold">Tambah Kapal Baru</span>
            </a>
        </div>
        @endcan
    </div>

    <!-- Filter & Search Card -->
    <div class="card card-custom mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('kapal.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama kapal, kode, SIPI, atau pemilik..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="jenis_kapal" class="form-select">
                        <option value="">-- Semua Jenis Kapal --</option>
                        <option value="tanpa_motor" {{ request('jenis_kapal') === 'tanpa_motor' ? 'selected' : '' }}>Perahu Tanpa Motor</option>
                        <option value="motor_tempel" {{ request('jenis_kapal') === 'motor_tempel' ? 'selected' : '' }}>Perahu Motor Tempel</option>
                        <option value="kapal_motor" {{ request('jenis_kapal') === 'kapal_motor' ? 'selected' : '' }}>Kapal Motor (Inboard)</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- Semua Status --</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif Beroperasi</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif / Rusak</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-dark w-100 fw-semibold">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'jenis_kapal', 'status']))
                        <a href="{{ route('kapal.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
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
                <i class="bi bi-list-columns-reverse text-primary"></i> Daftar Armada Kapal Penangkap Ikan
            </h6>
            <span class="badge bg-light text-secondary border px-2.5 py-1.5">
                Total: {{ $kapals->total() }} Kapal
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th style="width: 120px;">Kode Kapal</th>
                        <th>Nama Kapal</th>
                        <th>Jenis Kapal</th>
                        <th>Kapasitas (GT)</th>
                        <th>Pemilik / Nakhoda</th>
                        <th>No. SIPI</th>
                        <th class="text-center" style="width: 110px;">Status</th>
                        @can('create', \App\Models\Kapal::class)
                        <th class="text-center" style="width: 140px;">Aksi</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse($kapals as $index => $kapal)
                        <tr>
                            <td class="text-center text-muted fw-semibold">
                                {{ $kapals->firstItem() + $index }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace fw-bold">
                                    {{ $kapal->kode_kapal }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $kapal->nama_kapal }}</div>
                                <small class="text-muted">
                                    <i class="bi bi-journal-check me-1"></i>{{ $kapal->pendaratan_count }}x Pendaratan
                                </small>
                            </td>
                            <td>
                                @if($kapal->jenis_kapal === 'tanpa_motor')
                                    <span class="badge bg-secondary-subtle text-secondary border">
                                        Tanpa Motor
                                    </span>
                                @elseif($kapal->jenis_kapal === 'motor_tempel')
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                                        Motor Tempel
                                    </span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        Kapal Motor
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-semibold font-monospace">
                                    {{ $kapal->gt !== null ? number_format($kapal->gt, 2) . ' GT' : '-' }}
                                </span>
                            </td>
                            <td>
                                @if($kapal->nelayan)
                                    <div class="fw-semibold text-dark">{{ $kapal->nelayan->nama }}</div>
                                    @if($kapal->nelayan->tpi)
                                        <small class="text-muted d-block">
                                            <i class="bi bi-geo-alt me-1"></i>{{ $kapal->nelayan->tpi->nama_tpi }}
                                        </small>
                                    @endif
                                @else
                                    <span class="text-muted small">Belum terhubung</span>
                                @endif
                            </td>
                            <td>
                                @if($kapal->no_sipi)
                                    <span class="font-monospace small text-dark fw-semibold">
                                        {{ $kapal->no_sipi }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($kapal->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1">
                                        <i class="bi bi-check-circle me-1"></i>Aktif
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border px-2.5 py-1">
                                        <i class="bi bi-dash-circle me-1"></i>Nonaktif
                                    </span>
                                @endif
                            </td>
                            @can('create', \App\Models\Kapal::class)
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    @can('update', $kapal)
                                    <a href="{{ route('kapal.edit', $kapal) }}" class="btn btn-sm btn-outline-warning text-dark" title="Edit Data">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    @endcan
                                    @can('delete', $kapal)
                                    <form action="{{ route('kapal.destroy', $kapal) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kapal \'{{ $kapal->nama_kapal }}\'? Data yang memiliki riwayat pendaratan tidak dapat dihapus.');">
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
                            <td colspan="{{ auth()->user()?->can('create', \App\Models\Kapal::class) ? 9 : 8 }}" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-tsunami display-6 d-block mb-2"></i>
                                    <span class="fw-semibold">Tidak ada data kapal ditemukan.</span>
                                    <p class="small text-secondary mb-0">Silakan tambahkan armada kapal baru atau sesuaikan filter pencarian.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kapals->hasPages())
            <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    Menampilkan {{ $kapals->firstItem() }} sampai {{ $kapals->lastItem() }} dari {{ $kapals->total() }} data
                </div>
                <div>
                    {{ $kapals->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
