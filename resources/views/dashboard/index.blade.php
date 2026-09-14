@extends('layouts.app')

@section('title', 'Dashboard Interaktif & Analitik')

@section('content')
<div class="container-fluid p-0">

    <!-- Welcome Banner -->
    <div class="card border-0 text-white mb-4 shadow-sm" style="background: linear-gradient(135deg, #0B3C5D 0%, #174d75 60%, #1D2731 100%); border-radius: 14px; overflow: hidden;">
        <div class="card-body p-4 position-relative">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-white text-dark fw-bold px-2 py-1" style="font-size: 0.75rem;">
                            {{ $user->role_label }}
                        </span>
                        @if($isAdminTpi && $assignedTpi)
                            <span class="badge bg-info-subtle text-info-emphasis px-2 py-1" style="font-size: 0.75rem;">
                                <i class="bi bi-geo-alt-fill me-1"></i>{{ $assignedTpi->nama_tpi }}
                            </span>
                        @endif
                    </div>
                    <h3 class="fw-bold mb-1">Selamat Datang, {{ $user->name }}!</h3>
                    <p class="text-white-50 mb-3" style="font-size: 0.92rem;">
                        @if($isAdminTpi)
                            Anda bertugas sebagai Admin Operasional pada <strong>{{ $assignedTpi->nama_tpi ?? 'TPI Bengkulu' }}</strong>. Pantau, catat pendaratan ikan, dan tinjau performa operasional TPI Anda secara real-time.
                        @elseif($user->role === 'verifikator')
                            Anda bertugas sebagai <strong>Verifikator DKP Provinsi Bengkulu</strong>. Periksa keabsahan volume tangkapan, harga pasar ikan, dan sahkan pendaratan resmi.
                        @elseif($user->role === 'pimpinan')
                            Panel Eksekutif Pengawasan & Analisis Agregat Produksi Perikanan Tangkap Dinas Kelautan dan Perikanan Provinsi Bengkulu.
                        @else
                            Sistem Tata Kelola Data Aktivitas TPI Provinsi Bengkulu.
                        @endif
                    </p>

                    <!-- Role Quick Actions -->
                    <div class="d-flex flex-wrap gap-2">
                        @if($isAdminTpi)
                            <a href="{{ route('pendaratan.create') }}" class="btn btn-warning text-dark fw-semibold btn-sm px-3 py-2 shadow-sm">
                                <i class="bi bi-plus-circle-fill me-1"></i> + Input Pendaratan Baru
                            </a>
                            <a href="{{ route('laporan.index') }}" class="btn btn-outline-light btn-sm px-3 py-2">
                                <i class="bi bi-file-earmark-bar-graph me-1"></i> Laporan Rekapitulasi
                            </a>
                            <a href="{{ route('kapal.index') }}" class="btn btn-outline-light btn-sm px-3 py-2">
                                <i class="bi bi-tsunami me-1"></i> Data Kapal ({{ $totalKapal }})
                            </a>
                        @elseif($user->role === 'verifikator')
                            <a href="{{ route('verifikasi.index') }}" class="btn btn-warning text-dark fw-bold btn-sm px-3 py-2 shadow-sm">
                                <i class="bi bi-shield-check me-1"></i> Buka Antrean Verifikasi ({{ $pendaratanMenungguVerifikasi }})
                            </a>
                            <a href="{{ route('laporan.index') }}" class="btn btn-outline-light btn-sm px-3 py-2">
                                <i class="bi bi-file-earmark-bar-graph me-1"></i> Hub Laporan Resmi
                            </a>
                        @elseif($user->role === 'pimpinan')
                            <a href="{{ route('laporan.index', ['tab' => 'tpi']) }}" class="btn btn-warning text-dark fw-semibold btn-sm px-3 py-2 shadow-sm">
                                <i class="bi bi-pie-chart-fill me-1"></i> Rekapitulasi per TPI
                            </a>
                            <a href="{{ route('laporan.index', ['tab' => 'dkp']) }}" class="btn btn-outline-light btn-sm px-3 py-2">
                                <i class="bi bi-journal-bookmark me-1"></i> Format Publikasi DKP
                            </a>
                            <a href="{{ route('laporan.index', ['tab' => 'triwulan']) }}" class="btn btn-outline-light btn-sm px-3 py-2">
                                <i class="bi bi-calendar3 me-1"></i> Standar Triwulanan BPS
                            </a>
                        @endif
                    </div>
                </div>

                <div class="col-lg-4 d-none d-lg-flex justify-content-end align-items-center">
                    <div class="text-center p-3 rounded-3" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); min-width: 220px;">
                        <div class="text-white-50 small mb-1">Tanggal Hari Ini</div>
                        <div class="fw-bold fs-5">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</div>
                        <div class="text-info small mt-1">
                            <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::now()->format('H:i') }} WIB
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Verifikator Alert Banner (if there are items waiting for verification) -->
    @if($user->role === 'verifikator' && $pendaratanMenungguVerifikasi > 0)
        <div class="alert alert-warning border-0 shadow-sm d-flex flex-wrap align-items-center justify-content-between p-3 mb-4 rounded-3 gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning text-dark p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="bi bi-exclamation-circle-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Perhatian: Antrean Verifikasi Data Pendaratan</div>
                    <div class="text-secondary small">
                        Terdapat <strong>{{ $pendaratanMenungguVerifikasi }}</strong> dokumen pendaratan baru yang berstatus <em>diajukan</em> dan menunggu proses verifikasi Anda.
                    </div>
                </div>
            </div>
            <a href="{{ route('verifikasi.index') }}" class="btn btn-dark btn-sm fw-semibold px-3 py-2 shadow-sm">
                <i class="bi bi-shield-check me-1"></i> Buka Antrean Verifikasi
            </a>
        </div>
    @endif

    <!-- 4 Main Statistics Cards -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Total TPI Aktif -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-semibold small">
                        {{ $isAdminTpi ? 'Status TPI Operasional' : 'Total TPI Aktif' }}
                    </span>
                    <div class="p-2 rounded-3 text-primary bg-primary-subtle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-building fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-1">
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalTpiAktif) }}</h3>
                    <span class="text-muted small">Lokasi</span>
                </div>
                <div class="small text-muted mt-auto pt-2 border-top">
                    @if($isAdminTpi && $assignedTpi)
                        <span class="text-success fw-semibold"><i class="bi bi-check-circle me-1"></i>{{ $assignedTpi->nama_tpi }}</span>
                    @else
                        <span>TPI beroperasi di Provinsi Bengkulu</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card 2: Total Pendaratan Terverifikasi -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-semibold small">Pendaratan Diverifikasi</span>
                    <div class="p-2 rounded-3 text-success bg-success-subtle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-check2-circle fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-1">
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalPendaratanDiverifikasi) }}</h3>
                    <span class="text-muted small">Transaksi</span>
                </div>
                <div class="small text-muted mt-auto pt-2 border-top d-flex justify-content-between">
                    <span>Terverifikasi Sah</span>
                    @if($pendaratanMenungguVerifikasi > 0)
                        <span class="badge bg-warning-subtle text-warning-emphasis fw-bold">
                            {{ $pendaratanMenungguVerifikasi }} menunggu
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card 3: Total Volume Ikan (Kg) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-semibold small">Total Volume Ikan</span>
                    <div class="p-2 rounded-3 text-info bg-info-subtle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-box-seam fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-1 mb-1">
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalVolumeIkan, 2, ',', '.') }}</h3>
                    <span class="text-muted small fw-semibold">Kg</span>
                </div>
                <div class="small text-muted mt-auto pt-2 border-top">
                    <span>Volume tangkapan yang telah disahkan</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Total Nilai Produksi (Rp) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-semibold small">Total Nilai Produksi</span>
                    <div class="p-2 rounded-3 text-warning bg-warning-subtle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-cash-coin fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-1 mb-1">
                    <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalNilaiProduksi, 0, ',', '.') }}</h4>
                </div>
                <div class="small text-muted mt-auto pt-2 border-top">
                    <span>Estimasi perputaran ekonomi pendaratan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- 2x2 ANALYTICAL CHARTS SECTION (CHART.JS)                      -->
    <!-- ============================================================= -->
    <div class="row g-4 mb-4">
        <!-- Chart 1: Tren Produksi Bulanan (Dual-Axis Bar & Line) -->
        <div class="col-12 col-lg-6">
            <div class="card card-custom h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                            <i class="bi bi-graph-up text-primary"></i>
                            <span>Tren Produksi Bulanan (Tahun {{ $trendBulanan['tahun'] ?? date('Y') }})</span>
                        </div>
                        <div class="text-muted small">Perbandingan Volume Tangkapan (Kg) dan Estimasi Nilai (Rp)</div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary fw-bold">Dual-Axis</span>
                </div>
                <div class="card-body p-3">
                    <div style="position: relative; height: 320px;">
                        <canvas id="chartTrenBulanan"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Kontribusi Produksi per TPI -->
        <div class="col-12 col-lg-6">
            <div class="card card-custom h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                            <i class="bi bi-building text-info"></i>
                            <span>{{ $isAdminTpi ? 'Produksi TPI Terkait' : 'Kontribusi Produksi per TPI' }}</span>
                        </div>
                        <div class="text-muted small">{{ $isAdminTpi ? 'Volume pendaratan pada TPI tugas Anda' : 'Volume tangkapan terverifikasi antar TPI di Bengkulu' }}</div>
                    </div>
                    <a href="{{ route('laporan.index', ['tab' => 'tpi']) }}" class="btn btn-sm btn-outline-secondary">
                        Detail <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-3">
                    <div style="position: relative; height: 320px;">
                        <canvas id="chartProduksiTpi"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 3: Top 7 Komoditas Ikan Tangkapan Tertinggi (Horizontal Bar) -->
        <div class="col-12 col-lg-6">
            <div class="card card-custom h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                            <i class="bi bi-trophy text-warning"></i>
                            <span>Top 7 Komoditas Ikan Tertinggi</span>
                        </div>
                        <div class="text-muted small">Jenis ikan dengan volume pendaratan terbanyak (Kg)</div>
                    </div>
                    <a href="{{ route('laporan.index', ['tab' => 'ikan']) }}" class="btn btn-sm btn-outline-secondary">
                        Semua Ikan <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-3">
                    @if(empty($topKomoditas['labels']))
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada data komoditas ikan terverifikasi
                        </div>
                    @else
                        <div style="position: relative; height: 320px;">
                            <canvas id="chartTopKomoditas"></canvas>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Chart 4: Komposisi Kategori Ikan (Doughnut) -->
        <div class="col-12 col-lg-6">
            <div class="card card-custom h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                            <i class="bi bi-pie-chart text-success"></i>
                            <span>Komposisi Kategori Ikan</span>
                        </div>
                        <div class="text-muted small">Pelagis Besar, Pelagis Kecil, Demersal, dan Non-Ikan</div>
                    </div>
                    <span class="badge bg-success-subtle text-success fw-bold">Kategori DKP</span>
                </div>
                <div class="card-body p-3">
                    <div style="position: relative; height: 320px;">
                        <canvas id="chartKategoriIkan"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities Table -->
    <div class="card card-custom mb-4">
        <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between border-bottom">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-primary fs-5"></i>
                <h5 class="fw-bold mb-0 text-dark">Aktivitas Pendaratan Terbaru</h5>
            </div>
            <a href="{{ route('pendaratan.index') }}" class="btn btn-sm btn-outline-secondary">
                Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body p-0">
            @if($recentPendaratan->isEmpty())
                <div class="text-center py-5">
                    <div class="text-muted mb-2">
                        <i class="bi bi-journal-x" style="font-size: 3rem;"></i>
                    </div>
                    <h6 class="fw-semibold text-secondary">Belum Ada Aktivitas Pendaratan</h6>
                    <p class="text-muted small mb-3">Belum terdapat data transaksi pendaratan kapal yang tercatat di sistem.</p>
                    @if($isAdminTpi)
                        <a href="{{ route('pendaratan.create') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Input Pendaratan Sekarang
                        </a>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 5%;">No</th>
                                <th style="width: 18%;">Kode Pendaratan</th>
                                <th style="width: 18%;">TPI</th>
                                <th style="width: 14%;">Tanggal</th>
                                <th style="width: 18%;">Kapal</th>
                                <th style="width: 15%;">Nelayan</th>
                                <th class="text-center pe-4" style="width: 12%;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPendaratan as $index => $item)
                                <tr>
                                    <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark font-monospace">{{ $item->kode_pendaratan }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $item->tpi?->nama_tpi ?? '-' }}</div>
                                        <small class="text-muted">{{ $item->tpi?->kode_tpi }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $item->tanggal_pendaratan ? $item->tanggal_pendaratan->format('d/m/Y') : '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $item->kapal?->nama_kapal ?? '-' }}</div>
                                        <small class="text-muted">{{ $item->kapal?->kode_kapal }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $item->nelayan?->nama ?? '-' }}</div>
                                        <small class="text-muted">{{ $item->nelayan?->kelompok_nelayan ?? 'Mandiri' }}</small>
                                    </td>
                                    <td class="text-center pe-4">
                                        @if($item->status_verifikasi === 'draft')
                                            <span class="badge bg-secondary">Draft</span>
                                        @elseif($item->status_verifikasi === 'diajukan')
                                            <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Diajukan</span>
                                        @elseif($item->status_verifikasi === 'diverifikasi')
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Diverifikasi</span>
                                        @elseif($item->status_verifikasi === 'ditolak')
                                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Ditolak</span>
                                        @else
                                            <span class="badge bg-light text-dark">{{ ucfirst($item->status_verifikasi) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const formatNumber = (num) => new Intl.NumberFormat('id-ID').format(num);
    const formatRupiah = (num) => 'Rp ' + new Intl.NumberFormat('id-ID').format(num);

    // Global Font Styling
    Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
    Chart.defaults.color = '#64748b';

    // -------------------------------------------------------------
    // 1. Chart: Tren Produksi Bulanan (Dual-Axis Bar & Line)
    // -------------------------------------------------------------
    const ctxTren = document.getElementById('chartTrenBulanan');
    if (ctxTren) {
        const trendData = @json($trendBulanan);
        new Chart(ctxTren, {
            data: {
                labels: trendData.labels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Volume Tangkapan (Kg)',
                        data: trendData.volumes,
                        backgroundColor: 'rgba(50, 140, 193, 0.75)',
                        borderColor: '#328CC1',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        yAxisID: 'yVolume',
                        order: 2
                    },
                    {
                        type: 'line',
                        label: 'Nilai Produksi (Rp)',
                        data: trendData.values,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.15)',
                        pointBackgroundColor: '#f59e0b',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                        pointRadius: 4,
                        borderWidth: 2.5,
                        fill: false,
                        tension: 0.35,
                        yAxisID: 'yNilai',
                        order: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { boxWidth: 14, font: { size: 12, weight: '600' } }
                    },
                    tooltip: {
                        padding: 10,
                        callbacks: {
                            label: function (context) {
                                if (context.dataset.yAxisID === 'yNilai') {
                                    return ' Nilai: ' + formatRupiah(context.raw);
                                }
                                return ' Volume: ' + formatNumber(context.raw) + ' Kg';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    yVolume: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Volume (Kg)', font: { size: 11, weight: '600' } },
                        ticks: {
                            callback: (v) => formatNumber(v) + ' Kg'
                        },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    yNilai: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { display: true, text: 'Nilai (Rp)', font: { size: 11, weight: '600' } },
                        ticks: {
                            callback: (v) => 'Rp ' + (v >= 1000000 ? (v / 1000000).toFixed(1) + ' Jt' : formatNumber(v))
                        },
                        grid: { drawOnChartArea: false }
                    }
                }
            }
        });
    }

    // -------------------------------------------------------------
    // 2. Chart: Kontribusi Produksi per TPI (Bar)
    // -------------------------------------------------------------
    const ctxTpi = document.getElementById('chartProduksiTpi');
    if (ctxTpi) {
        const tpiData = @json($produksiPerTpi);
        const barColors = [
            'rgba(11, 60, 93, 0.85)',
            'rgba(50, 140, 193, 0.85)',
            'rgba(71, 184, 224, 0.85)',
            'rgba(16, 185, 129, 0.85)',
            'rgba(245, 158, 11, 0.85)'
        ];

        new Chart(ctxTpi, {
            type: 'bar',
            data: {
                labels: tpiData.labels,
                datasets: [
                    {
                        label: 'Volume Tangkapan (Kg)',
                        data: tpiData.volumes,
                        backgroundColor: tpiData.labels.map((_, i) => barColors[i % barColors.length]),
                        borderColor: '#ffffff',
                        borderWidth: 1.5,
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 10,
                        callbacks: {
                            label: function (context) {
                                const idx = context.dataIndex;
                                const vol = formatNumber(tpiData.volumes[idx]) + ' Kg';
                                const val = formatRupiah(tpiData.values[idx]);
                                return [' Volume: ' + vol, ' Nilai: ' + val];
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        ticks: { callback: (v) => formatNumber(v) + ' Kg' },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    }
                }
            }
        });
    }

    // -------------------------------------------------------------
    // 3. Chart: Top 7 Komoditas Ikan (Horizontal Bar)
    // -------------------------------------------------------------
    const ctxTop = document.getElementById('chartTopKomoditas');
    if (ctxTop) {
        const topData = @json($topKomoditas);
        new Chart(ctxTop, {
            type: 'bar',
            data: {
                labels: topData.labels,
                datasets: [
                    {
                        axis: 'y',
                        label: 'Volume (Kg)',
                        data: topData.volumes,
                        backgroundColor: 'rgba(11, 60, 93, 0.85)',
                        borderColor: '#0B3C5D',
                        borderWidth: 1.5,
                        borderRadius: 5
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 10,
                        callbacks: {
                            label: function (context) {
                                const idx = context.dataIndex;
                                const vol = formatNumber(topData.volumes[idx]) + ' Kg';
                                const val = formatRupiah(topData.values[idx]);
                                return [' Volume: ' + vol, ' Nilai: ' + val];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { callback: (v) => formatNumber(v) + ' Kg' },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    y: { grid: { display: false } }
                }
            }
        });
    }

    // -------------------------------------------------------------
    // 4. Chart: Komposisi Kategori Ikan (Doughnut)
    // -------------------------------------------------------------
    const ctxKategori = document.getElementById('chartKategoriIkan');
    if (ctxKategori) {
        const catData = @json($kategoriIkan);
        const palette = [
            '#0B3C5D', // Pelagis Besar
            '#328CC1', // Pelagis Kecil
            '#47b8e0', // Demersal
            '#f59e0b', // Non-Ikan
            '#10b981', // Lainnya
        ];

        new Chart(ctxKategori, {
            type: 'doughnut',
            data: {
                labels: catData.labels,
                datasets: [
                    {
                        data: catData.volumes,
                        backgroundColor: palette.slice(0, catData.labels.length),
                        hoverOffset: 6,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 14, font: { size: 12, weight: '500' }, padding: 14 }
                    },
                    tooltip: {
                        padding: 10,
                        callbacks: {
                            label: function (context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const val = context.raw;
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                return ' ' + context.label + ': ' + formatNumber(val) + ' Kg (' + pct + '%)';
                            }
                        }
                    }
                },
                cutout: '62%'
            }
        });
    }
});
</script>
@endpush
