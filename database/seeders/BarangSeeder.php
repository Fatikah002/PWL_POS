<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data =
            [
                [
                    'barang_id' => 1,
                    'kategori_id' => 1,
                    'barang_kode' => 'ELK001',
                    'barang_nama' => 'HP',
                    'harga_beli' => 3000000,
                    'harga_jual' => 3500000
                ],
                [
                    'barang_id' => 2,
                    'kategori_id' => 1,
                    'barang_kode' => 'ELK002',
                    'barang_nama' => 'Laptop',
                    'harga_beli' => 7000000,
                    'harga_jual' => 7500000
                ],
                [
                    'barang_id' => 3,
                    'kategori_id' => 1,
                    'barang_kode' => 'ELK003',
                    'barang_nama' => 'Smartwatch',
                    'harga_beli' => 1500000,
                    'harga_jual' => 1800000
                ],


                [
                    'barang_id' => 4,
                    'kategori_id' => 2,
                    'barang_kode' => 'PKN001',
                    'barang_nama' => 'Kaos Polos',
                    'harga_beli' => 50000,
                    'harga_jual' => 75000
                ],
                [
                    'barang_id' => 5,
                    'kategori_id' => 2,
                    'barang_kode' => 'PKN002',
                    'barang_nama' => 'Celana Jeans',
                    'harga_beli' => 200000,
                    'harga_jual' => 250000
                ],
                [
                    'barang_id' => 6,
                    'kategori_id' => 2,
                    'barang_kode' => 'PKN003',
                    'barang_nama' => 'Jaket Hoodie',
                    'harga_beli' => 180000,
                    'harga_jual' => 220000
                ],
                [
                    'barang_id' => 7,
                    'kategori_id' => 3,
                    'barang_kode' => 'MKN001',
                    'barang_nama' => 'Mie Instan',
                    'harga_beli' => 2500,
                    'harga_jual' => 3500
                ],
                [
                    'barang_id' => 8,
                    'kategori_id' => 3,
                    'barang_kode' => 'MKN002',
                    'barang_nama' => 'Biskuit Coklat',
                    'harga_beli' => 8000,
                    'harga_jual' => 10000
                ],
                [
                    'barang_id' => 9,
                    'kategori_id' => 3,
                    'barang_kode' => 'MKN003',
                    'barang_nama' => 'Snack Keju',
                    'harga_beli' => 5000,
                    'harga_jual' => 7500
                ],
                [
                    'barang_id' => 10,
                    'kategori_id' => 4,
                    'barang_kode' => 'MNM001',
                    'barang_nama' => 'Air Mineral',
                    'harga_beli' => 3000,
                    'harga_jual' => 4000
                ],
                [
                    'barang_id' => 11,
                    'kategori_id' => 4,
                    'barang_kode' => 'MNM002',
                    'barang_nama' => 'Kopi Instan',
                    'harga_beli' => 6000,
                    'harga_jual' => 8000
                ],
                [
                    'barang_id' => 12,
                    'kategori_id' => 4,
                    'barang_kode' => 'MNM003',
                    'barang_nama' => 'Jus Jeruk',
                    'harga_beli' => 12000,
                    'harga_jual' => 15000
                ],
                [
                    'barang_id' => 13,
                    'kategori_id' => 5,
                    'barang_kode' => 'ALT001',
                    'barang_nama' => 'Pensil 2B',
                    'harga_beli' => 2000,
                    'harga_jual' => 3000
                ],
                [
                    'barang_id' => 14,
                    'kategori_id' => 5,
                    'barang_kode' => 'ALT002',
                    'barang_nama' => 'Pulpen Hitam',
                    'harga_beli' => 4000,
                    'harga_jual' => 5000
                ],
                [
                    'barang_id' => 15,
                    'kategori_id' => 5,
                    'barang_kode' => 'ALT003',
                    'barang_nama' => 'Krayon',
                    'harga_beli' => 40000,
                    'harga_jual' => 50000
                ],

            ];
        DB::table('m_barang')->insert($data);
    }
}
