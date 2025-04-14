<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{

    public function uploadFoto(Request $request)
{
    try {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['status' => 'unauthorized'], 401);
        }

        if ($request->hasFile('foto')) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            $path = $request->file('foto')->store('foto-profil', 'public');
            $user->foto = $path;
            $user->save();

            return response()->json([
                'status' => 'success',
                'foto_url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['status' => 'error'], 400);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}
}
