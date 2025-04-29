<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\PenjualanModel;
use App\Models\PenjualanDetailModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;

class PenjualanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Penjualan',
            'list'  => ['Home', 'Penjualan']
        ];

        $page = (object) [
            'title' => 'Daftar Transaksi Penjualan yang sudah terdaftar dalam sistem'
        ];

        $activeMenu = 'penjualan';

        $user = UserModel::all();

        return view('penjualan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'user' => $user,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        $penjualan = PenjualanModel::select(
            'penjualan_id',
            'user_id',
            'pembeli',
            'penjualan_kode',
            'penjualan_tanggal'
        )
            ->with('user')
            ->with('details');

        if ($request->user_id) {
            $penjualan->where('user_id', $request->user_id);
        }

        if ($request->start_date && $request->end_date) {
            $penjualan->whereBetween('penjualan_tanggal', [$request->start_date, $request->end_date]);
        }

        return DataTables::of($penjualan)
            ->addIndexColumn()
            ->addColumn('total_items', function ($penjualan) {
                return $penjualan->details->sum('jumlah');
            })
            ->addColumn('total_amount', function ($penjualan) {
                return $penjualan->getTotalAmount();
            })
            ->addColumn('aksi', function ($penjualan) {
                $btn = '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button>';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button>';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        $barang = BarangModel::select('barang_id', 'barang_nama', 'barang_kode', 'harga_jual', 'harga_beli')
            ->get();
        $user = UserModel::select('user_id', 'nama')->get();


        return view('penjualan.create_ajax')
            ->with('barang', $barang)
            ->with('user', $user);
    }

    public function store_ajax(Request $request)
{
    $rules = [
        'penjualan_kode' => 'required|unique:t_penjualan,penjualan_kode',
        'penjualan_tanggal' => 'required|date',
        'pembeli' => 'required|string|max:50',
        'items' => 'required|array|min:1',
        'items.*.barang_id' => 'required|exists:m_barang,barang_id',
        'items.*.harga' => 'required|numeric|min:1',
        'items.*.jumlah' => 'required|numeric|min:1',
    ];

    $messages = [
        'items.required' => 'Detail barang tidak boleh kosong',
        'items.*.barang_id.exists' => 'Barang tidak ditemukan',
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Ada kesalahan pada data yang dimasukkan',
            'msgField' => $validator->errors()
        ]);
    }

    DB::beginTransaction();

    try {
        // Simpan data penjualan
        $penjualan = PenjualanModel::create([
            'user_id' => $request->user_id,
            'pembeli' => $request->pembeli,
            'penjualan_kode' => $request->penjualan_kode,
            'penjualan_tanggal' => now(), // Gunakan tanggal yang diterima dari request
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Simpan detail penjualan
        foreach ($request->items as $item) {
            PenjualanDetailModel::create([
                'penjualan_id' => $penjualan->penjualan_id,
                'barang_id' => $item['barang_id'],
                'jumlah' => $item['jumlah'],
                'harga' => $item['harga'],
                'penjualan_tanggal' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        DB::commit();
        return response()->json([
            'status' => true,
            'message' => 'Transaksi penjualan berhasil disimpan'
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
}



    public function show_ajax(string $id)
    {
        $penjualan = PenjualanModel::with(['user', 'details.barang'])->find($id);

        if (!$penjualan) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        $total = $penjualan->getTotalAmount();

        return view('penjualan.show_ajax', [
            'penjualan' => $penjualan,
            'total' => $total
        ]);
    }

    public function edit_ajax(string $id)
    {
        $penjualan = PenjualanModel::with(['details.barang'])->find($id);

        if (!$penjualan) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        $barang = BarangModel::select('barang_id', 'barang_nama', 'barang_kode', 'harga_jual', 'harga_beli')
            ->get();
        $user = UserModel::select('user_id', 'nama')->get();

        return view('penjualan.edit_ajax', [
            'penjualan' => $penjualan,
            'barang' => $barang,
            'user' => $user
        ]);
    }

    public function update_ajax(Request $request, $id)
{
    // Menemukan penjualan berdasarkan ID
    $penjualan = PenjualanModel::findOrFail($id);

    // Validasi data utama
    $request->validate([
        'pembeli' => 'required|string|max:255',
        'detail' => 'required|array',
        'detail.*.barang_id' => 'required|exists:m_barang,barang_id',
        'detail.*.jumlah' => 'required|integer|min:1',
        'detail.*.harga' => 'required|numeric|min:0',
    ]);

    try {
        // Update data utama
        $penjualan->update([
            'pembeli' => $request->pembeli,
        ]);

        // Update setiap detail
        foreach ($request->detail as $item) {
            $detail = \App\Models\PenjualanDetailModel::where('penjualan_id', $penjualan->penjualan_id)
                        ->where('barang_id', $item['barang_id'])
                        ->first();

            if ($detail) {
                $detail->jumlah = $item['jumlah'];
                $detail->harga = $item['harga'];
                // $detail->total = $item['jumlah'] * $item['harga'];
                $detail->save();
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Transaksi berhasil diperbarui!',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
        ]);
    }

        
    // Update detail barang
    foreach ($request->detail as $detail) {
        $penjualanDetail = PenjualanDetailModel::find($detail['id']);
        $penjualanDetail->update([
            'jumlah' => $detail['jumlah'],
            'harga' => $detail['harga'],
        ]);
    }
    
    return response()->json([
        'status' => true,
        'message' => 'Data transaksi berhasil diperbarui.'
    ]);
        
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        $penjualan = PenjualanModel::with(['details.barang', 'user'])->find($id);

        if (!$penjualan) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return view('penjualan.confirm_ajax', [
            'penjualan' => $penjualan
        ]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            DB::beginTransaction();
            try {
                $penjualan = PenjualanModel::find($id);

                if (!$penjualan) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data penjualan tidak ditemukan',
                    ]);
                }

                PenjualanDetailModel::where('penjualan_id', $id)->delete();

                $penjualan->delete();

                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Data penjualan berhasil dihapus'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]);
            }
        }
        return redirect('/');
    }

    public function export_excel()
    {
        $penjualan = PenjualanModel::with(['user', 'details.barang'])
            ->orderBy('penjualan_tanggal', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Transaksi');
        $sheet->setCellValue('C1', 'Tanggal');
        $sheet->setCellValue('D1', 'Pembeli');
        $sheet->setCellValue('E1', 'Kasir');
        $sheet->setCellValue('F1', 'Total Item');
        $sheet->setCellValue('G1', 'Total Harga');

        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;

        foreach ($penjualan as $data) {
            $total_items = $data->details->sum('jumlah');
            $total_amount = $data->getTotalAmount();

            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $data->penjualan_kode);

            $excelDateTime = Date::PHPToExcel(new \DateTime($data->penjualan_tanggal));
            $sheet->setCellValue('C' . $baris, $excelDateTime);
            $sheet->getStyle('C' . $baris)->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm:ss');

            $sheet->setCellValue('D' . $baris, $data->pembeli);
            $sheet->setCellValue('E' . $baris, $data->user->nama);
            $sheet->setCellValue('F' . $baris, $total_items);
            $sheet->setCellValue('G' . $baris, $total_amount);

            $baris++;
            $no++;
        }

        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Penjualan');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Penjualan ' . date('Y-m-d H:i:s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $penjualan = PenjualanModel::with(['user', 'details.barang'])
            ->orderBy('penjualan_tanggal', 'desc')
            ->get();

        $pdf = Pdf::loadView('penjualan.export_pdf', ['penjualan' => $penjualan]);
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->render();

        return $pdf->stream('Data Penjualan ' . date('Y-m-d H:i:s') . '.pdf');
    }
}
