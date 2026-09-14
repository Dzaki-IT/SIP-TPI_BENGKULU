@extends('layouts.app')

@section('title', 'Tambah TPI')

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header & Breadcrumbs -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tpi.index') }}" class="text-decoration-none">Master Data TPI</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah TPI Baru</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle-fill text-primary"></i> Tambah Tempat Pelelangan Ikan (TPI)
        </h4>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card card-custom">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-info-circle me-1 text-primary"></i> Formulir Master Data TPI
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('tpi.store') }}" method="POST">
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="kode_tpi" class="form-label fw-semibold">Kode TPI <span class="text-danger">*</span></label>
                                <input type="text" name="kode_tpi" id="kode_tpi" class="form-control @error('kode_tpi') is-invalid @enderror" value="{{ old('kode_tpi') }}" placeholder="Contoh: TPI-006" required autofocus>
                                <small class="text-muted">Kode unik identifikasi TPI (maks. 20 karakter).</small>
                                @error('kode_tpi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="nama_tpi" class="form-label fw-semibold">Nama TPI <span class="text-danger">*</span></label>
                                <input type="text" name="nama_tpi" id="nama_tpi" class="form-control @error('nama_tpi') is-invalid @enderror" value="{{ old('nama_tpi') }}" placeholder="Contoh: TPI Pasar Seluma" required>
                                @error('nama_tpi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="wilayah_id" class="form-label fw-semibold">Kabupaten / Kota <span class="text-danger">*</span></label>
                                <select name="wilayah_id" id="wilayah_id" class="form-select @error('wilayah_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Wilayah --</option>
                                    @foreach($wilayahList as $wil)
                                        <option value="{{ $wil->id }}" {{ old('wilayah_id') == $wil->id ? 'selected' : '' }}>
                                            {{ $wil->nama_kabupaten_kota }} ({{ $wil->kode_wilayah }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('wilayah_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="status_operasi" class="form-label fw-semibold">Status Operasional <span class="text-danger">*</span></label>
                                <select name="status_operasi" id="status_operasi" class="form-select @error('status_operasi') is-invalid @enderror" required>
                                    <option value="aktif" {{ old('status_operasi', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif Beroperasi</option>
                                    <option value="tidak_aktif" {{ old('status_operasi') === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                                @error('status_operasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="instansi_pengelola" class="form-label fw-semibold">Instansi Pengelola</label>
                                <input type="text" name="instansi_pengelola" id="instansi_pengelola" class="form-control @error('instansi_pengelola') is-invalid @enderror" value="{{ old('instansi_pengelola') }}" placeholder="Contoh: UPTD PPP Bengkulu / Dinas Perikanan">
                                @error('instansi_pengelola')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="jenis_perairan" class="form-label fw-semibold">Jenis Wilayah Perairan <span class="text-danger">*</span></label>
                                <select name="jenis_perairan" id="jenis_perairan" class="form-select @error('jenis_perairan') is-invalid @enderror" required>
                                    <option value="laut" {{ old('jenis_perairan', 'laut') === 'laut' ? 'selected' : '' }}>Perairan Laut (WPP-NRI 572)</option>
                                </select>
                                @error('jenis_perairan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label fw-semibold">Alamat Lengkap / Lokasi TPI</label>
                            <textarea name="alamat" id="alamat" rows="2" class="form-control @error('alamat') is-invalid @enderror" placeholder="Contoh: Jl. Pelabuhan Samudera No. 12, Kel. Sumber Jaya">{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-semibold">Keterangan Tambahan</label>
                            <textarea name="keterangan" id="keterangan" rows="2" class="form-control @error('keterangan') is-invalid @enderror" placeholder="Catatan fasilitas dermaga, pabrik es, cold storage, atau keterangan lainnya...">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                            <a href="{{ route('tpi.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-arrow-left me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                                <i class="bi bi-save me-1"></i> Simpan Data TPI
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
