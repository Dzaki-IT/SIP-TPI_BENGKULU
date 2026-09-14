@extends('layouts.app')

@section('title', 'Edit Jenis Ikan - ' . $jenisIkan->nama_resmi)

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header & Breadcrumbs -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('jenis-ikan.index') }}" class="text-decoration-none">Master Jenis Ikan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Jenis Ikan</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-pencil-square text-primary"></i> Edit Komoditas: <span class="text-primary">{{ $jenisIkan->nama_resmi }}</span>
        </h4>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-pencil me-1 text-primary"></i> Perbarui Data Komoditas Ikan
                    </h6>
                    <span class="badge bg-light text-dark border font-monospace">
                        {{ $jenisIkan->kode_ikan }}
                    </span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('jenis-ikan.update', $jenisIkan) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="kode_ikan" class="form-label fw-semibold">Kode Ikan <span class="text-danger">*</span></label>
                                <input type="text" name="kode_ikan" id="kode_ikan" class="form-control @error('kode_ikan') is-invalid @enderror" value="{{ old('kode_ikan', $jenisIkan->kode_ikan) }}" required>
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
                                        <option value="{{ $cat }}" {{ old('kategori', $jenisIkan->kategori) === $cat ? 'selected' : '' }}>
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
                                <input type="text" name="nama_resmi" id="nama_resmi" class="form-control @error('nama_resmi') is-invalid @enderror" value="{{ old('nama_resmi', $jenisIkan->nama_resmi) }}" required>
                                <small class="text-muted">Nama baku/dagang resmi ikan.</small>
                                @error('nama_resmi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="nama_lokal" class="form-label fw-semibold">Nama Ilmiah / Nama Lokal</label>
                                <input type="text" name="nama_lokal" id="nama_lokal" class="form-control @error('nama_lokal') is-invalid @enderror" value="{{ old('nama_lokal', $jenisIkan->nama_lokal) }}">
                                <small class="text-muted">Nama ilmiah (latin) atau sebutan nelayan lokal.</small>
                                @error('nama_lokal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="satuan" class="form-label fw-semibold">Satuan Pengukuran <span class="text-danger">*</span></label>
                                <input type="text" name="satuan" id="satuan" class="form-control @error('satuan') is-invalid @enderror" value="{{ old('satuan', $jenisIkan->satuan) }}" required>
                                <small class="text-muted">Satuan standar pencatatan pendaratan (default: Kg).</small>
                                @error('satuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="is_active" class="form-label fw-semibold">Status Komoditas <span class="text-danger">*</span></label>
                                <select name="is_active" id="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                                    <option value="1" {{ old('is_active', $jenisIkan->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Aktif Ditransaksikan</option>
                                    <option value="0" {{ old('is_active', $jenisIkan->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Nonaktif / Dilindungi Penuh</option>
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
                                <i class="bi bi-check2-circle me-1"></i> Perbarui Jenis Ikan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
