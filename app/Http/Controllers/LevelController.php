<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class LevelController extends Controller
{
        public function index()
        {
                $breadcrumb = (object)[
                        'title' => 'Daftar Level',
                        'list'  => ['Home', 'level']
                ];

                $page = (object) [
                        'title' => 'Daftar Level yang terdaftar dalam sistem'
                ];

                $activeMenu = 'level'; // set menu yang sedang aktif

                $level = LevelModel::all();

                return view('level.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
        }

        // Ambil data user dalam bentuk json untuk datatables
        public function list(Request $request)
        {
                $levels = LevelModel::select('level_id', 'level_kode', 'level_nama');

                if ($request->level_id) {
                        $levels->where('level_id', $request->level_id);
                }

                return DataTables::of($levels)
                        //menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
                        ->addIndexColumn()
                        ->addColumn('aksi', function ($level) { //menambahkan kolom aksi
                                // $btn  = '<a href="' . url('/user/' . $user->user_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                                //$btn .= '<a href="' . url('/user/' . $user->user_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                                //$btn .= '<form class="d-inline-block" method="POST" action="' . url('/user/' . $user->user_id) . '">'
                                //   . csrf_field() . method_field('DELETE') .
                                //  '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
                                $btn  = '<button onclick="modalAction(\''.url('/level/' . $level->level_id . '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button> ';
                                $btn .= '<button onclick="modalAction(\'' . url('/level/' . $level->level_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                                $btn .= '<button onclick="modalAction(\'' . url('/level/' . $level->level_id . '/delete_ajax') . '\')"  class="btn btn-danger btn-sm">Hapus</button> ';
                                return $btn;
                        })
                        ->rawColumns(['aksi'])
                        ->make(true);
        }

        // Menampilkan halaman form tambah level
        public function create()
        {
                $breadcrumb = (object) [
                        'title' => 'Tambah level',
                        'list'  => ['Home', 'level', 'Tambah']
                ];

                $page = (object) [
                        'title' => 'Tambah level baru'
                ];

                $level = LevelModel::all(); // Ambil data level untuk ditampilkan di form
                $activeMenu = 'level'; // Set menu yang sedang aktif

                return view('level.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
        }

        // Menyimpan data level baru
        public function store(Request $request)
        {
                $request->validate([

                        'level_kode'    => 'required|string|min:3|unique:m_level,level_kode',
                        'level_nama'    => 'required|string|max:100'
                ]);

                LevelModel::create([
                        'level_kode'      => $request->level_kode,
                        'level_nama'      => $request->level_nama
                ]);

                return redirect('/level')->with('success', 'Data level berhasil disimpan');
        }

        // Menampilkan detail level
        public function show(string $id)
        {

                $level = LevelModel::find($id);

                $breadcrumb = (object) [
                        'title' => 'Detail Level',
                        'list'  => ['Home', 'Level', 'Detail']
                ];

                $page = (object) [
                        'title' => 'Detail Level'
                ];

                $activeMenu = 'level'; // set menu yang sedang aktif

                return view('level.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
        }

        // Menampilkan halaman form edit level
        public function edit(string $id)
        {
                $level = LevelModel::find($id);
                // $level = LevelModel::all();

                $breadcrumb = (object) [
                        'title' => 'Edit Level',
                        'list'  => ['Home', 'Level', 'Edit']
                ];

                $page = (object) [
                        'title' => 'Edit level'
                ];

                $activeMenu = 'level'; // set menu yang sedang aktif

                return view('level.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
        }

        // Menyimpan perubahan data level
        public function update(Request $request, string $id)
        {
                $request->validate([
                        'level_kode'      => 'required|string|min:3|unique:m_level,level_nama,' . $id . ',level_id',
                        'level_nama'      => 'required|string|max:100',
                ]);

                LevelModel::find($id)->update([
                        'level_kode'     => $request->level_kode,
                        'level_nama'     => $request->level_nama

                ]);

                return redirect('/level')->with('success', 'Data level berhasil diubah');
        }

        // Menghapus data level
        public function destroy(string $id)
        {
                $check = LevelModel::find($id);
                if (!$check) {
                        return redirect('/level')->with('error', 'Data level tidak ditemukan');
                }

                try {
                        LevelModel::destroy($id); // Hapus data level

                        return redirect('/level')->with('success', 'Data level berhasil dihapus');
                } catch (\Illuminate\Database\QueryException $e) {
                        // Jika terjadi error ketika menghapus data, redirect kembali ke halaman dengan membawa pesan error
                        return redirect('/level')->with('error', 'Data level gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
                }
        }

        public function create_ajax()
        {
                $level = LevelModel::select('level_kode', 'level_nama')->get();

                return view('level.create_ajax')
                        ->with('level', $level);
        }

        public function store_ajax(Request $request)
        {
                // cek apakah request berupa ajax
                if ($request->ajax() || $request->wantsJson()) {
                        $rules = [
                                'level_kode'    => 'required|string|min:3|unique:m_level,level_kode',
                                'level_nama'    => 'required|string|max:100'
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

                        LevelModel::create($request->all());
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
                $level = LevelModel::find($id);

                return view('level.edit_ajax', ['level' => $level]);
        }

        //Mengakomodir request update data level melalui ajax
        public function update_ajax(Request $request, $id)
        {
                // cek apakah request dari ajax
                if ($request->ajax() || $request->wantsJson()) {
                        $rules = [
                                'level_kode' => 'required|string|min:3|unique:m_level,level_kode,' . $id . ',level_id',
                                'level_nama' => 'required|string|max:100',
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

                        $check = LevelModel::find($id);
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
                $level = LevelModel::find($id);

                return view('level.confirm_ajax', ['level' => $level]);
        }

        public function delete_ajax(Request $request, $id)
        {
                if ($request->ajax() || $request->wantsJson()) {
                        try {
                                $level = LevelModel::find($id);
                                if ($level) {
                                        $level->delete();
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
                $level = LevelModel::find($id);
                return view('level.show_ajax', ['level' => $level]);
        }
}
