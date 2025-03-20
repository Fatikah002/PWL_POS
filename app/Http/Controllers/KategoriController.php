<?php

namespace App\Http\Controllers;

use App\Models\KatergoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar Kategori',
            'list'  => ['Home', 'kategori']
        ];

        $page = (object) [
            'title' => 'Daftar Kategori yang terdaftar dalam sistem'
        ];

        $activeMenu = 'kategori'; // set menu yang sedang aktif

        $kategori = KatergoriModel::all();

        return view('kategori.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }

    // Ambil data user dalam bentuk json untuk datatables
    public function list(Request $request)
    {
        $categories = KatergoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama');

        if ($request->kategori_id) {
            $categories->where('kategori_id', $request->kategori_id);
        }

        return DataTables::of($categories)
            //menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addIndexColumn()
            ->addColumn('aksi', function ($kategori) { //menambahkan kolom aksi
                // $btn  = '<a href="' . url('/kategori/' . $kategori->kategori_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                // $btn .= '<a href="' . url('/kategori/' . $kategori->kategori_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                // $btn .= '<form class="d-inline-block" method="POST" action="' . url('/kategori/' . $kategori->kategori_id) . '">'
                //     . csrf_field() . method_field('DELETE') .
                //     '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
                $btn  = '<button onclick="modalAction(\''.url('/kategori/' . $kategori->kategori_id . '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id . '/delete_ajax') . '\')"  class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    // Menampilkan halaman form tambah kategori
    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah kategori',
            'list'  => ['Home', 'kategori', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah kategori baru'
        ];

        $kategori = KatergoriModel::all(); // Ambil data kategori untuk ditampilkan di form
        $activeMenu = 'kategori'; // Set menu yang sedang aktif

        return view('kategori.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }

    // Menyimpan data kategori baru
    public function store(Request $request)
    {
        $request->validate([

            'kategori_kode'    => 'required|string|min:3|unique:m_kategori,kategori_kode',
            'kategori_nama'    => 'required|string|max:100'
        ]);

        KatergoriModel::create([
            'kategori_kode'      => $request->kategori_kode,
            'kategori_nama'      => $request->kategori_nama
        ]);

        return redirect('/kategori')->with('success', 'Data kategori berhasil disimpan');
    }

    // Menampilkan detail kategori
    public function show(string $id)
    {

        $kategori = KatergoriModel::find($id);

        $breadcrumb = (object) [
            'title' => 'Detail Kategori',
            'list'  => ['Home', 'Kategori', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail Kategori'
        ];

        $activeMenu = 'kategori'; // set menu yang sedang aktif

        return view('kategori.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }

    // Menampilkan halaman form edit kategori
    public function edit(string $id)
    {
        $kategori = KatergoriModel::find($id);

        $breadcrumb = (object) [
            'title' => 'Edit Kategori',
            'list'  => ['Home', 'Kategori', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit Kategori'
        ];

        $activeMenu = 'kategori'; // set menu yang sedang aktif

        return view('kategori.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }

    // Menyimpan perubahan data kategori
    public function update(Request $request, string $id)
    {
        $request->validate([
            'kategori_kode'      => 'required|string|min:3|unique:m_kategori,kategori_nama,' . $id . ',kategori_id',
            'kategori_nama'      => 'required|string|max:100',
        ]);

        KatergoriModel::find($id)->update([
            'kategori_kode'     => $request->kategori_kode,
            'kategori_nama'     => $request->kategori_nama

        ]);

        return redirect('/kategori')->with('success', 'Data kategori berhasil diubah');
    }

    // Menghapus data kategori
    public function destroy(string $id)
    {
        $check = KatergoriModel::find($id);
        if (!$check) {
            return redirect('/kategori')->with('error', 'Data kategori tidak ditemukan');
        }

        try {
            KatergoriModel::destroy($id); // Hapus data kategori

            return redirect('/kategori')->with('success', 'Data kategori berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            // Jika terjadi error ketika menghapus data, redirect kembali ke halaman dengan membawa pesan error
            return redirect('/kategori')->with('error', 'Data kategori gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax()
        {
                $kategori = KatergoriModel::select('kategori_kode', 'kategori_nama')->get();

                return view('kategori.create_ajax')
                        ->with('kategori', $kategori);
        }

        public function store_ajax(Request $request)
        {
                // cek apakah request berupa ajax
                if ($request->ajax() || $request->wantsJson()) {
                        $rules = [
                                'kategori_kode'    => 'required|string|min:3|unique:m_kategori,kategori_kode',
                                'kategori_nama'    => 'required|string|max:100'
                        ];

                        // use Illuminate\Support\Facades\Validator;
                        $validator = Validator::make($request->all(), $rules);

                        if ($validator->fails()) {
                                return response()->json([
                                        'status' => false, // response status, false: error/gagal, true: berhasil
                                        'message' => 'Validasi Gagal',
                                        'msgField' => $validator->errors(), // pesan error validasi
                                ]);
                        }

                        KatergoriModel::create($request->all());
                        return response()->json([
                                'status' => true,
                                'message' => 'Data level berhasil disimpan'
                        ]);
                }
                redirect('/');
        }

        //Menampilkan halaman form edit level ajax
        public function edit_ajax(string $id)
        {
                $kategori = KatergoriModel::find($id);

                return view('kategori.edit_ajax', ['kategori' => $kategori]);
        }

        //Mengakomodir request update data kategori melalui ajax
        public function update_ajax(Request $request, $id)
        {
                // cek apakah request dari ajax
                if ($request->ajax() || $request->wantsJson()) {
                        $rules = [
                                'kategori_kode' => 'required|string|min:3|unique:m_kategori,kategori_kode,' . $id . ',kategori_id',
                                'kategori_nama' => 'required|string|max:100',
                        ];

                        // use Illuminate\Support\Facades\Validator;
                        $validator = Validator::make($request->all(), $rules);

                        if ($validator->fails()) {
                                return response()->json([
                                        'status' => false, //respon json, true:berhasil, false:gagal
                                        'message' => 'Validasi gagal.',
                                        'msgField' => $validator->errors() // menunjukkan field mana yang error
                                ]);
                        }

                        $check = KatergoriModel::find($id);
                        if ($check) {
                                if (!$request->filled('password')) { // jika password tidak diisi, maka hapus dari request
                                        $request->request->remove('password');
                                }
                                $check->update($request->all());
                                return response()->json([
                                        'status' => true,
                                        'message' => 'Data berhasil diupdate'
                                ]);
                        } else {
                                return response()->json([
                                        'status' => false,
                                        'message' => 'Data tidak ditemukan'
                                ]);
                        }
                }
                return redirect('/');
        }

        public function confirm_ajax(string $id)
        {
                $kategori = KatergoriModel::find($id);

                return view('kategori.confirm_ajax', ['kategori' => $kategori]);
        }

        public function delete_ajax(Request $request, $id)
        {
                if ($request->ajax() || $request->wantsJson()) {
                        try {
                                $kategori = KatergoriModel::find($id);
                                if ($kategori) {
                                        $kategori->delete();
                                        return response()->json([
                                                'status' => true,
                                                'message' => 'Data berhasil dihapus'
                                        ]);
                                } else {
                                        return response()->json([
                                                'status' => false,
                                                'message' => 'Data tidak ditemukan'
                                        ]);
                                }
                        } catch (\Exception $e) {
                                return response()->json([
                                        'status' => false,
                                        'message' => 'Data tidak dapat dihapus karena terhubung dengan data lain'
                                ]);
                        }
                }
                return redirect('/');
        }

        //menampilkan detail dengan ajax
        public function show_ajax($id)
        {
                $kategori = KatergoriModel::find($id);
                return view('kategori.show_ajax', ['kategori' => $kategori]);
        }
}
