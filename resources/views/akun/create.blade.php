@extends('layouts.app')

@section('title', 'Tambah Akun')

@section('content')
<div class="container-fluid p-0">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('akun.index') }}" class="text-decoration-none">Manajemen Akun</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Akun</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2"><i class="bi bi-person-plus-fill text-primary"></i> Tambah Akun Pengguna</h4>
    </div>

    @include('akun._form', ['formAction' => route('akun.store'), 'submitLabel' => 'Simpan Akun'])
</div>
@endsection
