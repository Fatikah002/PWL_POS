<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\KatergoriModel;
use Database\Seeders\BarangSeeder;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
        public function index()
        {
                $breadcrumb = (object)[
                        'title' => 'Daftar barang',
                        'list'  => ['Home', 'Barang']
                ];

                $page = (object) [
                        'title' => 'Daftar barang yang terdaftar dalam sistem'
                ];

                $activeMenu = 'barang'; // set menu yang sedang aktif

                $kategori = KatergoriModel::all(); //ambil data kategori untuk di filter

                return view('barang.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
        }

        // Ambil data user dalam bentuk json untuk datatables
        public function list(Request $request)
        {
                $barangs = BarangModel::select('barang_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual', 'kategori_id')
                        ->with('kategori');


                if ($request->kategori_id) {
                        $barangs->where('kategori_id', $request->kategori_id);
                }

                return DataTables::of($barangs)
                        //menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
                        ->addIndexColumn()
                        ->addColumn('aksi', function ($barang) { //menambahkan kolom aksi
                                //     $btn  = '<a href="' . url('/barang/' . $barang->barang_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                                //     $btn .= '<a href="' . url('/barang/' . $barang->barang_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                                //     $btn .= '<form class="d-inline-block" method="POST" action="' .url('/barang/' . $barang->barang_id) . '">'
                                //             .csrf_field() . method_field('DELETE') .
                                //             '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
                                $btn  = '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                                $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                                $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id . '/delete_ajax') . '\')"  class="btn btn-danger btn-sm">Hapus</button> ';
                                return $btn;
                        })
                        ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html 
                        ->make(true);
        }

        // Menampilkan halaman form tambah user
        public function create()
        {
                $breadcrumb = (object) [
                        'title' => 'Tambah Barang',
                        'list'  => ['Home', 'Barang', 'Tambah']
                ];

                $page = (object) [
                        'title' => 'Tambah barang baru'
                ];

                $kategori = KatergoriModel::all(); // Ambil data level untuk ditampilkan di form
                $activeMenu = 'barang'; // Set menu yang sedang aktif

                return view('barang.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
        }

        // Menyimpan data user baru
        public function store(Request $request)
        {
                $request->validate([
                        //     'barang_id'     => 'required|string|min:3|unique:m_barang,barang_id',
                        'barang_kode'   => 'required|string|max:10',
                        'barang_nama'   => 'required|string|max:100',
                        'harga_beli'    => 'required|integer',
                        'harga_jual'    => 'required|integer',
                        'kategori_id'   => 'required|integer'
                ]);

                BarangModel::create([
                        'barang_kode'   => $request->barang_kode,
                        'barang_nama'   => $request->barang_nama,
                        'harga_beli'    => $request->harga_beli,
                        'harga_jual'    => $request->harga_jual,
                        'kategori_id'   => $request->kategori_id
                ]);

                return redirect('/barang')->with('success', 'Data barang berhasil disimpan');
        }

        // Menampilkan detail user
        public function show(string $id)
        {

                $barang = BarangModel::with('kategori')->find($id);

                $breadcrumb = (object) [
                        'title' => 'Detail Barang',
                        'list'  => ['Home', 'Barang', 'Detail']
                ];

                $page = (object) [
                        'title' => 'Detail barang'
                ];

                $activeMenu = 'barang'; // set menu yang sedang aktif

                return view('barang.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'barang' => $barang, 'activeMenu' => $activeMenu]);
        }

        // Menampilkan halaman form edit user
        public function edit(string $id)
        {
                $barang     = BarangModel::find($id);
                $kategori   = KatergoriModel::all();

                $breadcrumb = (object) [
                        'title' => 'Edit Barang',
                        'list'  => ['Home', 'Barang', 'Edit']
                ];

                $page = (object) [
                        'title' => 'Edit barang'
                ];

                $activeMenu = 'barang'; // set menu yang sedang aktif

                return view('barang.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'barang' => $barang, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
        }

        // Menyimpan perubahan data user
        public function update(Request $request, string $id)
        {
                $request->validate([
                        'barang_kode'   => 'required|string|max:10',
                        'barang_nama'   => 'required|string|max:100',
                        'harga_beli'    => 'required|integer',
                        'harga_jual'    => 'required|integer',
                        'kategori_id'   => 'required|integer'
                ]);

                BarangModel::find($id)->update([
                        'barang_kode'   => $request->barang_kode,
                        'barang_nama'   => $request->barang_nama,
                        'harga_beli'    => $request->harga_beli,
                        'harga_jual'    => $request->harga_jual,
                        'kategori_id'   => $request->kategori_id
                ]);

                return redirect('/barang')->with('success', 'Data barang berhasil diubah');
        }

        // Menghapus data user
        public function destroy(string $id)
        {
                $check = BarangModel::find($id);
                if (!$check) {
                        return redirect('/barang')->with('error', 'Data barang tidak ditemukan');
                }

                try {
                        BarangModel::destroy($id); // Hapus data barang

                        return redirect('/barang')->with('success', 'Data barang berhasil dihapus');
                } catch (\Illuminate\Database\QueryException $e) {
                        // Jika terjadi error ketika menghapus data, redirect kembali ke halaman dengan membawa pesan error
                        return redirect('/barang')->with('error', 'Data barang gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
                }
        }

        // Menambah data baru dengan ajax
        public function create_ajax()
        {
                $kategori = KatergoriModel::select('kategori_id', 'kategori_nama')->get();

                return view('barang.create_ajax')
                        ->with('kategori', $kategori);
        }

        //Simpan data melalui ajax
        public function store_ajax(Request $request)
        {
                //cek apakah request berupa ajax
                if ($request->ajax() || $request->wantsJson()) {

                        $rules = [
                                'barang_kode' => 'required|string|min:3|unique:m_barang,barang_kode',
                                'barang_nama' => 'required|string|max:100',
                                'harga_beli' => 'required|integer',
                                'harga_jual' => 'required|integer',
                                'kategori_id' => 'required|integer'
                        ];

                        // use Illuminate\Support\Facades\Validator;
                        $validator = Validator::make($request->all(), $rules);

                        if ($validator->fails()) {
                                return response()->json([
                                        'status' => false, //response status, false: error/gagal, true:berhasil
                                        'message' => 'Validasi Gagal',
                                        'msgField' => $validator->errors(), //pesan error validasi
                                ]);
                        }

                        BarangModel::create($request->all());

                        return response()->json([
                                'status' => true,
                                'message' => 'Data barang berhasil disimpan'
                        ]);
                }
                redirect('/');
        }

        //Menampilkan halaman form edit barang ajax
        public function edit_ajax(string $id)
        {
                $barang = BarangModel::find($id);
                $kategori = KatergoriModel::select('kategori_id', 'kategori_nama')->get();

                return view('barang.edit_ajax', ['barang' => $barang, 'kategori' => $kategori]);
        }

        //Mengakomodir request update data barang melalui ajax
        public function update_ajax(Request $request, $id)
        {
                // cek apakah request dari ajax
                if ($request->ajax() || $request->wantsJson()) {
                        $rules = [
                                'barang_kode' => 'required|string|min:3|unique:m_barang,barang_kode,' . $id . ',barang_id',
                                'barang_nama' => 'required|string|max:100',
                                'harga_beli' => 'required|integer',
                                'harga_jual' => 'required|integer',
                                'kategori_id' => 'required|integer'
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

                        $check = BarangModel::find($id);
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

        //menampilkan detail data barang dengan ajax
        public function show_ajax($id)
        {
                $barang = BarangModel::with('kategori')->find($id);
                return view('barang.show_ajax', ['barang' => $barang]);
        }

        public function delete_ajax(Request $request, $id)
        {
                if ($request->ajax() || $request->wantsJson()) {
                        try {
                                $barang = BarangModel::find($id);
                                if ($barang) {
                                        $barang->delete();
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


        //Confirm ajax
        public function confirm_ajax(string $id)
        {
                $barang = BarangModel::find($id);

                return view('barang.confirm_ajax', ['barang' => $barang]);
        }
}
