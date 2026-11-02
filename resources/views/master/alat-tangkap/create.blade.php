@extends('layouts.app')

@section('title', 'Tambah Alat Tangkap')

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header & Breadcrumbs -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('alat-tangkap.index') }}" class="text-decoration-none">Master Alat Tangkap</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Alat Tangkap</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle-fill text-primary"></i> Tambah Alat Penangkapan Ikan (API)
        </h4>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-info-circle me-1 text-primary"></i> Formulir Alat Penangkapan Ikan
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('alat-tangkap.store') }}" method="POST">
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="kode_alat" class="form-label fw-semibold">Kode Alat Tangkap <span class="text-danger">*</span></label>
                                <input type="text" name="kode_alat" id="kode_alat" class="form-control @error('kode_alat') is-invalid @enderror" value="{{ old('kode_alat') }}" placeholder="Contoh: AT-014" required autofocus>
                                <small class="text-muted">Kode unik identifikasi API (maks. 20 karakter).</small>
                                @error('kode_alat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="jenis_kategori" class="form-label fw-semibold">Kategori / Golongan Alat</label>
                                <input type="text" list="kategoriOptions" name="jenis_kategori" id="jenis_kategori" class="form-control @error('jenis_kategori') is-invalid @enderror" value="{{ old('jenis_kategori') }}" placeholder="Contoh: Pukat, Jaring, Pancing...">
                                <datalist id="kategoriOptions">
                                    <option value="Pukat">
                                    <option value="Jaring">
                                    <option value="Pancing">
                                    <option value="Jaring Angkat">
                                    <option value="Perangkap">
                                    <option value="Lainnya">
                                    @foreach($existingCategories as $cat)
                                        <option value="{{ $cat }}">
                                    @endforeach
                                </datalist>
                                <small class="text-muted">Ketik langsung atau pilih dari rekomendasi.</small>
                                @error('jenis_kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nama_alat" class="form-label fw-semibold">Nama Alat Tangkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_alat" id="nama_alat" class="form-control @error('nama_alat') is-invalid @enderror" value="{{ old('nama_alat') }}" placeholder="Contoh: Pancing Ulur (Hand Line)" required>
                            @error('nama_alat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nelayan_id" class="form-label fw-semibold">Pemilik / Nelayan Terdaftar</label>
                            <select name="nelayan_id" id="nelayan_id" class="form-select @error('nelayan_id') is-invalid @enderror">
                                <option value="">-- Pilih Nelayan Pemilik (Opsional) --</option>
                                @foreach($nelayanList as $nelayan)
                                    <option value="{{ $nelayan->id }}" {{ old('nelayan_id') == $nelayan->id ? 'selected' : '' }}>
                                        {{ $nelayan->nama }} ({{ $nelayan->kode_nelayan }}) - {{ $nelayan->tpi->nama_tpi ?? 'Non-Pangkalan' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hubungkan alat tangkap dengan nelayan pemiliknya bila sudah terdaftar.</small>
                            @error('nelayan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="is_active" class="form-label fw-semibold">Status Penggunaan <span class="text-danger">*</span></label>
                            <select name="is_active" id="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif Digunakan Nelayan</option>
                                <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Nonaktif / Dilarang (Illegal Fishing)</option>
                            </select>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                            <a href="{{ route('alat-tangkap.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-arrow-left me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                                <i class="bi bi-save me-1"></i> Simpan Alat Tangkap
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
