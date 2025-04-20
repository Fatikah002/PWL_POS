<?php

namespace App\Http\Controllers;

use App\Models\StokModel;
use App\Models\BarangModel;
use App\Models\SupplierModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class StokController extends Controller
{
    public function index()
    {
        $activeMenu = 'stok';
        $breadcrumb = (object)[
            'title' => 'Data Stok',
            'list' => ['Home', 'Stok']
        ];

        $barang = BarangModel::all();
        $supplier = SupplierModel::all();
        $user = UserModel::all();

        return view('stok.index', compact('activeMenu', 'breadcrumb', 'barang', 'supplier', 'user'));
    }

    public function list(Request $request)
    {
        $stok = StokModel::with(['barang', 'supplier', 'user']);

        return DataTables::of($stok)
            ->addIndexColumn()
            ->addColumn('aksi', function ($stok) {
                $btn  = '<button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/delete_ajax') . '\')"  class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        $barang = BarangModel::all();
        $supplier = SupplierModel::all();
        $user = UserModel::all();

        return view('stok.create_ajax', compact('barang', 'supplier', 'user'));
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax()) {
            $rules = [
                'supplier_id' => 'required|exists:m_supplier,supplier_id',
                'barang_id' => 'required|exists:m_barang,barang_id',
                'user_id' => 'required|exists:m_user,user_id',
                'stok_jumlah' => 'required|integer|min:0',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $data = $request->all();
            $data['stok_tanggal'] = now(); // Menambahkan tanggal otomatis

            StokModel::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Data stok berhasil disimpan'
            ]);
        }

        return redirect('/');
    }


    public function edit_ajax($id)
    {
        $stok = StokModel::find($id);
        $barang = BarangModel::all();
        $supplier = SupplierModel::all();
        $user = UserModel::all();

        return view('stok.edit_ajax', compact('stok', 'barang', 'supplier', 'user'));
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax()) {
            $rules = [
                'supplier_id'   => 'required|exists:m_supplier,supplier_id',
                'barang_id'     => 'required|exists:m_barang,barang_id',
                'user_id'       => 'required|exists:m_user,user_id',
                'stok_jumlah'   => 'required|integer|min:1',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $data = $request->all();

            $stok = StokModel::find($id);
            if ($stok) {
                $stok->update($data);

                if ($stok) {
                    $stok->update($request->all());
                    return response()->json([
                        'status' => true,
                        'message' => 'Data stok berhasil diupdate'
                    ]);
                }

                return response()->json([
                    'status' => false,
                    'message' => 'Data stok tidak ditemukan'
                ]);
            }

            return redirect('/');
        }
    }

    public function show_ajax($id)
    {
        $stok = StokModel::with(['barang', 'supplier', 'user'])->find($id);
        return view('stok.show_ajax', compact('stok'));
    }

    public function confirm_ajax($id)
    {
        $stok = StokModel::find($id);
        return view('stok.confirm_ajax', ['stok' => $stok]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax()) {
            $stok = StokModel::find($id);
            if ($stok) {
                $stok->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data stok berhasil dihapus'
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Data stok tidak ditemukan'
            ]);
        }

        return redirect('/');
    }

    public function import()
    {
        return view('stok.import');
    }
    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_stok' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('file_stok');

            try {
                $reader = IOFactory::createReader('Xlsx');
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();

                $data = $sheet->toArray(null, false, true, true);
                if (count($data) > 1) {
                    foreach ($data as $baris => $value) {
                        if ($baris > 1) {
                            $insert[] = [
                                'user_id' => $value['A'], 
                                'supplier_id' => $value['B'], 
                                'barang_id' => $value['C'], 
                                'stok_tanggal' => now(),
                                'stok_jumlah' => $value['E'], 
                                'created_at' => now(), 
                            ];
                        }
                    }

                    if (count($insert) > 0) {
                        $insertResult = StokModel::insertOrIgnore($insert);

                        if (!$insertResult) {
                            Log::error('Gagal menyisipkan data stok.', [
                                'data' => $insert,
                                'error' => 'Data stok tidak berhasil dimasukkan.'
                            ]);

                            return response()->json([
                                'status'  => false,
                                'message' => 'Gagal menyisipkan data ke database.'
                            ]);
                        }
                    }

                    return response()->json([
                        'status'  => true,
                        'message' => 'Data stok berhasil diimport'
                    ]);
                } else {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Tidak ada data yang diimport'
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error saat memproses file Excel stok.', [
                    'error' => $e->getMessage(),
                    'file' => $file->getRealPath()
                ]);
                return response()->json([
                    'status'  => false,
                    'message' => 'Terjadi kesalahan saat memproses file stok.'
                ]);
            }
        }

        return redirect('/');
    }



    public function export_excel()
    {
        //ambil data stok yang akan di export
        $stok = StokModel::select('user_id', 'supplier_id', 'barang_id', 'stok_tanggal', 'stok_jumlah')
            ->orderBy('barang_id')
            ->with('barang')
            ->get();

        // load library excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Pengguna');
        $sheet->setCellValue('C1', 'Nama Supplier');
        $sheet->setCellValue('D1', 'Nama Barang');
        $sheet->setCellValue('E1', 'Tanggal Stok');
        $sheet->setCellValue('F1', 'Jumlah Stok');

        $sheet->getStyle('A1:F1')->getFont()->setBold(true); // bold header

        $no = 1;        // nomor data dimulai dari 1
        $baris = 2;     //baris data dimulai dari baris ke 2
        foreach ($stok as $key => $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->user->nama);
            $sheet->setCellValue('C' . $baris, $value->supplier->supplier_nama);
            $sheet->setCellValue('D' . $baris, $value->barang->barang_nama);
            $sheet->setCellValue('E' . $baris, \Carbon\Carbon::parse($value->stok_tanggal)->format('Y-m-d H:i:s'));
            $sheet->setCellValue('F' . $baris, $value->stok_jumlah);
            $baris++;
            $no++;
        }

        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true); //set auto size untuk kolom
        }

        $sheet->setTitle('Data Stok'); // set title sheet
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Stok ' . date('Y-m-d H:i:s') . '.xlsx';
        header('Content-Type: application/vnd. openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        $writer->save('php://output');
        exit;
    } // end function export_excel

    public function export_pdf()
    {
        $stok = StokModel::select('user_id', 'supplier_id', 'barang_id', 'stok_tanggal', 'stok_jumlah')
            ->orderBy('barang_id')
            ->with('barang')
            ->get();


        //use Barryvdh\DomPDF\Facade\Pdf;
        $pdf = Pdf::loadView('stok.export_pdf', ['stok' => $stok]);
        $pdf->setPaper('a4', 'potrait'); //Set ukuran kertas dan orientasi
        $pdf->setOption('isRemoteEnabled', true); // set true jika ada gambar dari url
        $pdf->render();

        return $pdf->stream('Data Stok ' . date('Y-m-d H:i:s') . '.pdf');
    }
}
