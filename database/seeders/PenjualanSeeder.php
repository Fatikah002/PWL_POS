<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data =
            [
                ['penjualan_id' => 1, 'user_id' => 1, 'pembeli' => 'Budi Santoso', 'penjualan_kode' => 'TRX001', 'penjualan_tanggal' => '2024-02-01 10:30:00'],
                ['penjualan_id' => 2, 'user_id' => 1, 'pembeli' => 'Siti Aisyah', 'penjualan_kode' => 'TRX002', 'penjualan_tanggal' => '2024-02-02 11:45:00'],
                ['penjualan_id' => 3, 'user_id' => 1, 'pembeli' => 'Rudi Hartono', 'penjualan_kode' => 'TRX003', 'penjualan_tanggal' => '2024-02-03 14:00:00'],
                ['penjualan_id' => 4, 'user_id' => 1, 'pembeli' => 'Dewi Anggraini', 'penjualan_kode' => 'TRX004', 'penjualan_tanggal' => '2024-02-04 16:20:00'],
                ['penjualan_id' => 5, 'user_id' => 1, 'pembeli' => 'Agus Setiawan', 'penjualan_kode' => 'TRX005', 'penjualan_tanggal' => '2024-02-05 09:10:00'],
                ['penjualan_id' => 6, 'user_id' => 1, 'pembeli' => 'Tina Marlina', 'penjualan_kode' => 'TRX006', 'penjualan_tanggal' => '2024-02-06 12:40:00'],
                ['penjualan_id' => 7, 'user_id' => 1, 'pembeli' => 'Joko Wido', 'penjualan_kode' => 'TRX007', 'penjualan_tanggal' => '2024-02-07 15:15:00'],
                ['penjualan_id' => 8, 'user_id' => 1, 'pembeli' => 'Indah Lestari', 'penjualan_kode' => 'TRX008', 'penjualan_tanggal' => '2024-02-08 17:30:00'],
                ['penjualan_id' => 9, 'user_id' => 1, 'pembeli' => 'Bagus Prasetyo', 'penjualan_kode' => 'TRX009', 'penjualan_tanggal' => '2024-02-09 19:50:00'],
                ['penjualan_id' => 10, 'user_id' => 1, 'pembeli' => 'Ratna Sari', 'penjualan_kode' => 'TRX010', 'penjualan_tanggal' => '2024-02-10 08:25:00'],
            ];
        DB::table('t_penjualan')->insert($data);
    }
}
