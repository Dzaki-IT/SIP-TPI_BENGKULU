@extends('layouts.app')

@section('title', 'Ubah Pendaratan ' . $pendaratan->kode_pendaratan)

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header & Breadcrumbs -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pendaratan.index') }}" class="text-decoration-none">Riwayat Pendaratan</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pendaratan.show', $pendaratan) }}" class="text-decoration-none">{{ $pendaratan->kode_pendaratan }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ubah Data</li>
            </ol>
        </nav>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-pencil-square text-primary"></i> Ubah Data Pendaratan: {{ $pendaratan->kode_pendaratan }}
                </h4>
                <div class="text-muted small mt-1">Perbarui data pendaratan dan rincian komoditas hasil tangkapan.</div>
            </div>
            <div>
                @if($pendaratan->status_verifikasi === 'ditolak')
                    <span class="badge bg-danger fs-6 px-3 py-2">
                        <i class="bi bi-exclamation-triangle me-1"></i> Status: Perlu Revisi / Ditolak
                    </span>
                @else
                    <span class="badge bg-secondary fs-6 px-3 py-2">
                        <i class="bi bi-file-earmark me-1"></i> Status: Draf
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Alert Box if Status is DITOLAK -->
    @if($pendaratan->status_verifikasi === 'ditolak')
    <div class="alert alert-danger shadow-sm border-danger d-flex align-items-start gap-3 p-4 mb-4" role="alert">
        <i class="bi bi-exclamation-octagon-fill fs-3 text-danger flex-shrink-0 mt-1"></i>
        <div class="w-100">
            <h5 class="alert-heading fw-bold mb-1">Catatan Penolakan dari Petugas Verifikator:</h5>
            <div class="p-3 bg-white rounded border border-danger border-opacity-50 my-2 text-dark font-monospace fs-6">
                "{{ $pendaratan->catatan_verifikasi }}"
            </div>
            <div class="small text-muted">
                Ditolak oleh: <strong>{{ $pendaratan->verifier->name ?? 'Petugas Verifikator' }}</strong>
                @if($pendaratan->verified_at)
                    &bull; Tanggal: {{ $pendaratan->verified_at->translatedFormat('d F Y, H:i') }} WIB
                @endif
            </div>
            <div class="small fw-semibold text-danger mt-2">
                <i class="bi bi-arrow-right-circle me-1"></i> Silakan perbaiki data di bawah ini sesuai arahan verifikator, lalu klik tombol <strong>"Simpan & Ajukan Verifikasi"</strong>.
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('pendaratan.update', $pendaratan) }}" method="POST" id="pendaratanEditForm">
        @csrf
        @method('PUT')

        <!-- Card 1: Informasi Header Operasional -->
        <div class="card card-custom mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle text-primary"></i> 1. Informasi Pangkalan TPI & Operasional Kapal
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <!-- TPI -->
                    <div class="col-md-6">
                        <label for="tpi_id" class="form-label fw-semibold">Pangkalan TPI <span class="text-danger">*</span></label>
                        @if(auth()->user()?->role === 'admin_tpi' && auth()->user()?->tpi_id)
                            <input type="hidden" name="tpi_id" value="{{ auth()->user()->tpi_id }}">
                            <input type="text" class="form-control bg-light" value="{{ auth()->user()->tpi->nama_tpi }} ({{ auth()->user()->tpi->wilayah->nama_kabupaten_kota ?? 'Bengkulu' }})" readonly>
                            <small class="text-muted">Pangkalan TPI terkunci sesuai penugasan akun Anda.</small>
                        @else
                            <select name="tpi_id" id="tpi_id" class="form-select @error('tpi_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Pangkalan TPI --</option>
                                @foreach($tpiList as $tpi)
                                    <option value="{{ $tpi->id }}" {{ (old('tpi_id', $pendaratan->tpi_id) == $tpi->id) ? 'selected' : '' }}>
                                        {{ $tpi->nama_tpi }} ({{ $tpi->wilayah->nama_kabupaten_kota ?? 'Bengkulu' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('tpi_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>

                    <!-- Tanggal Pendaratan -->
                    <div class="col-md-6">
                        <label for="tanggal_pendaratan" class="form-label fw-semibold">Tanggal Pendaratan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pendaratan" id="tanggal_pendaratan"
                               class="form-control @error('tanggal_pendaratan') is-invalid @enderror"
                               value="{{ old('tanggal_pendaratan', $pendaratan->tanggal_pendaratan?->format('Y-m-d')) }}"
                               max="{{ date('Y-m-d') }}" required>
                        <small class="text-muted">Tanggal kapal sandar dan membongkar muatan (maks. hari ini).</small>
                        @error('tanggal_pendaratan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Kapal Nelayan -->
                    <div class="col-md-4">
                        <label for="kapal_id" class="form-label fw-semibold">Armada Kapal</label>
                        <select name="kapal_id" id="kapal_id" class="form-select @error('kapal_id') is-invalid @enderror">
                            <option value="">-- Pilih Kapal (Opsional) --</option>
                            @foreach($kapalList as $kapal)
                                <option value="{{ $kapal->id }}" {{ old('kapal_id', $pendaratan->kapal_id) == $kapal->id ? 'selected' : '' }}>
                                    {{ $kapal->nama_kapal }} ({{ $kapal->gt ? $kapal->gt . ' GT' : 'Non-mesin' }})
                                </option>
                            @endforeach
                        </select>
                        @error('kapal_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Nelayan / Nakhoda -->
                    <div class="col-md-4">
                        <label for="nelayan_id" class="form-label fw-semibold">Nakhoda / Nelayan</label>
                        <select name="nelayan_id" id="nelayan_id" class="form-select @error('nelayan_id') is-invalid @enderror">
                            <option value="">-- Pilih Nelayan (Opsional) --</option>
                            @foreach($nelayanList as $nelayan)
                                <option value="{{ $nelayan->id }}" {{ old('nelayan_id', $pendaratan->nelayan_id) == $nelayan->id ? 'selected' : '' }}>
                                    {{ $nelayan->nama }} ({{ $nelayan->kode_nelayan }})
                                </option>
                            @endforeach
                        </select>
                        @error('nelayan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Alat Tangkap -->
                    <div class="col-md-4">
                        <label for="alat_tangkap_id" class="form-label fw-semibold">Alat Penangkapan Ikan (API)</label>
                        <select name="alat_tangkap_id" id="alat_tangkap_id" class="form-select @error('alat_tangkap_id') is-invalid @enderror">
                            <option value="">-- Pilih Alat Tangkap (Opsional) --</option>
                            @foreach($alatTangkapList as $alat)
                                <option value="{{ $alat->id }}" {{ old('alat_tangkap_id', $pendaratan->alat_tangkap_id) == $alat->id ? 'selected' : '' }}>
                                    {{ $alat->nama_alat }} ({{ $alat->jenis_kategori ?? 'Umum' }})
                                </option>
                            @endforeach
                        </select>
                        @error('alat_tangkap_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Rincian Hasil Tangkapan (Dynamic Items) -->
        <div class="card card-custom mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-basket text-primary"></i> 2. Rincian Komoditas Tangkapan Ikan
                </h6>
                <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" id="btnTambahBaris">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Jenis Ikan
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" id="itemsTable">
                        <thead class="table-light">
                            <tr class="text-secondary small">
                                <th style="width: 40px;" class="text-center">No</th>
                                <th style="min-width: 250px;">Komoditas Ikan <span class="text-danger">*</span></th>
                                <th style="width: 170px;" class="text-end">Volume (Kg) <span class="text-danger">*</span></th>
                                <th style="width: 180px;" class="text-end">Harga / Kg (Rp)</th>
                                <th style="width: 200px;" class="text-end">Subtotal (Rp)</th>
                                <th style="min-width: 180px;">Keterangan Mutu / Catatan</th>
                                <th style="width: 60px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            {{-- Rows will be injected by JavaScript --}}
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="2" class="text-end text-uppercase pe-3">Grand Total :</td>
                                <td class="text-end text-primary" id="grandTotalVolume">0,00 Kg</td>
                                <td></td>
                                <td class="text-end text-success fs-6" id="grandTotalNilai">Rp 0</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light py-2 px-3 small text-muted d-flex justify-content-between">
                <span><i class="bi bi-info-circle me-1"></i> Subtotal nilai dihitung otomatis: Volume (Kg) &times; Harga / Kg.</span>
                <span id="rowCountInfo">0 baris komoditas</span>
            </div>
        </div>

        <!-- Form Submission Actions -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-5">
            <a href="{{ route('pendaratan.show', $pendaratan) }}" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-1"></i> Batal & Kembali
            </a>
            <div class="d-flex gap-2">
                <button type="submit" name="action" value="draft" class="btn btn-secondary px-4">
                    <i class="bi bi-save me-1"></i> Simpan sebagai Draft
                </button>
                <button type="submit" name="action" value="diajukan" class="btn btn-primary px-4 shadow-sm">
                    <i class="bi bi-send-check me-1"></i> Simpan & Ajukan Verifikasi
                </button>
            </div>
        </div>
    </form>

</div>

<!-- Options template for Species dropdown -->
<template id="speciesOptionsTemplate">
    <option value="">-- Pilih Jenis Ikan --</option>
    @foreach($jenisIkanList as $ikan)
        <option value="{{ $ikan->id }}">
            {{ $ikan->nama_resmi }} {{ $ikan->nama_lokal ? "({$ikan->nama_lokal})" : '' }} &bull; {{ $ikan->kategori ?? 'Laut' }}
        </option>
    @endforeach
</template>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const itemsBody = document.getElementById('itemsBody');
    const btnTambahBaris = document.getElementById('btnTambahBaris');
    const grandTotalVolumeEl = document.getElementById('grandTotalVolume');
    const grandTotalNilaiEl = document.getElementById('grandTotalNilai');
    const rowCountInfoEl = document.getElementById('rowCountInfo');
    const speciesOptionsHtml = document.getElementById('speciesOptionsTemplate').innerHTML;

    // Determine initial items: old input if validation failed, else existing DB items
    @php
        $existingItems = $pendaratan->detailPendaratan->map(function($d) {
            return [
                'jenis_ikan_id' => $d->jenis_ikan_id,
                'volume_kg' => (float) $d->volume_kg,
                'harga_per_kg' => (float) $d->harga_per_kg,
                'keterangan' => $d->keterangan,
            ];
        });
    @endphp
    const initialItems = @json(old('items', $existingItems));

    let rowIndex = 0;

    function addRow(data = {}) {
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.dataset.index = rowIndex;

        const volumeVal = data.volume_kg !== undefined ? data.volume_kg : '';
        const hargaVal = data.harga_per_kg !== undefined ? data.harga_per_kg : '';
        const keteranganVal = data.keterangan !== undefined ? data.keterangan : '';
        const jenisId = data.jenis_ikan_id !== undefined ? String(data.jenis_ikan_id) : '';

        tr.innerHTML = `
            <td class="text-center row-number text-muted small"></td>
            <td>
                <select name="items[${rowIndex}][jenis_ikan_id]" class="form-select form-select-sm select-jenis" required>
                    ${speciesOptionsHtml}
                </select>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" step="0.01" min="0.01" name="items[${rowIndex}][volume_kg]"
                           class="form-control text-end input-volume" placeholder="0.00" value="${volumeVal}" required>
                    <span class="input-group-text">Kg</span>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    <input type="number" step="100" min="0" name="items[${rowIndex}][harga_per_kg]"
                           class="form-control text-end input-harga" placeholder="0" value="${hargaVal}">
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    <input type="text" class="form-control text-end bg-light input-subtotal fw-semibold" readonly value="0">
                </div>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][keterangan]"
                       class="form-control form-select-sm" placeholder="Catatan mutu, kondisi, dll" value="${keteranganVal}">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row" title="Hapus Baris">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        itemsBody.appendChild(tr);

        // Select the chosen species
        if (jenisId) {
            const selectEl = tr.querySelector('.select-jenis');
            if (selectEl) {
                selectEl.value = jenisId;
            }
        }

        rowIndex++;
        updateRowNumbers();
        recalculateRow(tr);
        recalculateTotals();
    }

    function updateRowNumbers() {
        const rows = itemsBody.querySelectorAll('tr.item-row');
        rows.forEach((row, idx) => {
            const numCell = row.querySelector('.row-number');
            if (numCell) numCell.textContent = idx + 1;
        });
        rowCountInfoEl.textContent = `${rows.length} baris komoditas`;
    }

    function recalculateRow(row) {
        const volumeInput = row.querySelector('.input-volume');
        const hargaInput = row.querySelector('.input-harga');
        const subtotalInput = row.querySelector('.input-subtotal');

        const volume = parseFloat(volumeInput.value) || 0;
        const harga = parseFloat(hargaInput.value) || 0;
        const subtotal = volume * harga;

        subtotalInput.value = formatNumber(subtotal);
    }

    function recalculateTotals() {
        let totalVol = 0;
        let totalNilai = 0;

        const rows = itemsBody.querySelectorAll('tr.item-row');
        rows.forEach(row => {
            const volume = parseFloat(row.querySelector('.input-volume').value) || 0;
            const harga = parseFloat(row.querySelector('.input-harga').value) || 0;
            totalVol += volume;
            totalNilai += (volume * harga);
        });

        grandTotalVolumeEl.textContent = `${formatNumber(totalVol, 2)} Kg`;
        grandTotalNilaiEl.textContent = `Rp ${formatNumber(totalNilai, 0)}`;
    }

    function formatNumber(val, decimals = 0) {
        return Number(val).toLocaleString('id-ID', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
    }

    // Event delegation
    itemsBody.addEventListener('input', function (e) {
        if (e.target.classList.contains('input-volume') || e.target.classList.contains('input-harga')) {
            const row = e.target.closest('tr.item-row');
            if (row) {
                recalculateRow(row);
                recalculateTotals();
            }
        }
    });

    itemsBody.addEventListener('click', function (e) {
        const deleteBtn = e.target.closest('.btn-delete-row');
        if (deleteBtn) {
            const rows = itemsBody.querySelectorAll('tr.item-row');
            if (rows.length <= 1) {
                alert('Minimal satu baris rincian komoditas ikan wajib diisi.');
                return;
            }
            const row = deleteBtn.closest('tr.item-row');
            if (row) {
                row.remove();
                updateRowNumbers();
                recalculateTotals();
            }
        }
    });

    btnTambahBaris.addEventListener('click', function () {
        addRow();
    });

    // Populate initial items
    if (Array.isArray(initialItems) && initialItems.length > 0) {
        initialItems.forEach(item => addRow(item));
    } else {
        addRow();
    }
});
</script>
@endpush
