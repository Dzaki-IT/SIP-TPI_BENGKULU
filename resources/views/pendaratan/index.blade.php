@extends('layouts.app')

@section('title', 'Riwayat Pendaratan Ikan')

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header & Actions -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Aktivitas & Transaksi</li>
                    <li class="breadcrumb-item active" aria-current="page">Riwayat Pendaratan</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-journal-text text-primary"></i> Data Pendaratan & Produksi Ikan
            </h4>
        </div>
        @can('create', \App\Models\Pendaratan::class)
        <div>
            <a href="{{ route('pendaratan.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center gap-2 px-3 py-2">
                <i class="bi bi-plus-circle-fill"></i>
                <span class="fw-semibold">Catat Pendaratan Ikan</span>
            </a>
        </div>
        @endcan
    </div>

    <!-- Summary Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom h-100 border-start border-primary border-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-medium">Total Pendaratan</div>
                            <div class="fs-4 fw-bold text-dark">{{ number_format($summary['total'], 0, ',', '.') }}</div>
                            <div class="small text-muted mt-1">
                                <span class="text-secondary fw-semibold">{{ $summary['draft'] }}</span> Draf &bull;
                                <span class="text-success fw-semibold">{{ $summary['diverifikasi'] }}</span> Terverifikasi
                            </div>
                        </div>
                        <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary fs-4">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom h-100 border-start border-info border-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-medium">Total Volume Produksi</div>
                            <div class="fs-4 fw-bold text-dark">{{ number_format($summary['total_volume'], 2, ',', '.') }} <span class="fs-6 fw-normal text-muted">Kg</span></div>
                            <div class="small text-muted mt-1">Akumulasi seluruh komoditas</div>
                        </div>
                        <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info fs-4">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom h-100 border-start border-success border-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-medium">Total Nilai Produksi</div>
                            <div class="fs-4 fw-bold text-dark">Rp {{ number_format($summary['total_nilai'], 0, ',', '.') }}</div>
                            <div class="small text-muted mt-1">Estimasi nilai ekonomi</div>
                        </div>
                        <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success fs-4">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom h-100 border-start border-warning border-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-medium">Menunggu Verifikasi</div>
                            <div class="fs-4 fw-bold text-warning">{{ number_format($summary['diajukan'], 0, ',', '.') }}</div>
                            <div class="small text-muted mt-1">
                                @if($summary['ditolak'] > 0)
                                    <span class="text-danger fw-semibold">{{ $summary['ditolak'] }} perlu perbaikan</span>
                                @else
                                    <span>Dalam antrean verifikator</span>
                                @endif
                            </div>
                        </div>
                        <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning fs-4">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="card card-custom mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('pendaratan.index') }}">
                <!-- Status Badge Pills -->
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3 pb-3 border-bottom">
                    <span class="text-muted small fw-semibold me-1">Status:</span>
                    <a href="{{ request()->fullUrlWithQuery(['status_verifikasi' => '']) }}"
                       class="btn btn-sm {{ !request('status_verifikasi') ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">
                        Semua ({{ $summary['total'] }})
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status_verifikasi' => 'diajukan']) }}"
                       class="btn btn-sm {{ request('status_verifikasi') === 'diajukan' ? 'btn-warning text-dark' : 'btn-outline-secondary' }} rounded-pill px-3">
                        <i class="bi bi-clock"></i> Menunggu Verifikasi ({{ $summary['diajukan'] }})
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status_verifikasi' => 'diverifikasi']) }}"
                       class="btn btn-sm {{ request('status_verifikasi') === 'diverifikasi' ? 'btn-success' : 'btn-outline-secondary' }} rounded-pill px-3">
                        <i class="bi bi-check-circle"></i> Terverifikasi ({{ $summary['diverifikasi'] }})
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status_verifikasi' => 'draft']) }}"
                       class="btn btn-sm {{ request('status_verifikasi') === 'draft' ? 'btn-secondary' : 'btn-outline-secondary' }} rounded-pill px-3">
                        <i class="bi bi-file-earmark"></i> Draf ({{ $summary['draft'] }})
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status_verifikasi' => 'ditolak']) }}"
                       class="btn btn-sm {{ request('status_verifikasi') === 'ditolak' ? 'btn-danger' : 'btn-outline-secondary' }} rounded-pill px-3">
                        <i class="bi bi-x-circle"></i> Ditolak ({{ $summary['ditolak'] }})
                    </a>
                </div>

                <!-- Input Filters -->
                <div class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" name="q" class="form-control border-start-0" placeholder="Cari kode, kapal, nelayan..." value="{{ request('q', request('search')) }}">
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
                        <input type="text" class="form-control bg-light text-muted" value="{{ auth()->user()->tpi->nama_tpi ?? 'Pangkalan TPI' }}" readonly disabled>
                    </div>
                    @endif

                    <div class="col-md-2">
                        <input type="date" name="tanggal_dari" class="form-control" title="Tanggal Dari" value="{{ request('tanggal_dari') }}" placeholder="Tanggal Dari">
                    </div>

                    <div class="col-md-2">
                        <input type="date" name="tanggal_sampai" class="form-control" title="Tanggal Sampai" value="{{ request('tanggal_sampai') }}" placeholder="Tanggal Sampai">
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="{{ route('pendaratan.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card card-custom">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-nowrap text-secondary small">
                        <th class="ps-3" style="width: 50px;">No</th>
                        <th>Kode Pendaratan</th>
                        <th>Tanggal</th>
                        <th>Pangkalan TPI</th>
                        <th>Kapal / Nelayan</th>
                        <th>Alat Tangkap</th>
                        <th class="text-end">Volume</th>
                        <th class="text-end">Nilai Produksi</th>
                        <th class="text-center">Status</th>
                        <th class="text-center pe-3" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaratan as $index => $item)
                    <tr>
                        <td class="ps-3 text-muted small">{{ $pendaratan->firstItem() + $index }}</td>
                        <td>
                            <a href="{{ route('pendaratan.show', $item) }}" class="fw-bold text-decoration-none text-primary">
                                {{ $item->kode_pendaratan }}
                            </a>
                            <div class="text-muted" style="font-size: 0.72rem;">
                                {{ $item->detailPendaratan->count() }} komoditas ikan
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
                            <div class="text-muted small">
                                <i class="bi bi-person me-1"></i>{{ $item->nelayan->nama ?? '-' }}
                            </div>
                        </td>
                        <td class="small text-muted">
                            {{ $item->alatTangkap->nama_alat ?? '-' }}
                        </td>
                        <td class="text-end fw-semibold text-nowrap">
                            {{ number_format($item->total_volume, 2, ',', '.') }} <span class="small text-muted fw-normal">Kg</span>
                        </td>
                        <td class="text-end fw-bold text-nowrap text-success">
                            Rp {{ number_format($item->total_nilai, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if($item->status_verifikasi === 'draft')
                                <span class="badge bg-secondary">
                                    <i class="bi bi-pencil-square me-1"></i>Draf
                                </span>
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
                            <div class="btn-group btn-group-sm">
                                <!-- Detail Button -->
                                <a href="{{ route('pendaratan.show', $item) }}" class="btn btn-outline-secondary" title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <!-- Quick Ajukan (if draft or ditolak) -->
                                @if(in_array($item->status_verifikasi, ['draft', 'ditolak']))
                                @can('submit', $item)
                                <form action="{{ route('pendaratan.ajukan', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Ajukan data {{ $item->kode_pendaratan }} ke petugas verifikator?');">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary" title="Ajukan Verifikasi">
                                        <i class="bi bi-send"></i>
                                    </button>
                                </form>
                                @endcan
                                @endif

                                <!-- Edit Button (only draft or ditolak) -->
                                @if(in_array($item->status_verifikasi, ['draft', 'ditolak']))
                                @can('update', $item)
                                <a href="{{ route('pendaratan.edit', $item) }}" class="btn btn-outline-warning" title="Ubah Data">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan
                                @endif

                                <!-- Delete Button (only draft) -->
                                @if($item->status_verifikasi === 'draft')
                                @can('delete', $item)
                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id }}" title="Hapus Data">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endcan
                                @endif
                            </div>

                            <!-- Delete Modal for Draft -->
                            @if($item->status_verifikasi === 'draft')
                            @can('delete', $item)
                            <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold text-danger">
                                                <i class="bi bi-exclamation-triangle me-1"></i> Konfirmasi Hapus
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <p class="mb-1">Apakah Anda yakin ingin menghapus data draf pendaratan:</p>
                                            <div class="p-2 bg-light rounded my-2">
                                                <strong class="text-primary">{{ $item->kode_pendaratan }}</strong><br>
                                                <small class="text-muted">Tanggal: {{ $item->tanggal_pendaratan?->format('d/m/Y') }} &bull; TPI: {{ $item->tpi->nama_tpi ?? '-' }}</small>
                                            </div>
                                            <p class="text-danger small mb-0">Tindakan ini tidak dapat dibatalkan dan akan menghapus seluruh rincian tangkapan ikan terkait.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('pendaratan.destroy', $item) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Hapus Permanen</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endcan
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <div class="text-muted mb-2">
                                <i class="bi bi-inbox fs-1"></i>
                            </div>
                            <h6 class="fw-bold text-dark">Belum Ada Data Pendaratan</h6>
                            <p class="text-muted small mb-3">Tidak ada data pendaratan yang sesuai dengan kriteria filter Anda.</p>
                            @can('create', \App\Models\Pendaratan::class)
                            <a href="{{ route('pendaratan.create') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> Catat Pendaratan Pertama
                            </a>
                            @endcan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pendaratan->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Menampilkan {{ $pendaratan->firstItem() }} hingga {{ $pendaratan->lastItem() }} dari {{ $pendaratan->total() }} entri
            </div>
            <div>
                {{ $pendaratan->links() }}
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
