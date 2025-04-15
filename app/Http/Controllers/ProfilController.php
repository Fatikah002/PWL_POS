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
        $profil = ProfilModel::where('user_id', Auth::id())->first();

        if ($request->hasFile('foto')) {
            // Hapus foto lama (selain default.png)
            if ($profil->foto && $profil->foto !== 'default.png') {
                Storage::delete('public/foto/' . $profil->foto);
            }

            // Simpan foto baru
            $file = $request->file('foto');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/foto', $filename);

            // Update nama file di database
            $profil->foto = $filename;
        }

        $profil->save();

        return redirect()->route('profil.index')->with('success', 'Foto profil berhasil diperbarui.');
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
