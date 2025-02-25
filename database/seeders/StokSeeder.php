<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = 
        [
            [ 'stok_id' => 1, 'supplier_id' => 1, 'barang_id' => 1, 'user_id' => 1, 'stok_tanggal' => '2024-02-01 10:00:00', 'stok_jumlah' => 30 ],
            [ 'stok_id' => 2, 'supplier_id' => 2, 'barang_id' => 2, 'user_id' => 1, 'stok_tanggal' => '2024-02-02 11:00:00', 'stok_jumlah' => 25 ],
            [ 'stok_id' => 3, 'supplier_id' => 3, 'barang_id' => 3, 'user_id' => 1, 'stok_tanggal' => '2024-02-03 12:00:00', 'stok_jumlah' => 40 ],
            [ 'stok_id' => 4, 'supplier_id' => 1, 'barang_id' => 4, 'user_id' => 1, 'stok_tanggal' => '2024-02-04 13:00:00', 'stok_jumlah' => 35 ],
            [ 'stok_id' => 5, 'supplier_id' => 2, 'barang_id' => 5, 'user_id' => 1, 'stok_tanggal' => '2024-02-05 14:00:00', 'stok_jumlah' => 20 ],
            [ 'stok_id' => 6, 'supplier_id' => 3, 'barang_id' => 6, 'user_id' => 2, 'stok_tanggal' => '2024-02-06 15:00:00', 'stok_jumlah' => 50 ],
            [ 'stok_id' => 7, 'supplier_id' => 1, 'barang_id' => 7, 'user_id' => 2, 'stok_tanggal' => '2024-02-07 16:00:00', 'stok_jumlah' => 15 ],
            [ 'stok_id' => 8, 'supplier_id' => 2, 'barang_id' => 8, 'user_id' => 2, 'stok_tanggal' => '2024-02-08 17:00:00', 'stok_jumlah' => 45 ],
            [ 'stok_id' => 9, 'supplier_id' => 3, 'barang_id' => 9, 'user_id' => 2, 'stok_tanggal' => '2024-02-09 18:00:00', 'stok_jumlah' => 32 ],
            [ 'stok_id' => 10, 'supplier_id' => 1, 'barang_id' => 10, 'user_id' => 2, 'stok_tanggal' => '2024-02-10 19:00:00', 'stok_jumlah' => 28 ],
            [ 'stok_id' => 11, 'supplier_id' => 2, 'barang_id' => 11, 'user_id' => 3, 'stok_tanggal' => '2024-02-11 20:00:00', 'stok_jumlah' => 38 ],
            [ 'stok_id' => 12, 'supplier_id' => 3, 'barang_id' => 12, 'user_id' => 3, 'stok_tanggal' => '2024-02-12 21:00:00', 'stok_jumlah' => 26 ],
            [ 'stok_id' => 13, 'supplier_id' => 1, 'barang_id' => 13, 'user_id' => 3, 'stok_tanggal' => '2024-02-13 22:00:00', 'stok_jumlah' => 18 ],
            [ 'stok_id' => 14, 'supplier_id' => 2, 'barang_id' => 14, 'user_id' => 3, 'stok_tanggal' => '2024-02-14 23:00:00', 'stok_jumlah' => 42 ],
            [ 'stok_id' => 15, 'supplier_id' => 3, 'barang_id' => 15, 'user_id' => 3, 'stok_tanggal' => '2024-02-15 09:00:00', 'stok_jumlah' => 33 ],
        ];
        DB::table('t_stok')->insert($data);
    }
}
