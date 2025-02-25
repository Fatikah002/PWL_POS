<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'supplier_id' => 1,
                'supplier_kode' => 001,
                'supplier_nama' => 'Supp A',
                'supplier_alamat' => 'Jl. Tembaga No.50',

            ],
            [
                'supplier_id' => 2,
                'supplier_kode' => 002,
                'supplier_nama' => 'Supp B',
                'supplier_alamat' => 'Jl. Mawar No.03',
            ],
            [
                'supplier_id' => 3,
                'supplier_kode' => 003,
                'supplier_nama' => 'Supp C',
                'supplier_alamat' => 'Jl. Sulawesi No.27',
            ],

        ];
        DB::table('m_supplier')->insert($data);
    }
}
