@extends('layouts.app')

@section('title', 'Edit Akun - ' . $akun->name)

@section('content')
<div class="container-fluid p-0">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('akun.index') }}" class="text-decoration-none">Manajemen Akun</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Akun</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2"><i class="bi bi-pencil-square text-primary"></i> Edit Akun: <span class="text-primary">{{ $akun->name }}</span></h4>
    </div>

    @include('akun._form', ['formAction' => route('akun.update', $akun), 'submitLabel' => 'Perbarui Akun', 'akun' => $akun])
</div>
@endsection
