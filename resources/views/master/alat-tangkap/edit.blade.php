@extends('layouts.app')

@section('title', 'Edit Alat Tangkap - ' . $alatTangkap->nama_alat)

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header & Breadcrumbs -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('alat-tangkap.index') }}" class="text-decoration-none">Master Alat Tangkap</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Alat Tangkap</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-pencil-square text-primary"></i> Edit Data Alat Tangkap: <span class="text-primary">{{ $alatTangkap->nama_alat }}</span>
        </h4>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-pencil me-1 text-primary"></i> Perbarui Data Alat Penangkapan Ikan
                    </h6>
                    <span class="badge bg-light text-dark border font-monospace">
                        {{ $alatTangkap->kode_alat }}
                    </span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('alat-tangkap.update', $alatTangkap) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="kode_alat" class="form-label fw-semibold">Kode Alat Tangkap <span class="text-danger">*</span></label>
                                <input type="text" name="kode_alat" id="kode_alat" class="form-control @error('kode_alat') is-invalid @enderror" value="{{ old('kode_alat', $alatTangkap->kode_alat) }}" required>
                                <small class="text-muted">Kode unik identifikasi API (maks. 20 karakter).</small>
                                @error('kode_alat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="jenis_kategori" class="form-label fw-semibold">Kategori / Golongan Alat</label>
                                <input type="text" list="kategoriOptions" name="jenis_kategori" id="jenis_kategori" class="form-control @error('jenis_kategori') is-invalid @enderror" value="{{ old('jenis_kategori', $alatTangkap->jenis_kategori) }}">
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
                                @error('jenis_kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nama_alat" class="form-label fw-semibold">Nama Alat Tangkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_alat" id="nama_alat" class="form-control @error('nama_alat') is-invalid @enderror" value="{{ old('nama_alat', $alatTangkap->nama_alat) }}" required>
                            @error('nama_alat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nelayan_id" class="form-label fw-semibold">Pemilik / Nelayan Terdaftar</label>
                            <select name="nelayan_id" id="nelayan_id" class="form-select @error('nelayan_id') is-invalid @enderror">
                                <option value="">-- Pilih Nelayan Pemilik (Opsional) --</option>
                                @foreach($nelayanList as $nelayan)
                                    <option value="{{ $nelayan->id }}" {{ old('nelayan_id', $alatTangkap->nelayan_id) == $nelayan->id ? 'selected' : '' }}>
                                        {{ $nelayan->nama }} ({{ $nelayan->kode_nelayan }}) - {{ $nelayan->tpi->nama_tpi ?? 'Non-Pangkalan' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Pemilik dapat diubah atau dikosongkan jika alat tangkap belum terhubung.</small>
                            @error('nelayan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="is_active" class="form-label fw-semibold">Status Penggunaan <span class="text-danger">*</span></label>
                            <select name="is_active" id="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                                <option value="1" {{ old('is_active', $alatTangkap->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Aktif Digunakan Nelayan</option>
                                <option value="0" {{ old('is_active', $alatTangkap->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Nonaktif / Dilarang (Illegal Fishing)</option>
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
                                <i class="bi bi-check2-circle me-1"></i> Perbarui Alat Tangkap
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
