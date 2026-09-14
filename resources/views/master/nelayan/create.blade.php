@extends('layouts.app')

@section('title', 'Tambah Nelayan')

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header & Breadcrumbs -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('nelayan.index') }}" class="text-decoration-none">Master Data Nelayan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Nelayan</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-person-plus-fill text-primary"></i> Tambah Nelayan Terdaftar
        </h4>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card card-custom">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-info-circle me-1 text-primary"></i> Formulir Registrasi Nelayan Baru
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('nelayan.store') }}" method="POST">
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="kode_nelayan" class="form-label fw-semibold">Kode Nelayan <span class="text-danger">*</span></label>
                                <input type="text" name="kode_nelayan" id="kode_nelayan" class="form-control @error('kode_nelayan') is-invalid @enderror" value="{{ old('kode_nelayan') }}" placeholder="Contoh: NL-001" required autofocus>
                                <small class="text-muted">Kode unik registrasi nelayan (maks. 20 karakter).</small>
                                @error('kode_nelayan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="nama" class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" placeholder="Contoh: Budi Santoso" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="nik" class="form-label fw-semibold">Nomor Induk Kependudukan (NIK)</label>
                                <input type="text" name="nik" id="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik') }}" placeholder="16 digit NIK e-KTP" maxlength="16">
                                <small class="text-muted">Harus 16 digit angka sesuai KTP nelayan (opsional).</small>
                                @error('nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="tpi_id" class="form-label fw-semibold">Pangkalan TPI</label>
                                <select name="tpi_id" id="tpi_id" class="form-select @error('tpi_id') is-invalid @enderror">
                                    <option value="">-- Pilih Pangkalan TPI (Opsional) --</option>
                                    @foreach($tpiList as $tpi)
                                        <option value="{{ $tpi->id }}" {{ old('tpi_id', $defaultTpiId) == $tpi->id ? 'selected' : '' }}>
                                            {{ $tpi->nama_tpi }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tpi_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="kelompok_nelayan" class="form-label fw-semibold">Kelompok Nelayan (KUB / Koperasi)</label>
                                <input type="text" name="kelompok_nelayan" id="kelompok_nelayan" class="form-control @error('kelompok_nelayan') is-invalid @enderror" value="{{ old('kelompok_nelayan') }}" placeholder="Contoh: KUB Mina Bahari">
                                @error('kelompok_nelayan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="is_active" class="form-label fw-semibold">Status Keaktifan Nelayan <span class="text-danger">*</span></label>
                                <select name="is_active" id="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif Melaut</option>
                                    <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Tidak Aktif / Pensiun</option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="alamat" class="form-label fw-semibold">Alamat Tempat Tinggal</label>
                            <textarea name="alamat" id="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror" placeholder="Contoh: RT 03 RW 01 Kelurahan Kandang Mas, Kota Bengkulu">{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                            <a href="{{ route('nelayan.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-arrow-left me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                                <i class="bi bi-save me-1"></i> Simpan Data Nelayan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
