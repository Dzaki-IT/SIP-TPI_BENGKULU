@extends('layouts.app')

@section('title', 'Edit Kapal - ' . $kapal->nama_kapal)

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header & Breadcrumbs -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('kapal.index') }}" class="text-decoration-none">Master Data Kapal</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Kapal</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-pencil-square text-primary"></i> Edit Data Kapal: <span class="text-primary">{{ $kapal->nama_kapal }}</span>
        </h4>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card card-custom">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-pencil me-1 text-primary"></i> Perbarui Data Kapal
                    </h6>
                    <span class="badge bg-light text-dark border font-monospace">
                        {{ $kapal->kode_kapal }}
                    </span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('kapal.update', $kapal) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="kode_kapal" class="form-label fw-semibold">Kode Kapal <span class="text-danger">*</span></label>
                                <input type="text" name="kode_kapal" id="kode_kapal" class="form-control @error('kode_kapal') is-invalid @enderror" value="{{ old('kode_kapal', $kapal->kode_kapal) }}" required>
                                <small class="text-muted">Kode identifikasi registrasi kapal (maks. 20 karakter).</small>
                                @error('kode_kapal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="nama_kapal" class="form-label fw-semibold">Nama Kapal <span class="text-danger">*</span></label>
                                <input type="text" name="nama_kapal" id="nama_kapal" class="form-control @error('nama_kapal') is-invalid @enderror" value="{{ old('nama_kapal', $kapal->nama_kapal) }}" required>
                                @error('nama_kapal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="jenis_kapal" class="form-label fw-semibold">Jenis Kapal <span class="text-danger">*</span></label>
                                <select name="jenis_kapal" id="jenis_kapal" class="form-select @error('jenis_kapal') is-invalid @enderror" required>
                                    <option value="tanpa_motor" {{ old('jenis_kapal', $kapal->jenis_kapal) === 'tanpa_motor' ? 'selected' : '' }}>Perahu Tanpa Motor (Jukung/Dayung)</option>
                                    <option value="motor_tempel" {{ old('jenis_kapal', $kapal->jenis_kapal) === 'motor_tempel' ? 'selected' : '' }}>Perahu Motor Tempel (Outboard Engine)</option>
                                    <option value="kapal_motor" {{ old('jenis_kapal', $kapal->jenis_kapal) === 'kapal_motor' ? 'selected' : '' }}>Kapal Motor (Inboard Engine)</option>
                                </select>
                                @error('jenis_kapal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="gt" class="form-label fw-semibold">Kapasitas Gross Tonnage (GT)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="9999.99" name="gt" id="gt" class="form-control @error('gt') is-invalid @enderror" value="{{ old('gt', $kapal->gt) }}">
                                    <span class="input-group-text bg-light text-muted">GT</span>
                                    @error('gt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Kapasitas tonase kotor kapal.</small>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="nelayan_id" class="form-label fw-semibold">Pemilik / Nakhoda Terdaftar</label>
                                <select name="nelayan_id" id="nelayan_id" class="form-select @error('nelayan_id') is-invalid @enderror">
                                    <option value="">-- Pilih Nelayan Pemilik (Opsional) --</option>
                                    @foreach($nelayanList as $nel)
                                        <option value="{{ $nel->id }}" {{ old('nelayan_id', $kapal->nelayan_id) == $nel->id ? 'selected' : '' }}>
                                            {{ $nel->nama }} ({{ $nel->kode_nelayan }}) - {{ $nel->tpi->nama_tpi ?? 'Non-Pangkalan' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('nelayan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="no_sipi" class="form-label fw-semibold">Nomor SIPI (Surat Izin Penangkapan Ikan)</label>
                                <input type="text" name="no_sipi" id="no_sipi" class="form-control @error('no_sipi') is-invalid @enderror" value="{{ old('no_sipi', $kapal->no_sipi) }}">
                                <small class="text-muted">Nomor surat izin penangkapan ikan resmi DKP (opsional).</small>
                                @error('no_sipi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="is_active" class="form-label fw-semibold">Status Operasi Kapal <span class="text-danger">*</span></label>
                                <select name="is_active" id="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                                    <option value="1" {{ old('is_active', $kapal->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Aktif Beroperasi</option>
                                    <option value="0" {{ old('is_active', $kapal->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Nonaktif / Perbaikan / Docking</option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                            <a href="{{ route('kapal.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-arrow-left me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                                <i class="bi bi-check2-circle me-1"></i> Perbarui Data Kapal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
