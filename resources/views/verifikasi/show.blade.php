@extends('layouts.app')

@section('title', 'Tinjau Pendaratan ' . $pendaratan->kode_pendaratan)

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header & Breadcrumbs -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('verifikasi.index') }}" class="text-decoration-none">Verifikasi Data</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $pendaratan->kode_pendaratan }}</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-shield-check text-primary"></i> Peninjauan & Verifikasi Data Pendaratan
            </h4>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('verifikasi.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Antrean
            </a>
            <a href="{{ route('pendaratan.show', $pendaratan) }}" class="btn btn-outline-dark">
                <i class="bi bi-file-earmark-text me-1"></i> Format Dokumen Resmi
            </a>
        </div>
    </div>

    <!-- Verifier Decision Card (Prominent at Top for Actionable Workflow) -->
    @if($pendaratan->status_verifikasi === 'diajukan')
        @can('verify', $pendaratan)
        <div class="card card-custom border-warning border-2 shadow-sm mb-4">
            <div class="card-header bg-warning bg-opacity-10 py-3 border-bottom border-warning border-opacity-25">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-hourglass-split text-warning fs-5"></i>
                        <span class="fw-bold text-dark fs-6">Keputusan Verifikasi & Pengesahan Data</span>
                    </div>
                    <span class="badge bg-warning text-dark px-3 py-1">Menunggu Keputusan Anda</span>
                </div>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-4">
                    Periksa kesesuaian data pangkalan TPI, kapal, nakhoda, alat tangkap, serta volume dan harga komoditas ikan di bawah ini sebelum mengambil keputusan pengesahan.
                </p>

                <div class="row g-3">
                    <!-- Approve Action Form -->
                    <div class="col-md-7">
                        <form action="{{ route('verifikasi.approve', $pendaratan) }}" method="POST" id="formApprove">
                            @csrf
                            <div class="mb-3">
                                <label for="catatan" class="form-label fw-semibold small text-muted">Catatan Pengesahan (Opsional):</label>
                                <input type="text" name="catatan" id="catatan" class="form-control form-control-sm" placeholder="Contoh: Data telah diverifikasi sesuai logbook TPI.">
                            </div>
                            <button type="submit" class="btn btn-success btn-lg px-4 shadow-sm w-100 d-flex align-items-center justify-content-center gap-2"
                                    onclick="return confirm('Apakah Anda yakin data ini valid dan siap disahkan secara resmi?');">
                                <i class="bi bi-check-circle-fill fs-5"></i>
                                <span class="fw-bold">Setujui & Sahkan Data</span>
                            </button>
                        </form>
                    </div>

                    <!-- Reject Action Modal Trigger -->
                    <div class="col-md-5 d-flex flex-column justify-content-end">
                        <button type="button" class="btn btn-outline-danger btn-lg px-4 w-100 d-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle-fill fs-5"></i>
                            <span class="fw-bold">Tolak & Minta Perbaikan</span>
                        </button>
                        <small class="text-muted text-center mt-2">Data yang ditolak akan dikembalikan ke Admin TPI dengan catatan revisi.</small>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Pimpinan or other roles (Read-only notice) -->
        <div class="alert alert-warning shadow-sm d-flex align-items-center gap-3 p-3 mb-4">
            <i class="bi bi-info-circle-fill fs-3 text-warning"></i>
            <div>
                <div class="fw-bold">Status: Menunggu Tindakan Verifikator DKP</div>
                <div class="small text-muted">Akun Anda berstatus peninjau (Pimpinan). Keputusan persetujuan dan penolakan hanya dapat dilakukan oleh akun dengan peran Verifikator.</div>
            </div>
        </div>
        @endcan
    @elseif($pendaratan->status_verifikasi === 'diverifikasi')
        <div class="alert alert-success shadow-sm border-success d-flex align-items-center gap-3 p-3 mb-4">
            <i class="bi bi-check-circle-fill fs-2 text-success"></i>
            <div>
                <div class="fw-bold fs-6">Data Pendaratan Ini Telah Disetujui & Terverifikasi</div>
                <div class="small text-muted">
                    Diverifikasi oleh <strong>{{ $pendaratan->verifier->name ?? 'Verifikator' }}</strong>
                    @if($pendaratan->verified_at)
                        pada {{ $pendaratan->verified_at->translatedFormat('d F Y, H:i') }} WIB
                    @endif
                    @if($pendaratan->catatan_verifikasi)
                        &bull; Catatan: <span class="fst-italic">"{{ $pendaratan->catatan_verifikasi }}"</span>
                    @endif
                </div>
            </div>
        </div>
    @elseif($pendaratan->status_verifikasi === 'ditolak')
        <div class="alert alert-danger shadow-sm border-danger d-flex align-items-start gap-3 p-4 mb-4">
            <i class="bi bi-x-octagon-fill fs-2 text-danger flex-shrink-0 mt-1"></i>
            <div>
                <h6 class="fw-bold text-danger mb-1">Status: Ditolak / Dalam Tahap Perbaikan</h6>
                <div class="p-3 bg-white rounded border border-danger border-opacity-50 my-2 text-dark font-monospace">
                    "{{ $pendaratan->catatan_verifikasi }}"
                </div>
                <div class="small text-muted">
                    Ditolak oleh <strong>{{ $pendaratan->verifier->name ?? 'Verifikator' }}</strong>
                    @if($pendaratan->verified_at)
                        pada {{ $pendaratan->verified_at->translatedFormat('d F Y, H:i') }} WIB
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Transaction Summary & Meta Details -->
    <div class="row g-4 mb-4">
        <!-- Pangkalan & Creator Info -->
        <div class="col-md-6">
            <div class="card card-custom h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-geo-alt text-primary"></i> Data Pangkalan & Petugas Pencatat
                    </h6>
                </div>
                <div class="card-body p-3">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 160px;">Kode Transaksi</td>
                            <td class="fw-bold font-monospace text-primary">: {{ $pendaratan->kode_pendaratan }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Pangkalan TPI</td>
                            <td class="fw-semibold text-dark">: {{ $pendaratan->tpi->nama_tpi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Wilayah Kabupaten/Kota</td>
                            <td class="text-dark">: {{ $pendaratan->tpi->wilayah->nama_kabupaten_kota ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Pendaratan</td>
                            <td class="fw-bold text-dark">: {{ $pendaratan->tanggal_pendaratan ? $pendaratan->tanggal_pendaratan->translatedFormat('d F Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Petugas Pencatat (Admin)</td>
                            <td class="text-dark">: {{ $pendaratan->creator->name ?? '-' }} ({{ $pendaratan->creator->email ?? '-' }})</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Waktu Pencatatan</td>
                            <td class="text-muted small">: {{ $pendaratan->created_at ? $pendaratan->created_at->translatedFormat('d M Y, H:i') : '-' }} WIB</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Kapal & Nelayan Info -->
        <div class="col-md-6">
            <div class="card card-custom h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-tsunami text-primary"></i> Identitas Kapal, Nelayan & Alat Tangkap
                    </h6>
                </div>
                <div class="card-body p-3">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 160px;">Armada Kapal</td>
                            <td class="fw-bold text-dark">: {{ $pendaratan->kapal->nama_kapal ?? 'Tanpa Kapal Terdaftar' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tonase / GT</td>
                            <td class="text-dark">: {{ $pendaratan->kapal && $pendaratan->kapal->gt ? $pendaratan->kapal->gt . ' GT' : 'Non-mesin / Perahu kecil' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nomor Izin SIPI</td>
                            <td class="text-dark">: {{ $pendaratan->kapal->no_sipi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nakhoda / Nelayan</td>
                            <td class="fw-bold text-dark">: {{ $pendaratan->nelayan->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">NIK / Kelompok</td>
                            <td class="text-dark">: {{ $pendaratan->nelayan->nik ?? '-' }} {{ $pendaratan->nelayan && $pendaratan->nelayan->kelompok_nelayan ? "({$pendaratan->nelayan->kelompok_nelayan})" : '' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alat Penangkapan Ikan</td>
                            <td class="text-dark">: {{ $pendaratan->alatTangkap->nama_alat ?? '-' }} ({{ $pendaratan->alatTangkap->jenis_kategori ?? 'Umum' }})</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Fish Landing Items Table -->
    <div class="card card-custom mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-basket text-primary"></i> Rincian Komoditas Ikan Hasil Tangkapan ({{ $pendaratan->detailPendaratan->count() }} Jenis)
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-secondary small">
                        <th style="width: 40px;" class="text-center">No</th>
                        <th>Komoditas Ikan</th>
                        <th>Kategori</th>
                        <th class="text-end" style="width: 140px;">Volume (Kg)</th>
                        <th class="text-end" style="width: 160px;">Harga Satuan</th>
                        <th class="text-end" style="width: 180px;">Nilai Produksi (Rp)</th>
                        <th>Keterangan Mutu / Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaratan->detailPendaratan as $index => $detail)
                    <tr>
                        <td class="text-center text-muted small">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $detail->jenisIkan->nama_resmi ?? '-' }}</div>
                            @if($detail->jenisIkan && $detail->jenisIkan->nama_lokal)
                                <div class="text-muted small">Nama lokal: {{ $detail->jenisIkan->nama_lokal }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
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
                            Tidak ada komoditas ikan terdaftar.
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

</div>

<!-- Rejection Modal -->
@can('verify', $pendaratan)
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('verifikasi.reject', $pendaratan) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="rejectModalLabel">
                        <i class="bi bi-x-circle me-1"></i> Tolak & Minta Perbaikan Data
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        Tentukan alasan atau rincian perbaikan yang harus dilakukan oleh Admin TPI terkait data pendaratan <strong>{{ $pendaratan->kode_pendaratan }}</strong>.
                    </p>

                    <div class="mb-3">
                        <label for="catatan_verifikasi" class="form-label fw-semibold">
                            Catatan Alasan Penolakan / Instruksi Revisi <span class="text-danger">*</span>
                        </label>
                        <textarea name="catatan_verifikasi" id="catatan_verifikasi" rows="4"
                                  class="form-control @error('catatan_verifikasi') is-invalid @enderror"
                                  placeholder="Jelaskan secara spesifik data yang keliru (contoh: Volume ikan tongkol tidak sesuai nota lelang, mohon perbaiki menjadi 120 Kg)." required minlength="5">{{ old('catatan_verifikasi') }}</textarea>
                        <small class="text-muted">Minimal 5 karakter. Catatan ini akan ditampilkan secara langsung kepada Admin TPI.</small>
                        @error('catatan_verifikasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="bi bi-send me-1"></i> Kirim Penolakan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@endsection
