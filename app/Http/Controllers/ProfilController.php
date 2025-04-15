<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\ProfilModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Profil',
            'list' => ['Home', 'Profil']
        ];

        $activeMenu = 'profil';

        $profil = ProfilModel::where('user_id', Auth::user()->user_id)->first();

        return view('profil.index', compact('breadcrumb', 'activeMenu', 'profil'));
    }

    public function edit()
    {
        $profil = ProfilModel::where('user_id', Auth::user()->user_id)->first();

        $breadcrumb = (object) [
            'title' => 'Edit Profil',
            'list' => ['Home', 'Profil', 'Edit Profil']
        ];
        $activeMenu = 'profil';

        return view('profil.edit', compact('profil', 'activeMenu', 'breadcrumb'));
    }


    public function update(Request $request)
    {
        $request->validate([
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $profil = ProfilModel::firstOrCreate(
            ['user_id' => Auth::user()->user_id],
            ['foto' => null]
        );

        if ($request->hasFile('foto')) {
            if ($profil->foto && Storage::exists('public/foto/' . $profil->foto)) {
                Storage::delete('public/foto/' . $profil->foto);
            }

            $foto = $request->file('foto');
            $namaFoto = time() . '.' . $foto->getClientOriginalExtension();
            $foto->storeAs('public/foto', $namaFoto);

            $profil->foto = $namaFoto;
            $profil->save();
        }

        return redirect()->route('profil.index')->with('success', 'Profil berhasil diperbarui');
    }

    public function delete()
    {
        $userId = Auth::user()->user_id;
        $profil = ProfilModel::where('user_id', $userId)->first();

        if ($profil && $profil->foto && $profil->foto != 'default.png') {
            // Hapus file lama dari storage
            Storage::disk('public')->delete('foto/' . $profil->foto);
        }

        // Setel ulang ke default
        $profil->foto = 'default.png';
        $profil->save();

        return redirect()->route('profil.index')->with('success', 'Foto profil berhasil dihapus.');
    }
}