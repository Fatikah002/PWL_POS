@extends('layouts.template')

@section('content')
    <div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="card text-center shadow-lg p-4" style="border-radius: 20px; max-width: 350px; width: 100%;">

            <div class="d-flex justify-content-center mb-3">
                <img src="{{ $profil && $profil->foto ? asset('storage/foto/' . $profil->foto) : asset('storage/foto/default.png') }}"
                    alt="Foto Profil" class="rounded-circle border border-3"
                    style="width: 150px; height: 150px; object-fit: cover;">
            </div>

            <h4 class="fw-bold mb-1">{{ Auth::user()->nama }}</h4>
            <p class="text-muted mb-3">{{ '@' . Auth::user()->username }}</p>

            <div class="d-flex flex-column align-items-center">
                <a href="{{ route('profil.edit') }}" class="btn btn-outline-primary btn-sm px-3 rounded-pill mb-2">Edit
                    Foto</a>

                @if ($profil->foto && $profil->foto != 'default.png')
                    <a href="{{ route('profil.delete') }}" onclick="return confirm('Yakin ingin menghapus foto profil?')"
                        class="btn btn-outline-danger btn-sm px-3 rounded-pill">Hapus Foto</a>
                @endif
            </div>
        </div>
    </div>
@endsection
