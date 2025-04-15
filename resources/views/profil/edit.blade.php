@extends('layouts.template')

@section('content')
    <div class="container mt-5">

        <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3 text-center">
                <img src="{{ $profil && $profil->foto ? asset('storage/foto/' . $profil->foto) : asset('default/default.png') }}"
                    class="rounded-circle" style="width: 200px; height: 200px; object-fit: cover; border: 3px solid #ccc;">
            </div>

            <div class="mb-3">
                <label for="foto" class="form-label">Foto Profil</label>
                <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                @error('foto')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ url('/profil') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection
