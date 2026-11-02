@extends('layouts.app')

@section('title', 'Modul Laporan & Rekapitulasi')

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1" style="font-size: 0.75rem;">
                    <i class="bi bi-file-earmark-bar-graph me-1"></i> Modul Analitikal
                </span>
                <span class="text-muted small">Standar DKP Provinsi Bengkulu & BPS PP-TPI</span>
            </div>
            <h3 class="fw-bold text-dark mb-0">Laporan & Rekapitulasi Produksi Perikanan</h3>
            <p class="text-muted small mb-0">Agregat statistik pendaratan ikan terverifikasi di Tempat Pelelangan Ikan se-Provinsi Bengkulu.</p>
        </div>

        <!-- Action Buttons (Export & Print) -->
        <div class="d-flex align-items-center gap-2">
            <a id="btnExportExcel" href="{{ route('laporan.export', array_merge(request()->all(), ['tipe' => $activeTab])) }}" class="btn btn-outline-success btn-sm px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-spreadsheet-fill text-success fs-6"></i>
                <span>Ekspor Excel</span>
            </a>
            <a id="btnCetakDoc" href="{{ route('laporan.cetak', array_merge(request()->all(), ['tipe' => $activeTab])) }}" target="_blank" class="btn btn-primary btn-sm px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-printer-fill fs-6"></i>
                <span>Cetak Dokumen Resmi</span>
            </a>
        </div>
    </div>

    <!-- Filter Control Card -->
    <div class="card card-custom mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-funnel-fill text-primary"></i>
                <span>Filter Parameter Rekapitulasi</span>
            </div>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('laporan.index') }}" id="filterForm">
                <input type="hidden" name="tab" id="activeTabInput" value="{{ $activeTab }}">

                <div class="row g-3">
                    <!-- Tahun -->
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="tahunSelect" class="form-label small fw-semibold text-secondary">Tahun</label>
                        <select name="tahun" id="tahunSelect" class="form-select form-select-sm">
                            @foreach($availableYears as $yr)
                                <option value="{{ $yr }}" {{ $filters['tahun'] == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Periode Tipe -->
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="periodeTipeSelect" class="form-label small fw-semibold text-secondary">Tipe Periode</label>
                        <select name="periode_tipe" id="periodeTipeSelect" class="form-select form-select-sm" onchange="togglePeriodInputs()">
                            <option value="semua" {{ $filters['periode_tipe'] === 'semua' ? 'selected' : '' }}>Semua (Setahun Penuh)</option>
                            <option value="bulanan" {{ $filters['periode_tipe'] === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                            <option value="triwulanan" {{ $filters['periode_tipe'] === 'triwulanan' ? 'selected' : '' }}>Triwulanan</option>
                        </select>
                    </div>

                    <!-- Bulan (shown if Bulanan) -->
                    <div class="col-12 col-sm-6 col-md-2" id="bulanWrapper" style="{{ $filters['periode_tipe'] === 'bulanan' ? '' : 'display: none;' }}">
                        <label for="bulanSelect" class="form-label small fw-semibold text-secondary">Pilih Bulan</label>
                        <select name="bulan" id="bulanSelect" class="form-select form-select-sm">
                            @php
                                $bulanList = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                            @endphp
                            @foreach($bulanList as $mNum => $mLabel)
                                <option value="{{ $mNum }}" {{ ($filters['bulan'] ?? date('n')) == $mNum ? 'selected' : '' }}>{{ $mLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Triwulan (shown if Triwulanan) -->
                    <div class="col-12 col-sm-6 col-md-2" id="triwulanWrapper" style="{{ $filters['periode_tipe'] === 'triwulanan' ? '' : 'display: none;' }}">
                        <label for="triwulanSelect" class="form-label small fw-semibold text-secondary">Pilih Triwulan</label>
                        <select name="triwulan" id="triwulanSelect" class="form-select form-select-sm">
                            <option value="1" {{ ($filters['triwulan'] ?? 1) == 1 ? 'selected' : '' }}>Triwulan I (Jan - Mar)</option>
                            <option value="2" {{ ($filters['triwulan'] ?? 1) == 2 ? 'selected' : '' }}>Triwulan II (Apr - Jun)</option>
                            <option value="3" {{ ($filters['triwulan'] ?? 1) == 3 ? 'selected' : '' }}>Triwulan III (Jul - Sep)</option>
                            <option value="4" {{ ($filters['triwulan'] ?? 1) == 4 ? 'selected' : '' }}>Triwulan IV (Okt - Des)</option>
                        </select>
                    </div>

                    <!-- Filter TPI -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="tpiSelect" class="form-label small fw-semibold text-secondary">Lokasi TPI</label>
                        @if($filters['is_admin_tpi'])
                            <input type="text" class="form-control form-control-sm bg-light" value="{{ $filters['selected_tpi']?->nama_tpi ?? 'TPI Anda' }}" readonly>
                            <input type="hidden" name="tpi_id" value="{{ $filters['tpi_id'] }}">
                        @else
                            <select name="tpi_id" id="tpiSelect" class="form-select form-select-sm">
                                <option value="">-- Semua TPI di Bengkulu --</option>
                                @foreach($tpis as $tpiItem)
                                    <option value="{{ $tpiItem->id }}" {{ $filters['tpi_id'] == $tpiItem->id ? 'selected' : '' }}>
                                        {{ $tpiItem->nama_tpi }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <!-- Filter Kategori Ikan -->
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="kategoriSelect" class="form-label small fw-semibold text-secondary">Kategori Ikan</label>
                        <select name="kategori_ikan" id="kategoriSelect" class="form-select form-select-sm">
                            <option value="">-- Semua Kategori --</option>
                            @foreach($kategoriList as $kat)
                                <option value="{{ $kat }}" {{ $filters['kategori_ikan'] === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Action Buttons -->
                    <div class="col-12 col-md-1 d-flex align-items-end gap-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100 py-1.5" title="Terapkan Filter">
                            <i class="bi bi-search"></i>
                        </button>
                        <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary btn-sm py-1.5" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </div>

                <!-- Active Filter Indicator Badge -->
                <div class="mt-3 pt-3 border-top d-flex flex-wrap align-items-center gap-2">
                    <span class="text-muted small">Filter Aktif:</span>
                    <span class="badge bg-secondary-subtle text-dark border">
                        <i class="bi bi-calendar-event me-1"></i>{{ $filters['periode_label'] }}
                    </span>
                    @if($filters['tpi_id'] && $filters['selected_tpi'])
                        <span class="badge bg-info-subtle text-info-emphasis border">
                            <i class="bi bi-geo-alt me-1"></i>{{ $filters['selected_tpi']->nama_tpi }}
                        </span>
                    @else
                        <span class="badge bg-light text-muted border">Semua TPI Provinsi</span>
                    @endif
                    @if($filters['kategori_ikan'])
                        <span class="badge bg-warning-subtle text-warning-emphasis border">
                            <i class="bi bi-tag me-1"></i>{{ $filters['kategori_ikan'] }}
                        </span>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Report Tabs Navigation -->
    <div class="card card-custom">
        <div class="card-header bg-white p-0 border-bottom">
            <ul class="nav nav-tabs card-header-tabs m-0 px-3 pt-2" id="reportTabs" role="tablist">
                <!-- Tab 1: Rekap per TPI -->
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold px-4 py-3 {{ $activeTab === 'tpi' ? 'active text-primary border-bottom-primary' : 'text-secondary' }}"
                            id="tab-tpi-btn" data-bs-toggle="tab" data-bs-target="#tab-tpi" type="button" role="tab"
                            aria-controls="tab-tpi" aria-selected="{{ $activeTab === 'tpi' ? 'true' : 'false' }}"
                            onclick="switchReportTab('tpi')">
                        <i class="bi bi-building me-1"></i> Rekapitulasi per TPI
                    </button>
                </li>

                <!-- Tab 2: Rekap per Komoditas Ikan -->
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold px-4 py-3 {{ $activeTab === 'ikan' ? 'active text-primary border-bottom-primary' : 'text-secondary' }}"
                            id="tab-ikan-btn" data-bs-toggle="tab" data-bs-target="#tab-ikan" type="button" role="tab"
                            aria-controls="tab-ikan" aria-selected="{{ $activeTab === 'ikan' ? 'true' : 'false' }}"
                            onclick="switchReportTab('ikan')">
                        <i class="bi bi-fingerprint me-1"></i> Rekapitulasi Komoditas Ikan
                    </button>
                </li>

                <!-- Tab 3: Standar DKP Bengkulu (per Kab/Kota) -->
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold px-4 py-3 {{ $activeTab === 'dkp' ? 'active text-primary border-bottom-primary' : 'text-secondary' }}"
                            id="tab-dkp-btn" data-bs-toggle="tab" data-bs-target="#tab-dkp" type="button" role="tab"
                            aria-controls="tab-dkp" aria-selected="{{ $activeTab === 'dkp' ? 'true' : 'false' }}"
                            onclick="switchReportTab('dkp')">
                        <i class="bi bi-journal-bookmark me-1"></i> Format Publikasi DKP Bengkulu
                    </button>
                </li>

                <!-- Tab 4: Triwulanan (Standar BPS PP-TPI) -->
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold px-4 py-3 {{ $activeTab === 'triwulan' ? 'active text-primary border-bottom-primary' : 'text-secondary' }}"
                            id="tab-triwulan-btn" data-bs-toggle="tab" data-bs-target="#tab-triwulan" type="button" role="tab"
                            aria-controls="tab-triwulan" aria-selected="{{ $activeTab === 'triwulan' ? 'true' : 'false' }}"
                            onclick="switchReportTab('triwulan')">
                        <i class="bi bi-calendar3 me-1"></i> Format Triwulanan (BPS PP-TPI)
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="reportTabsContent">

                <!-- ===================================================== -->
                <!-- TAB 1: REKAPITULASI PER TPI                           -->
                <!-- ===================================================== -->
                <div class="tab-pane fade {{ $activeTab === 'tpi' ? 'show active' : '' }}" id="tab-tpi" role="tabpanel" aria-labelledby="tab-tpi-btn">
                    <div class="alert alert-light border d-flex flex-wrap gap-3 small mb-4">
                        <span><strong>Tahun:</strong> {{ $filters['tahun'] }}</span>
                        <span><strong>Periode:</strong> {{ $filters['periode_label'] }}</span>
                        <span><strong>Tanggal laporan:</strong> {{ $reportGeneratedAt->translatedFormat('d F Y') }}</span>
                    </div>
                    <!-- Quick KPI Pill Header -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="p-3 rounded-3 bg-light border">
                                <div class="text-muted small fw-semibold">Jumlah TPI Terdata</div>
                                <div class="fs-4 fw-bold text-dark">{{ $rekapTpiData['totals']['count'] }} Lokasi</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="p-3 rounded-3 bg-light border">
                                <div class="text-muted small fw-semibold">Total Frekuensi Pendaratan</div>
                                <div class="fs-4 fw-bold text-dark">{{ number_format($rekapTpiData['totals']['frekuensi_pendaratan'], 0, ',', '.') }} Trip</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="p-3 rounded-3 bg-light border">
                                <div class="text-muted small fw-semibold">Total Volume Tangkapan</div>
                                <div class="fs-4 fw-bold text-primary">{{ number_format($rekapTpiData['totals']['total_volume'], 2, ',', '.') }} Kg</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="p-3 rounded-3 bg-light border">
                                <div class="text-muted small fw-semibold">Total Nilai Produksi</div>
                                <div class="fs-4 fw-bold text-success">Rp {{ number_format($rekapTpiData['totals']['total_nilai'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabular Data -->
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle mb-0">
                            <thead class="table-light text-secondary">
                                <tr class="text-center align-middle">
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 12%;">Kode TPI</th>
                                    <th style="width: 20%;" class="text-start">Nama TPI</th>
                                    <th style="width: 15%;" class="text-start">Kabupaten / Kota</th>
                                    <th style="width: 12%;">Frekuensi Pendaratan</th>
                                    <th style="width: 12%;">Total Volume (Kg)</th>
                                    <th style="width: 12%;">Rata-rata / Trip (Kg)</th>
                                    <th style="width: 12%;">Total Nilai (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rekapTpiData['rows'] as $index => $row)
                                    <tr>
                                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                                        <td class="text-center font-monospace fw-semibold">{{ $row['kode_tpi'] }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $row['nama_tpi'] }}</div>
                                        </td>
                                        <td>{{ $row['kabupaten_kota'] }}</td>
                                        <td class="text-center fw-semibold">{{ number_format($row['frekuensi_pendaratan'], 0, ',', '.') }}</td>
                                        <td class="text-end fw-semibold">{{ number_format($row['total_volume'], 2, ',', '.') }}</td>
                                        <td class="text-end text-muted">{{ number_format($row['rata_rata_per_trip'], 2, ',', '.') }}</td>
                                        <td class="text-end fw-bold text-success">Rp {{ number_format($row['total_nilai'], 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                            Tidak ditemukan data pendaratan terverifikasi untuk filter yang dipilih.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-primary fw-bold">
                                <tr>
                                    <td colspan="4" class="text-center text-uppercase">Grand Total / Rekapitulasi Provinsi</td>
                                    <td class="text-center">{{ number_format($rekapTpiData['totals']['frekuensi_pendaratan'], 0, ',', '.') }} Trip</td>
                                    <td class="text-end">{{ number_format($rekapTpiData['totals']['total_volume'], 2, ',', '.') }} Kg</td>
                                    <td class="text-end">{{ number_format($rekapTpiData['totals']['rata_rata_per_trip'], 2, ',', '.') }} Kg</td>
                                    <td class="text-end text-success">Rp {{ number_format($rekapTpiData['totals']['total_nilai'], 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- ===================================================== -->
                <!-- TAB 2: REKAPITULASI KOMODITAS IKAN                    -->
                <!-- ===================================================== -->
                <div class="tab-pane fade {{ $activeTab === 'ikan' ? 'show active' : '' }}" id="tab-ikan" role="tabpanel" aria-labelledby="tab-ikan-btn">
                    <div class="alert alert-light border d-flex flex-wrap gap-3 small mb-4">
                        <span><strong>Tahun:</strong> {{ $filters['tahun'] }}</span>
                        <span><strong>Periode:</strong> {{ $filters['periode_label'] }}</span>
                        <span><strong>Tanggal laporan:</strong> {{ $reportGeneratedAt->translatedFormat('d F Y') }}</span>
                    </div>
                    <!-- Quick KPI Pill Header -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="p-3 rounded-3 bg-light border">
                                <div class="text-muted small fw-semibold">Jumlah Spesies Ikan</div>
                                <div class="fs-4 fw-bold text-dark">{{ $rekapIkanData['totals']['count'] }} Spesies</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="p-3 rounded-3 bg-light border">
                                <div class="text-muted small fw-semibold">Total Volume Tangkapan</div>
                                <div class="fs-4 fw-bold text-primary">{{ number_format($rekapIkanData['totals']['volume_kg'], 2, ',', '.') }} Kg</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="p-3 rounded-3 bg-light border">
                                <div class="text-muted small fw-semibold">Rata-rata Harga Pasar / Kg</div>
                                <div class="fs-4 fw-bold text-warning">Rp {{ number_format($rekapIkanData['totals']['rata_rata_harga'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="p-3 rounded-3 bg-light border">
                                <div class="text-muted small fw-semibold">Total Nilai Produksi Ikan</div>
                                <div class="fs-4 fw-bold text-success">Rp {{ number_format($rekapIkanData['totals']['total_nilai'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Table Data -->
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle mb-0">
                            <thead class="table-light text-secondary">
                                <tr class="text-center align-middle">
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 10%;">Kode Ikan</th>
                                    <th style="width: 22%;" class="text-start">Nama Komoditas Ikan</th>
                                    <th style="width: 15%;" class="text-start">Kategori</th>
                                    <th style="width: 12%;">Volume (Kg)</th>
                                    <th style="width: 10%;">Kontribusi (%)</th>
                                    <th style="width: 12%;">Rata-rata Harga/Kg</th>
                                    <th style="width: 14%;">Total Nilai (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rekapIkanData['rows'] as $index => $row)
                                    <tr>
                                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                                        <td class="text-center font-monospace">{{ $row['kode_ikan'] }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $row['nama_resmi'] }}</div>
                                            @if($row['nama_lokal'] && $row['nama_lokal'] !== '-')
                                                <small class="text-muted fst-italic">{{ $row['nama_lokal'] }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge 
                                                {{ $row['kategori'] === 'Pelagis Besar' ? 'bg-primary-subtle text-primary' : '' }}
                                                {{ $row['kategori'] === 'Pelagis Kecil' ? 'bg-info-subtle text-info-emphasis' : '' }}
                                                {{ $row['kategori'] === 'Demersal' ? 'bg-success-subtle text-success' : '' }}
                                                {{ $row['kategori'] === 'Non-Ikan' ? 'bg-warning-subtle text-warning-emphasis' : '' }}
                                                {{ !in_array($row['kategori'], ['Pelagis Besar', 'Pelagis Kecil', 'Demersal', 'Non-Ikan']) ? 'bg-secondary-subtle text-secondary' : '' }}
                                                border">
                                                {{ $row['kategori'] }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-semibold">{{ number_format($row['volume_kg'], 2, ',', '.') }}</td>
                                        <td class="text-center">
                                            <div class="fw-semibold">{{ number_format($row['persentase'], 2, ',', '.') }}%</div>
                                            <div class="progress" style="height: 4px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ min(100, $row['persentase']) }}%"></div>
                                            </div>
                                        </td>
                                        <td class="text-end text-muted">Rp {{ number_format($row['rata_rata_harga'], 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold text-success">Rp {{ number_format($row['total_nilai'], 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                            Tidak ditemukan data tangkapan komoditas ikan pada periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-primary fw-bold">
                                <tr>
                                    <td colspan="4" class="text-center text-uppercase">Total Keseluruhan Komoditas</td>
                                    <td class="text-end">{{ number_format($rekapIkanData['totals']['volume_kg'], 2, ',', '.') }} Kg</td>
                                    <td class="text-center">100.00%</td>
                                    <td class="text-end">Rp {{ number_format($rekapIkanData['totals']['rata_rata_harga'], 0, ',', '.') }}</td>
                                    <td class="text-end text-success">Rp {{ number_format($rekapIkanData['totals']['total_nilai'], 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- ===================================================== -->
                <!-- TAB 3: STANDAR FORMAT PUBLIKASI DKP BENGKULU          -->
                <!-- ===================================================== -->
                <div class="tab-pane fade {{ $activeTab === 'dkp' ? 'show active' : '' }}" id="tab-dkp" role="tabpanel" aria-labelledby="tab-dkp-btn">
                    <!-- Quick Summary Banner -->
                    <div class="alert alert-primary border-0 shadow-sm d-flex align-items-center gap-3 mb-4">
                        <i class="bi bi-info-circle-fill fs-4 text-primary"></i>
                        <div class="small">
                            <strong>Format Publikasi Resmi DKP Provinsi Bengkulu:</strong> Menyajikan kompilasi produksi perikanan tangkap laut menurut 7 Kabupaten/Kota pesisir di sepanjang garis pantai Samudera Hindia Provinsi Bengkulu.
                        </div>
                    </div>

                    <!-- Table Data -->
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle mb-0">
                            <thead class="table-light text-secondary">
                                <tr class="text-center align-middle">
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 20%;" class="text-start">Kabupaten / Kota Pesisir</th>
                                    <th style="width: 10%;">Kode Wilayah</th>
                                    <th style="width: 10%;">Jumlah TPI</th>
                                    <th style="width: 12%;">Frekuensi Trip</th>
                                    <th style="width: 13%;">Total Volume (Kg)</th>
                                    <th style="width: 14%;">Total Nilai (Rp)</th>
                                    <th style="width: 16%;" class="text-start">Komoditas Unggulan</th>
                                    <th style="width: 10%;">Kontribusi (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rekapDkpData['rows'] as $index => $row)
                                    <tr>
                                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $row['nama_kabupaten_kota'] }}</div>
                                        </td>
                                        <td class="text-center font-monospace text-muted">{{ $row['kode_wilayah'] }}</td>
                                        <td class="text-center">{{ $row['jumlah_tpi'] }} Unit</td>
                                        <td class="text-center fw-semibold">{{ number_format($row['frekuensi_trip'], 0, ',', '.') }}</td>
                                        <td class="text-end fw-semibold">{{ number_format($row['total_volume'], 2, ',', '.') }}</td>
                                        <td class="text-end fw-bold text-success">Rp {{ number_format($row['total_nilai'], 0, ',', '.') }}</td>
                                        <td>
                                            @if($row['komoditas_unggulan'] !== '-')
                                                <span class="badge bg-primary text-white"><i class="bi bi-star-fill me-1"></i>{{ $row['komoditas_unggulan'] }}</span>
                                            @else
                                                <span class="text-muted fst-italic">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-semibold">{{ number_format($row['kontribusi_persen'], 2, ',', '.') }}%</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            Tidak ada data wilayah pesisir.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-primary fw-bold">
                                <tr>
                                    <td colspan="3" class="text-center text-uppercase">Total Provinsi Bengkulu</td>
                                    <td class="text-center">{{ $rekapDkpData['totals']['jumlah_tpi'] }} Unit TPI</td>
                                    <td class="text-center">{{ number_format($rekapDkpData['totals']['frekuensi_trip'], 0, ',', '.') }} Trip</td>
                                    <td class="text-end">{{ number_format($rekapDkpData['totals']['total_volume'], 2, ',', '.') }} Kg</td>
                                    <td class="text-end text-success">Rp {{ number_format($rekapDkpData['totals']['total_nilai'], 0, ',', '.') }}</td>
                                    <td></td>
                                    <td class="text-center">100.00%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- ===================================================== -->
                <!-- TAB 4: FORMAT TRIWULANAN (STANDAR BPS PP-TPI)         -->
                <!-- ===================================================== -->
                <div class="tab-pane fade {{ $activeTab === 'triwulan' ? 'show active' : '' }}" id="tab-triwulan" role="tabpanel" aria-labelledby="tab-triwulan-btn">
                    <!-- Informational Banner -->
                    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-3 mb-4">
                        <i class="bi bi-calendar-check-fill fs-4 text-info"></i>
                        <div class="small">
                            <strong>Standar Survei Perusahaan Perikanan / Pendaratan Ikan (PP-TPI BPS):</strong> Data diagregasikan menurut kuartal periode (TW I: Jan-Mar, TW II: Apr-Jun, TW III: Jul-Sep, TW IV: Okt-Des) untuk sinkronisasi pelaporan statistik nasional.
                        </div>
                    </div>

                    <!-- Table Data -->
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.88rem;">
                            <thead class="table-light text-secondary">
                                <tr class="text-center align-middle">
                                    <th rowspan="2" style="width: 4%;">No</th>
                                    <th rowspan="2" style="width: 18%;" class="text-start">Nama TPI & Lokasi</th>
                                    <th colspan="2" class="border-start">Triwulan I</th>
                                    <th colspan="2" class="border-start">Triwulan II</th>
                                    <th colspan="2" class="border-start">Triwulan III</th>
                                    <th colspan="2" class="border-start">Triwulan IV</th>
                                    <th colspan="2" class="border-start bg-primary-subtle text-primary">Total Tahunan</th>
                                </tr>
                                <tr class="text-center small">
                                    <th class="border-start">Volume (Kg)</th>
                                    <th>Nilai (Rp)</th>
                                    <th class="border-start">Volume (Kg)</th>
                                    <th>Nilai (Rp)</th>
                                    <th class="border-start">Volume (Kg)</th>
                                    <th>Nilai (Rp)</th>
                                    <th class="border-start">Volume (Kg)</th>
                                    <th>Nilai (Rp)</th>
                                    <th class="border-start bg-primary-subtle text-primary">Volume (Kg)</th>
                                    <th class="bg-primary-subtle text-primary">Nilai (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rekapTriwulanData['rows'] as $index => $row)
                                    <tr>
                                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $row['nama_tpi'] }}</div>
                                            <small class="text-muted">{{ $row['kabupaten_kota'] }}</small>
                                        </td>
                                        <!-- TW 1 -->
                                        <td class="text-end border-start">{{ number_format($row['tw1_volume'], 2, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($row['tw1_nilai'], 0, ',', '.') }}</td>
                                        <!-- TW 2 -->
                                        <td class="text-end border-start">{{ number_format($row['tw2_volume'], 2, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($row['tw2_nilai'], 0, ',', '.') }}</td>
                                        <!-- TW 3 -->
                                        <td class="text-end border-start">{{ number_format($row['tw3_volume'], 2, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($row['tw3_nilai'], 0, ',', '.') }}</td>
                                        <!-- TW 4 -->
                                        <td class="text-end border-start">{{ number_format($row['tw4_volume'], 2, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($row['tw4_nilai'], 0, ',', '.') }}</td>
                                        <!-- Total -->
                                        <td class="text-end border-start fw-bold bg-light">{{ number_format($row['total_volume'], 2, ',', '.') }}</td>
                                        <td class="text-end fw-bold bg-light text-success">Rp {{ number_format($row['total_nilai'], 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center py-4 text-muted">
                                            Tidak ditemukan data triwulanan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-primary fw-bold">
                                <tr>
                                    <td colspan="2" class="text-center text-uppercase">Total Produksi Provinsi</td>
                                    <td class="text-end border-start">{{ number_format($rekapTriwulanData['totals']['tw1_volume'], 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($rekapTriwulanData['totals']['tw1_nilai'], 0, ',', '.') }}</td>
                                    <td class="text-end border-start">{{ number_format($rekapTriwulanData['totals']['tw2_volume'], 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($rekapTriwulanData['totals']['tw2_nilai'], 0, ',', '.') }}</td>
                                    <td class="text-end border-start">{{ number_format($rekapTriwulanData['totals']['tw3_volume'], 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($rekapTriwulanData['totals']['tw3_nilai'], 0, ',', '.') }}</td>
                                    <td class="text-end border-start">{{ number_format($rekapTriwulanData['totals']['tw4_volume'], 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($rekapTriwulanData['totals']['tw4_nilai'], 0, ',', '.') }}</td>
                                    <td class="text-end border-start">{{ number_format($rekapTriwulanData['totals']['total_volume'], 2, ',', '.') }}</td>
                                    <td class="text-end text-success">Rp {{ number_format($rekapTriwulanData['totals']['total_nilai'], 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function togglePeriodInputs() {
    const tipe = document.getElementById('periodeTipeSelect').value;
    const bulanWrap = document.getElementById('bulanWrapper');
    const triwulanWrap = document.getElementById('triwulanWrapper');

    if (tipe === 'bulanan') {
        bulanWrap.style.display = 'block';
        triwulanWrap.style.display = 'none';
    } else if (tipe === 'triwulanan') {
        bulanWrap.style.display = 'none';
        triwulanWrap.style.display = 'block';
    } else {
        bulanWrap.style.display = 'none';
        triwulanWrap.style.display = 'none';
    }
}

function switchReportTab(tabName) {
    document.getElementById('activeTabInput').value = tabName;

    // Update export and print button URLs with active tab
    const exportBtn = document.getElementById('btnExportExcel');
    const printBtn = document.getElementById('btnCetakDoc');

    if (exportBtn) {
        const url = new URL(exportBtn.href, window.location.origin);
        url.searchParams.set('tipe', tabName);
        exportBtn.href = url.pathname + url.search;
    }

    if (printBtn) {
        const url = new URL(printBtn.href, window.location.origin);
        url.searchParams.set('tipe', tabName);
        printBtn.href = url.pathname + url.search;
    }
}
</script>
@endpush
