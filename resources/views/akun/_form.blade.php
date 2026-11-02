@php($isEditing = isset($akun))

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-custom">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-person-vcard me-1 text-primary"></i> Informasi Akun</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ $formAction }}" method="POST">
                    @csrf
                    @if($isEditing)
                        @method('PUT')
                    @endif

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Nama Pengguna <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $akun->name ?? '') }}" maxlength="100" required autofocus>
                            <small class="text-muted">Nama ini ditampilkan sebagai sapaan pada dashboard.</small>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $akun->email ?? '') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="role" class="form-label fw-semibold">Role Akses <span class="text-danger">*</span></label>
                            <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="admin_tpi" @selected(old('role', $akun->role ?? '') === 'admin_tpi')>Admin TPI</option>
                                <option value="verifikator" @selected(old('role', $akun->role ?? '') === 'verifikator')>Verifikator</option>
                                <option value="pimpinan" @selected(old('role', $akun->role ?? '') === 'pimpinan')>Pimpinan</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6" id="tpiAssignmentField">
                            <label for="tpi_id" class="form-label fw-semibold">Penugasan TPI <span class="text-danger">*</span></label>
                            <select name="tpi_id" id="tpi_id" class="form-select @error('tpi_id') is-invalid @enderror">
                                <option value="">-- Pilih TPI --</option>
                                @foreach($tpiList as $tpi)
                                    <option value="{{ $tpi->id }}" @selected((string) old('tpi_id', $akun->tpi_id ?? '') === (string) $tpi->id)>{{ $tpi->nama_tpi }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Wajib untuk Admin TPI agar data hanya berasal dari TPI yang ditugaskan.</small>
                            @error('tpi_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold">Password {{ $isEditing ? '(kosongkan jika tidak diubah)' : '' }} <span class="text-danger">{{ $isEditing ? '' : '*' }}</span></label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" minlength="8" {{ $isEditing ? '' : 'required' }}>
                            <small class="text-muted">Minimal 8 karakter.</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password {{ $isEditing ? '(jika password diubah)' : '' }} <span class="text-danger">{{ $isEditing ? '' : '*' }}</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" minlength="8" {{ $isEditing ? '' : 'required' }}>
                        </div>
                        @if($isEditing)
                            <div class="col-md-6">
                                <label for="is_active" class="form-label fw-semibold">Status Akun <span class="text-danger">*</span></label>
                                <select name="is_active" id="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                                    <option value="1" @selected(old('is_active', $akun->is_active ? '1' : '0') === '1')>Aktif — dapat login</option>
                                    <option value="0" @selected(old('is_active', $akun->is_active ? '1' : '0') === '0')>Nonaktif — akses login ditutup</option>
                                </select>
                                <small class="text-muted">Akun dinonaktifkan tetap tersimpan untuk jejak audit dan dapat diaktifkan kembali.</small>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                        <a href="{{ route('akun.index') }}" class="btn btn-outline-secondary px-4"><i class="bi bi-arrow-left me-1"></i> Batal</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm"><i class="bi bi-check2-circle me-1"></i> {{ $submitLabel }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const role = document.getElementById('role');
        const tpiField = document.getElementById('tpiAssignmentField');
        const tpiSelect = document.getElementById('tpi_id');

        const syncTpiAssignment = () => {
            const isAdminTpi = role.value === 'admin_tpi';
            tpiField.classList.toggle('d-none', !isAdminTpi);
            tpiSelect.disabled = !isAdminTpi;
            tpiSelect.required = isAdminTpi;
        };

        role.addEventListener('change', syncTpiAssignment);
        syncTpiAssignment();
    });
</script>
@endpush
