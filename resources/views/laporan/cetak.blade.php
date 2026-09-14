<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $judulLaporan }} - DKP Provinsi Bengkulu</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Tinos:wght@400;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #1a202c;
            background-color: #f8fafc;
            font-size: 13px;
        }

        .print-page {
            max-width: 1050px;
            margin: 20px auto;
            background: #ffffff;
            padding: 40px 45px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
        }

        /* Official Government Kop Surat */
        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }

        .kop-title-prov {
            font-family: 'Tinos', serif;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            line-height: 1.2;
            color: #000;
        }

        .kop-title-dinas {
            font-family: 'Tinos', serif;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            line-height: 1.2;
            color: #0B3C5D;
        }

        .kop-title-sub {
            font-size: 12px;
            color: #4a5568;
            line-height: 1.4;
            margin-top: 4px;
        }

        .report-title-box {
            text-align: center;
            margin-bottom: 24px;
        }

        .report-title-main {
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #1a202c;
            margin-bottom: 4px;
        }

        .report-title-sub {
            font-size: 13px;
            color: #4a5568;
            font-weight: 500;
        }

        /* Metadata table */
        .meta-table td {
            padding: 3px 8px;
            font-size: 12px;
        }

        /* Report tables */
        .table-report {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 25px;
        }

        .table-report th, .table-report td {
            border: 1px solid #cbd5e1;
            padding: 7px 9px;
        }

        .table-report th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 700;
            text-align: center;
        }

        .table-report tfoot tr {
            background-color: #e2e8f0;
            font-weight: 700;
        }

        /* Signature block */
        .signature-box {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        /* Screen Controls Toolbar */
        .screen-toolbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        @media print {
            body {
                background: #ffffff;
                color: #000;
                font-size: 11pt;
            }

            .screen-toolbar {
                display: none !important;
            }

            .print-page {
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                border-radius: 0 !important;
            }

            .table-report th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .table-report tfoot tr {
                background-color: #e2e8f0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            @page {
                size: A4 {{ $tipe === 'triwulan' ? 'landscape' : 'portrait' }};
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body>

    <!-- Screen Controls Bar (Hidden when printed) -->
    <div class="screen-toolbar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('laporan.index', ['tab' => $tipe]) }}" class="btn btn-outline-secondary btn-sm px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Modul Laporan
            </a>
            <span class="text-muted small">Pratinjau Dokumen Resmi Cetak</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm">
                <i class="bi bi-printer-fill me-1"></i> Cetak Dokumen (Print)
            </button>
        </div>
    </div>

    <!-- Printable Paper Sheet -->
    <div class="print-page">

        <!-- Official Kop Surat -->
        <div class="kop-surat">
            <div class="d-flex align-items-center justify-content-center gap-4 text-center">
                <div>
                    <div class="kop-title-prov">PEMERINTAH PROVINSI BENGKULU</div>
                    <div class="kop-title-dinas">DINAS KELAUTAN DAN PERIKANAN</div>
                    <div class="kop-title-sub">
                        Sistem Informasi Tata Kelola Data Aktivitas Tempat Pelelangan Ikan (SIP-TPI)<br>
                        Jl. Pariwisata Pantai Panjang, Kota Bengkulu, Bengkulu 38222 | Telp: (0736) 21543 | Email: dkp@bengkuluprov.go.id
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Title -->
        <div class="report-title-box">
            <div class="report-title-main">{{ $judulLaporan }}</div>
            <div class="report-title-sub">Periode: {{ $filters['periode_label'] }}</div>
        </div>

        <!-- Metadata Information Box -->
        <div class="row mb-4">
            <div class="col-7">
                <table class="meta-table">
                    <tr>
                        <td class="text-muted" style="width: 140px;">Unit Pelapor / TPI</td>
                        <td>: <strong>{{ $filters['selected_tpi'] ? $filters['selected_tpi']->nama_tpi : 'Seluruh TPI se-Provinsi Bengkulu' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kategori Ikan</td>
                        <td>: {{ $filters['kategori_ikan'] ? $filters['kategori_ikan'] : 'Semua Komoditas Ikan Tangkap' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status Validasi Data</td>
                        <td>: <span class="badge bg-success text-white">Terverifikasi Sah</span></td>
                    </tr>
                </table>
            </div>
            <div class="col-5">
                <table class="meta-table ms-auto">
                    <tr>
                        <td class="text-muted" style="width: 120px;">Tanggal Dokumen</td>
                        <td>: {{ $tanggalCetak->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Waktu Cetak</td>
                        <td>: {{ $tanggalCetak->format('H:i') }} WIB</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Petugas Pencetak</td>
                        <td>: {{ $user->name }} ({{ $user->role_label }})</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Dynamic Report Content -->
        @if($tipe === 'tpi')
            <!-- TABEL 1: REKAP PER TPI -->
            <table class="table-report">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 14%;">Kode TPI</th>
                        <th style="width: 24%;" class="text-start">Nama TPI</th>
                        <th style="width: 18%;" class="text-start">Kabupaten / Kota</th>
                        <th style="width: 12%;">Pendaratan (Trip)</th>
                        <th style="width: 13%;">Total Volume (Kg)</th>
                        <th style="width: 14%;">Nilai Produksi (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['rows'] as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center font-monospace">{{ $row['kode_tpi'] }}</td>
                            <td class="fw-semibold">{{ $row['nama_tpi'] }}</td>
                            <td>{{ $row['kabupaten_kota'] }}</td>
                            <td class="text-center">{{ number_format($row['frekuensi_pendaratan'], 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($row['total_volume'], 2, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($row['total_nilai'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-center">TOTAL KESELURUHAN</td>
                        <td class="text-center">{{ number_format($data['totals']['frekuensi_pendaratan'], 0, ',', '.') }} Trip</td>
                        <td class="text-end">{{ number_format($data['totals']['total_volume'], 2, ',', '.') }} Kg</td>
                        <td class="text-end">Rp {{ number_format($data['totals']['total_nilai'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

        @elseif($tipe === 'ikan')
            <!-- TABEL 2: REKAP PER KOMODITAS IKAN -->
            <table class="table-report">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 12%;">Kode Ikan</th>
                        <th style="width: 25%;" class="text-start">Nama Komoditas Ikan</th>
                        <th style="width: 18%;">Kategori</th>
                        <th style="width: 13%;">Volume (Kg)</th>
                        <th style="width: 10%;">Kontribusi</th>
                        <th style="width: 17%;">Nilai Produksi (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['rows'] as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center font-monospace">{{ $row['kode_ikan'] }}</td>
                            <td>
                                <strong>{{ $row['nama_resmi'] }}</strong>
                                @if($row['nama_lokal'] && $row['nama_lokal'] !== '-')
                                    <span class="text-muted fst-italic small">({{ $row['nama_lokal'] }})</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $row['kategori'] }}</td>
                            <td class="text-end">{{ number_format($row['volume_kg'], 2, ',', '.') }}</td>
                            <td class="text-center">{{ number_format($row['persentase'], 2, ',', '.') }}%</td>
                            <td class="text-end">Rp {{ number_format($row['total_nilai'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-center">TOTAL KESELURUHAN ({{ $data['totals']['count'] }} Spesies)</td>
                        <td class="text-end">{{ number_format($data['totals']['volume_kg'], 2, ',', '.') }} Kg</td>
                        <td class="text-center">100.00%</td>
                        <td class="text-end">Rp {{ number_format($data['totals']['total_nilai'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

        @elseif($tipe === 'dkp')
            <!-- TABEL 3: STANDAR FORMAT DKP BENGKULU -->
            <table class="table-report">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 22%;" class="text-start">Kabupaten / Kota Pesisir</th>
                        <th style="width: 10%;">Kode</th>
                        <th style="width: 10%;">Jumlah TPI</th>
                        <th style="width: 12%;">Trip</th>
                        <th style="width: 13%;">Volume (Kg)</th>
                        <th style="width: 15%;">Nilai Produksi (Rp)</th>
                        <th style="width: 13%;">Unggulan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['rows'] as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="fw-semibold">{{ $row['nama_kabupaten_kota'] }}</td>
                            <td class="text-center font-monospace">{{ $row['kode_wilayah'] }}</td>
                            <td class="text-center">{{ $row['jumlah_tpi'] }} Unit</td>
                            <td class="text-center">{{ number_format($row['frekuensi_trip'], 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($row['total_volume'], 2, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($row['total_nilai'], 0, ',', '.') }}</td>
                            <td class="text-center">{{ $row['komoditas_unggulan'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-center">TOTAL PROVINSI BENGKULU</td>
                        <td class="text-center">{{ $data['totals']['jumlah_tpi'] }} Unit</td>
                        <td class="text-center">{{ number_format($data['totals']['frekuensi_trip'], 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($data['totals']['total_volume'], 2, ',', '.') }} Kg</td>
                        <td class="text-end">Rp {{ number_format($data['totals']['total_nilai'], 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

        @elseif($tipe === 'triwulan')
            <!-- TABEL 4: STANDAR FORMAT TRIWULANAN BPS -->
            <table class="table-report" style="font-size: 11px;">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 4%;">No</th>
                        <th rowspan="2" style="width: 18%;" class="text-start">Nama TPI</th>
                        <th colspan="2">Triwulan I</th>
                        <th colspan="2">Triwulan II</th>
                        <th colspan="2">Triwulan III</th>
                        <th colspan="2">Triwulan IV</th>
                        <th colspan="2">Total Setahun</th>
                    </tr>
                    <tr>
                        <th>Vol (Kg)</th>
                        <th>Nilai (Rp)</th>
                        <th>Vol (Kg)</th>
                        <th>Nilai (Rp)</th>
                        <th>Vol (Kg)</th>
                        <th>Nilai (Rp)</th>
                        <th>Vol (Kg)</th>
                        <th>Nilai (Rp)</th>
                        <th>Vol (Kg)</th>
                        <th>Nilai (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['rows'] as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="fw-semibold">{{ $row['nama_tpi'] }}</td>
                            <td class="text-end">{{ number_format($row['tw1_volume'], 2, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($row['tw1_nilai'], 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($row['tw2_volume'], 2, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($row['tw2_nilai'], 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($row['tw3_volume'], 2, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($row['tw3_nilai'], 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($row['tw4_volume'], 2, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($row['tw4_nilai'], 0, ',', '.') }}</td>
                            <td class="text-end fw-bold">{{ number_format($row['total_volume'], 2, ',', '.') }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($row['total_nilai'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-center">TOTAL PROVINSI</td>
                        <td class="text-end">{{ number_format($data['totals']['tw1_volume'], 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($data['totals']['tw1_nilai'], 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($data['totals']['tw2_volume'], 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($data['totals']['tw2_nilai'], 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($data['totals']['tw3_volume'], 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($data['totals']['tw3_nilai'], 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($data['totals']['tw4_volume'], 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($data['totals']['tw4_nilai'], 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($data['totals']['total_volume'], 2, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($data['totals']['total_nilai'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @endif

        <!-- Official Signature Section -->
        <div class="signature-box">
            <div class="row">
                <div class="col-6 text-center">
                    <div>Mengetahui,</div>
                    <div class="fw-bold">Kepala Dinas Kelautan dan Perikanan</div>
                    <div class="fw-bold">Provinsi Bengkulu</div>
                    <div style="height: 75px;"></div>
                    <div class="fw-bold text-decoration-underline">Syafriandi, SE., ST., M.Si</div>
                    <div class="small text-muted">Pembina Utama Madya</div>
                    <div class="small font-monospace">NIP. 19740512 199903 1 002</div>
                </div>
                <div class="col-6 text-center">
                    <div>Kota Bengkulu, {{ $tanggalCetak->translatedFormat('d F Y') }}</div>
                    <div>Petugas / Verifikator Data,</div>
                    <div style="height: 75px;"></div>
                    <div class="fw-bold text-decoration-underline">{{ $user->name }}</div>
                    <div class="small text-muted">{{ $user->role_label }}</div>
                    <div class="small font-monospace">NIP. 19850914 201001 1 008</div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
