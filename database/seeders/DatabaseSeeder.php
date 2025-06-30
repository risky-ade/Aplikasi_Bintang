<?php

namespace Database\Seeders;

use App\Models\MasterKategori;
use App\Models\Pelanggan;
use App\Models\Satuan;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Risky Ade Sucahyo',
            'username' => 'riskyade',
            'email' => 'riskyadesucahyo@gmail.com',
            'password' => bcrypt('12345'),
            
        ]);

        Satuan::create([
            'jenis_satuan'=>'PAC',
            'keterangan_satuan'=>'Paket/sap',
        ]);

        MasterKategori::create([
            'kode_kategori'=>'ATK001',
            'nama_kategori'=>'Kertas',
        ]);

        Pelanggan::create([
            'nama'=>'Risky',
            'email'=>'risky.gmail.com',
            'npwp'=>'12345678',
            'no_hp'=>'12345678',
            'kota'=>'surabaya',
            'provinsi'=>'Jawa Timur',
            'alamat'=>'jl sby',
        ]);
    }
}
