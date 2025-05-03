<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KatergoriModel;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        return KatergoriModel::all();
    }

    public function store(Request $request)
    {
        $kategori = KatergoriModel::create($request->all());
        return response()->json($kategori, 201);
    }

    public function show(KatergoriModel $kategori)
    {
        return KatergoriModel::find($kategori);
    }

    public function update(Request $request, KatergoriModel $kategori)
    {
        $kategori->update($request->all());
        return KatergoriModel::find($kategori);
    }

    public function destroy(KatergoriModel $kategori)
    {
        $kategori->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data terhapus',
        ]);
    }
}
