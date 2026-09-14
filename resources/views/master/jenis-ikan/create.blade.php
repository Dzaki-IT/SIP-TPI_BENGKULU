@extends('layouts.app')

@section('title', 'Tambah Jenis Ikan')

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header & Breadcrumbs -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('jenis-ikan.index') }}" class="text-decoration-none">Master Jenis Ikan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Jenis Ikan</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle-fill text-primary"></i> Tambah Komoditas Ikan & Hasil Laut
        </h4>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-info-circle me-1 text-primary"></i> Formulir Master Komoditas Ikan
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('jenis-ikan.store') }}" method="POST">
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="kode_ikan" class="form-label fw-semibold">Kode Ikan <span class="text-danger">*</span></label>
                                <input type="text" name="kode_ikan" id="kode_ikan" class="form-control @error('kode_ikan') is-invalid @enderror" value="{{ old('kode_ikan') }}" placeholder="Contoh: IKN-021" required autofocus>
                                <small class="text-muted">Kode unik identifikasi komoditas (maks. 20 karakter).</small>
                                @error('kode_ikan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="kategori" class="form-label fw-semibold">Kategori Komoditas <span class="text-danger">*</span></label>
                                <select name="kategori" id="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ old('kategori') === $cat ? 'selected' : '' }}>
                                            {{ $cat }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="nama_resmi" class="form-label fw-semibold">Nama Resmi (Komersial) <span class="text-danger">*</span></label>
                                <input type="text" name="nama_resmi" id="nama_resmi" class="form-control @error('nama_resmi') is-invalid @enderror" value="{{ old('nama_resmi') }}" placeholder="Contoh: Kerapu Tikus / Bebek" required>
                                <small class="text-muted">Nama baku/dagang resmi ikan.</small>
                                @error('nama_resmi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="nama_lokal" class="form-label fw-semibold">Nama Ilmiah / Nama Lokal</label>
                                <input type="text" name="nama_lokal" id="nama_lokal" class="form-control @error('nama_lokal') is-invalid @enderror" value="{{ old('nama_lokal') }}" placeholder="Contoh: Cromileptes altivelis / Ikan Gebur">
                                <small class="text-muted">Nama ilmiah (latin) atau sebutan nelayan lokal.</small>
                                @error('nama_lokal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="satuan" class="form-label fw-semibold">Satuan Pengukuran <span class="text-danger">*</span></label>
                                <input type="text" name="satuan" id="satuan" class="form-control @error('satuan') is-invalid @enderror" value="{{ old('satuan', 'Kg') }}" placeholder="Contoh: Kg, Ekor, Keranjang" required>
                                <small class="text-muted">Satuan standar pencatatan pendaratan (default: Kg).</small>
                                @error('satuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="is_active" class="form-label fw-semibold">Status Komoditas <span class="text-danger">*</span></label>
                                <select name="is_active" id="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif Ditransaksikan</option>
                                    <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Nonaktif / Dilindungi Penuh</option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                            <a href="{{ route('jenis-ikan.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-arrow-left me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                                <i class="bi bi-save me-1"></i> Simpan Jenis Ikan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
