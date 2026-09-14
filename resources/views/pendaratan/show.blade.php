@extends('layouts.app')

@section('title', 'Dokumen Pendaratan ' . $pendaratan->kode_pendaratan)

@push('styles')
<style>
@media print {
    #sidebar, #topNavbar, .no-print, .btn, .breadcrumb, .alert-dismissible .btn-close {
        display: none !important;
    }
    #mainContent {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .print-only {
        display: block !important;
    }
    body {
        background-color: #fff !important;
        color: #000 !important;
        font-size: 12pt;
    }
    .table-bordered th, .table-bordered td {
        border: 1px solid #000 !important;
    }
}
.print-only {
    display: none;
}
</style>
@endpush

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header & Action Buttons (No Print) -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 no-print">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pendaratan.index') }}" class="text-decoration-none">Riwayat Pendaratan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $pendaratan->kode_pendaratan }}</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-medical text-primary"></i> Dokumen Bukti Pendaratan Ikan
            </h4>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('pendaratan.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
            </a>

            <button type="button" class="btn btn-outline-dark" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Dokumen
            </button>

            <!-- Actions for Draft or Ditolak -->
            @if(in_array($pendaratan->status_verifikasi, ['draft', 'ditolak']) && (auth()->user()?->role === 'admin_tpi' || auth()->user()?->isAdmin()))
                <a href="{{ route('pendaratan.edit', $pendaratan) }}" class="btn btn-outline-warning">
                    <i class="bi bi-pencil me-1"></i> Ubah Data
                </a>

                <form action="{{ route('pendaratan.ajukan', $pendaratan) }}" method="POST" class="d-inline" onsubmit="return confirm('Ajukan dokumen pendaratan ini ke verifikator dinas?');">
                    @csrf
                    <button type="submit" class="btn btn-primary shadow-sm">
                        <i class="bi bi-send-check me-1"></i> Ajukan Verifikasi
                    </button>
                </form>
            @endif

            <!-- Delete for Draft only -->
            @if($pendaratan->status_verifikasi === 'draft' && (auth()->user()?->role === 'admin_tpi' || auth()->user()?->isAdmin()))
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="bi bi-trash me-1"></i> Hapus
                </button>
            @endif

            <!-- Quick link for Verifikator if Diajukan -->
            @if($pendaratan->status_verifikasi === 'diajukan' && auth()->user()?->isVerifikator())
                <a href="{{ route('verifikasi.show', $pendaratan) }}" class="btn btn-success shadow-sm">
                    <i class="bi bi-shield-check me-1"></i> Verifikasi Sekarang
                </a>
            @endif
        </div>
    </div>

    <!-- Rejection Alert Banner -->
    @if($pendaratan->status_verifikasi === 'ditolak')
    <div class="alert alert-danger shadow-sm border-danger d-flex align-items-start gap-3 p-4 mb-4" role="alert">
        <i class="bi bi-exclamation-octagon-fill fs-2 text-danger flex-shrink-0"></i>
        <div class="w-100">
            <h5 class="alert-heading fw-bold mb-1">Perhatian: Data Pendaratan Ditolak / Memerlukan Perbaikan</h5>
            <div class="p-3 bg-white rounded border border-danger border-opacity-50 my-2 text-dark font-monospace">
                "{{ $pendaratan->catatan_verifikasi }}"
            </div>
            <div class="small text-muted d-flex flex-wrap gap-3">
                <span><i class="bi bi-person me-1"></i> Verifikator: <strong>{{ $pendaratan->verifier->name ?? 'Petugas Verifikator' }}</strong></span>
                @if($pendaratan->verified_at)
                    <span><i class="bi bi-clock me-1"></i> Waktu: {{ $pendaratan->verified_at->translatedFormat('d F Y, H:i') }} WIB</span>
                @endif
            </div>
            @if(auth()->user()?->role === 'admin_tpi' || auth()->user()?->isAdmin())
            <div class="mt-3">
                <a href="{{ route('pendaratan.edit', $pendaratan) }}" class="btn btn-sm btn-danger">
                    <i class="bi bi-pencil-square me-1"></i> Klik di Sini untuk Memperbaiki Data
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Verification Approved Banner -->
    @if($pendaratan->status_verifikasi === 'diverifikasi')
    <div class="alert alert-success shadow-sm border-success d-flex align-items-center gap-3 p-3 mb-4" role="alert">
        <i class="bi bi-patch-check-fill fs-2 text-success flex-shrink-0"></i>
        <div>
            <div class="fw-bold fs-6">Data Telah Diverifikasi & Disahkan secara Resmi</div>
            <div class="small text-muted">
                Diverifikasi oleh <strong>{{ $pendaratan->verifier->name ?? 'Petugas Verifikator DKP' }}</strong>
                @if($pendaratan->verified_at)
                    pada tanggal {{ $pendaratan->verified_at->translatedFormat('d F Y, H:i') }} WIB.
                @endif
                @if($pendaratan->catatan_verifikasi)
                    <span class="fst-italic">"{{ $pendaratan->catatan_verifikasi }}"</span>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Document Card (Printed receipt structure) -->
    <div class="card card-custom p-4 border">
        <!-- Print Header (Official Dinas Kop) -->
        <div class="text-center pb-3 mb-4 border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <div class="text-start">
                    <h6 class="fw-bold text-uppercase mb-0 text-dark">Pemerintah Provinsi Bengkulu</h6>
                    <div class="small text-muted">Dinas Kelautan dan Perikanan</div>
                    <div class="text-muted" style="font-size: 0.72rem;">Sistem Informasi Tata Kelola Aktivitas TPI (SIP-TPI)</div>
                </div>
                <div class="text-end">
                    <div class="small text-muted">KODE PENDARATAN</div>
                    <div class="font-monospace fw-bold fs-5 text-primary">{{ $pendaratan->kode_pendaratan }}</div>
                    <div>
                        @if($pendaratan->status_verifikasi === 'draft')
                            <span class="badge bg-secondary px-2 py-1">DRAF</span>
                        @elseif($pendaratan->status_verifikasi === 'diajukan')
                            <span class="badge bg-warning text-dark px-2 py-1">MENUNGGU VERIFIKASI</span>
                        @elseif($pendaratan->status_verifikasi === 'diverifikasi')
                            <span class="badge bg-success px-2 py-1">TERVERIFIKASI & SAH</span>
                        @elseif($pendaratan->status_verifikasi === 'ditolak')
                            <span class="badge bg-danger px-2 py-1">DITOLAK / PERLU REVISI</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <h5 class="fw-bold text-dark text-uppercase letter-spacing-1 mb-0">Tanda Bukti Pendaratan Ikan (TBPI)</h5>
                <div class="small text-muted">Nomor Registrasi Elektronik: {{ $pendaratan->kode_pendaratan }}</div>
            </div>
        </div>

        <!-- Two Column Meta Info -->
        <div class="row g-4 mb-4">
            <!-- Left: Identitas Kapal & Nelayan -->
            <div class="col-md-6">
                <div class="p-3 bg-light rounded-3 h-100 border">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-tsunami text-primary"></i> Identitas Kapal & Nelayan
                    </h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 140px;">Nama Kapal</td>
                            <td class="fw-bold text-dark">: {{ $pendaratan->kapal->nama_kapal ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Ukuran Kapal</td>
                            <td class="text-dark">: {{ $pendaratan->kapal && $pendaratan->kapal->gt ? $pendaratan->kapal->gt . ' Gross Tonnage (GT)' : 'Tanpa Mesin / Motor Tempel' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">No. SIPI / Registrasi</td>
                            <td class="text-dark">: {{ $pendaratan->kapal->no_sipi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nakhoda / Nelayan</td>
                            <td class="fw-bold text-dark">: {{ $pendaratan->nelayan->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">NIK Nelayan</td>
                            <td class="text-dark">: {{ $pendaratan->nelayan->nik ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kelompok Nelayan</td>
                            <td class="text-dark">: {{ $pendaratan->nelayan->kelompok_nelayan ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Right: Info Pangkalan & Operasional -->
            <div class="col-md-6">
                <div class="p-3 bg-light rounded-3 h-100 border">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-building text-primary"></i> Pangkalan & Operasional
                    </h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 140px;">Pangkalan TPI</td>
                            <td class="fw-bold text-dark">: {{ $pendaratan->tpi->nama_tpi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kabupaten / Kota</td>
                            <td class="text-dark">: {{ $pendaratan->tpi->wilayah->nama_kabupaten_kota ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Sandar</td>
                            <td class="fw-bold text-primary">: {{ $pendaratan->tanggal_pendaratan ? $pendaratan->tanggal_pendaratan->translatedFormat('d F Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alat Tangkap (API)</td>
                            <td class="text-dark">: {{ $pendaratan->alatTangkap->nama_alat ?? '-' }} ({{ $pendaratan->alatTangkap->jenis_kategori ?? 'Umum' }})</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Petugas Pencatat</td>
                            <td class="text-dark">: {{ $pendaratan->creator->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status Dokumen</td>
                            <td class="text-dark">:
                                <span class="fw-semibold text-uppercase">
                                    {{ $pendaratan->status_verifikasi }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Fish Landing Items Table -->
        <div class="mb-4">
            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-basket text-primary"></i> Rincian Komoditas Ikan Hasil Tangkapan
            </h6>
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-secondary small">
                            <th style="width: 40px;" class="text-center">No</th>
                            <th>Jenis Komoditas Ikan</th>
                            <th>Kategori</th>
                            <th class="text-end" style="width: 130px;">Volume</th>
                            <th class="text-end" style="width: 150px;">Harga Satuan</th>
                            <th class="text-end" style="width: 170px;">Nilai Produksi</th>
                            <th>Keterangan Mutu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendaratan->detailPendaratan as $index => $detail)
                        <tr>
                            <td class="text-center text-muted small">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $detail->jenisIkan->nama_resmi ?? '-' }}</div>
                                @if($detail->jenisIkan && $detail->jenisIkan->nama_lokal)
                                    <div class="text-muted small fst-italic">Nama lokal: {{ $detail->jenisIkan->nama_lokal }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border small">
                                    {{ $detail->jenisIkan->kategori ?? 'Laut' }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold text-nowrap">
                                {{ number_format($detail->volume_kg, 2, ',', '.') }} <span class="small text-muted fw-normal">Kg</span>
                            </td>
                            <td class="text-end text-nowrap">
                                @if($detail->harga_per_kg > 0)
                                    Rp {{ number_format($detail->harga_per_kg, 0, ',', '.') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-success text-nowrap">
                                Rp {{ number_format($detail->nilai_produksi, 0, ',', '.') }}
                            </td>
                            <td class="small text-muted">
                                {{ $detail->keterangan ?: '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Tidak ada rincian ikan yang dicatat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="3" class="text-end text-uppercase pe-3">Grand Total :</td>
                            <td class="text-end text-primary text-nowrap">
                                {{ number_format($pendaratan->total_volume, 2, ',', '.') }} Kg
                            </td>
                            <td></td>
                            <td class="text-end text-success fs-6 text-nowrap">
                                Rp {{ number_format($pendaratan->total_nilai, 0, ',', '.') }}
                            </td>
                            <td class="small text-muted fw-normal">{{ $pendaratan->detailPendaratan->count() }} komoditas</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Signatures & Verification Footer (Print Friendly) -->
        <div class="row text-center mt-5 pt-4">
            <div class="col-4">
                <div class="small text-muted">Nakhoda / Nelayan,</div>
                <div style="height: 60px;"></div>
                <div class="fw-bold text-dark text-decoration-underline">{{ $pendaratan->nelayan->nama ?? '(........................)' }}</div>
                <div class="small text-muted">{{ $pendaratan->kapal->nama_kapal ?? 'Pemilik Kapal' }}</div>
            </div>
            <div class="col-4">
                <div class="small text-muted">Petugas Pencatat TPI,</div>
                <div style="height: 60px;"></div>
                <div class="fw-bold text-dark text-decoration-underline">{{ $pendaratan->creator->name ?? '(........................)' }}</div>
                <div class="small text-muted">{{ $pendaratan->tpi->nama_tpi ?? 'Admin TPI' }}</div>
            </div>
            <div class="col-4">
                <div class="small text-muted">Petugas Verifikator DKP,</div>
                <div style="height: 60px;" class="d-flex align-items-center justify-content-center">
                    @if($pendaratan->status_verifikasi === 'diverifikasi')
                        <span class="badge bg-success bg-opacity-10 text-success border border-success p-2 rounded">
                            <i class="bi bi-shield-check me-1"></i> SAH & TERVERIFIKASI
                        </span>
                    @endif
                </div>
                <div class="fw-bold text-dark text-decoration-underline">{{ $pendaratan->verifier->name ?? '(........................)' }}</div>
                <div class="small text-muted">Dinas Kelautan dan Perikanan</div>
            </div>
        </div>
    </div>

</div>

<!-- Delete Confirmation Modal (Draft Only) -->
@if($pendaratan->status_verifikasi === 'draft')
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-danger">
                    <i class="bi bi-exclamation-triangle me-1"></i> Konfirmasi Hapus Data
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data draf pendaratan <strong>{{ $pendaratan->kode_pendaratan }}</strong>?</p>
                <div class="alert alert-warning small mb-0">
                    Tindakan ini permanen dan akan menghapus seluruh data komoditas ikan di dalamnya.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('pendaratan.destroy', $pendaratan) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus Permanen</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
