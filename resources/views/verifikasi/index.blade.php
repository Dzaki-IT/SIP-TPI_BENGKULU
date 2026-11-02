@extends('layouts.app')

@section('title', 'Verifikasi Pendaratan Ikan')

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Verifikasi & Pengesahan</li>
                    <li class="breadcrumb-item active" aria-current="page">Antrean Verifikasi</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-shield-check text-primary"></i> Verifikasi Data Pendaratan Ikan
            </h4>
            <div class="text-muted small">Pemeriksaan, validasi, dan pengesahan data produksi hasil tangkapan pangkalan TPI se-Provinsi Bengkulu.</div>
        </div>
        <div>
            <span class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-hourglass-split me-1"></i> {{ $countDiajukan }} Dokumen Menunggu Verifikasi
            </span>
        </div>
    </div>

    <!-- Filter Card & Status Tabs -->
    <div class="card card-custom mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('verifikasi.index') }}">
                <!-- Status Tab Pills -->
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3 pb-3 border-bottom">
                    <span class="text-muted small fw-semibold me-1">Status Antrean:</span>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'diajukan']) }}"
                       class="btn btn-sm {{ $currentStatus === 'diajukan' ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary' }} rounded-pill px-3">
                        <i class="bi bi-clock me-1"></i> Menunggu Verifikasi ({{ $countDiajukan }})
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'diverifikasi']) }}"
                       class="btn btn-sm {{ $currentStatus === 'diverifikasi' ? 'btn-success fw-bold' : 'btn-outline-secondary' }} rounded-pill px-3">
                        <i class="bi bi-check-circle me-1"></i> Sudah Terverifikasi ({{ $countDiverifikasi }})
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'ditolak']) }}"
                       class="btn btn-sm {{ $currentStatus === 'ditolak' ? 'btn-danger fw-bold' : 'btn-outline-secondary' }} rounded-pill px-3">
                        <i class="bi bi-x-circle me-1"></i> Ditolak / Perlu Revisi ({{ $countDitolak }})
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'semua']) }}"
                       class="btn btn-sm {{ $currentStatus === 'semua' ? 'btn-primary fw-bold' : 'btn-outline-secondary' }} rounded-pill px-3">
                        Semua Status ({{ $countSemua }})
                    </a>
                </div>

                <!-- Input Filters -->
                <div class="row g-2 align-items-center">
                    <input type="hidden" name="status" value="{{ $currentStatus }}">

                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" name="q" class="form-control border-start-0" placeholder="Cari kode, kapal, nelayan..." value="{{ request('q', request('search')) }}">
                        </div>
                    </div>

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

                    <div class="col-md-2">
                        <input type="date" name="tanggal_dari" class="form-control" title="Tanggal Dari" value="{{ request('tanggal_dari') }}">
                    </div>

                    <div class="col-md-2">
                        <input type="date" name="tanggal_sampai" class="form-control" title="Tanggal Sampai" value="{{ request('tanggal_sampai') }}">
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="{{ route('verifikasi.index', ['status' => $currentStatus]) }}" class="btn btn-outline-secondary" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Queue Table Card -->
    <div class="card card-custom">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-nowrap text-secondary small">
                        <th class="ps-3" style="width: 50px;">No</th>
                        <th>Kode Pendaratan</th>
                        <th>Tanggal Sandar</th>
                        <th>Pangkalan TPI</th>
                        <th>Kapal / Nelayan</th>
                        <th>Komoditas Ikan</th>
                        <th class="text-end">Total Volume</th>
                        <th class="text-end">Nilai Produksi</th>
                        <th class="text-center">Status</th>
                        <th class="text-center pe-3" style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaratanList as $index => $item)
                    <tr>
                        <td class="ps-3 text-muted small">{{ $pendaratanList->firstItem() + $index }}</td>
                        <td>
                            <a href="{{ route('verifikasi.show', $item) }}" class="fw-bold text-decoration-none text-primary">
                                {{ $item->kode_pendaratan }}
                            </a>
                            <div class="text-muted" style="font-size: 0.72rem;">
                                Diajukan oleh: {{ $item->creator->name ?? 'Admin TPI' }}
                            </div>
                        </td>
                        <td class="text-nowrap small">
                            <i class="bi bi-calendar3 text-muted me-1"></i>
                            {{ $item->tanggal_pendaratan ? $item->tanggal_pendaratan->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-geo-alt text-primary me-1"></i>{{ $item->tpi->nama_tpi ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $item->kapal->nama_kapal ?? 'Tanpa Kapal' }}</div>
                            <div class="text-muted small"><i class="bi bi-person me-1"></i>{{ $item->nelayan->nama ?? '-' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                                {{ $item->detailPendaratan->count() }} Jenis Ikan
                            </span>
                        </td>
                        <td class="text-end fw-semibold text-nowrap">
                            {{ number_format($item->total_volume, 2, ',', '.') }} <span class="small text-muted fw-normal">Kg</span>
                        </td>
                        <td class="text-end fw-bold text-nowrap text-success">
                            Rp {{ number_format($item->total_nilai, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if($item->status_verifikasi === 'draft')
                                <span class="badge bg-secondary">Draf</span>
                            @elseif($item->status_verifikasi === 'diajukan')
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-clock me-1"></i>Menunggu Verifikasi
                                </span>
                            @elseif($item->status_verifikasi === 'diverifikasi')
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Terverifikasi
                                </span>
                            @elseif($item->status_verifikasi === 'ditolak')
                                <span class="badge bg-danger" data-bs-toggle="tooltip" title="{{ $item->catatan_verifikasi }}">
                                    <i class="bi bi-x-circle me-1"></i>Ditolak
                                </span>
                            @endif
                        </td>
                        <td class="text-center pe-3 text-nowrap">
                            @if($item->status_verifikasi === 'diajukan')
                                @can('verify', $item)
                                <a href="{{ route('verifikasi.show', $item) }}" class="btn btn-sm btn-primary shadow-sm px-3">
                                    <i class="bi bi-shield-check me-1"></i> Tinjau & Verifikasi
                                </a>
                                @else
                                <a href="{{ route('verifikasi.show', $item) }}" class="btn btn-sm btn-outline-secondary px-3">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </a>
                                @endcan
                            @else
                                <a href="{{ route('verifikasi.show', $item) }}" class="btn btn-sm btn-outline-secondary px-3">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <div class="text-muted mb-2">
                                <i class="bi bi-check-circle text-success fs-1"></i>
                            </div>
                            <h6 class="fw-bold text-dark">Antrean Bersih</h6>
                            <p class="text-muted small mb-0">
                                @if($currentStatus === 'diajukan')
                                    Tidak ada data pendaratan yang menunggu verifikasi saat ini.
                                @else
                                    Tidak ditemukan data pendaratan sesuai filter status ini.
                                @endif
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pendaratanList->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Menampilkan {{ $pendaratanList->firstItem() }} hingga {{ $pendaratanList->lastItem() }} dari {{ $pendaratanList->total() }} entri
            </div>
            <div>
                {{ $pendaratanList->links() }}
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
