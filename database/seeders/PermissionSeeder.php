<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
        // Master
            ['name' => 'master_produk.view',   'label' => 'Lihat Produk'],
            ['name' => 'master_produk.create', 'label' => 'Tambah Produk'],
            ['name' => 'master_produk.delete', 'label' => 'Hapus Produk'],

            ['name' => 'categories.create', 'label' => 'Tambah Kategori'],
            ['name' => 'kategori.destroy', 'label' => 'Hapus Kategori'],

            ['name' => 'units.create', 'label' => 'Tambah Satuan'],
            ['name' => 'units.destroy', 'label' => 'Hapus Satuan'],

            // Penjualan
            ['name' => 'penjualan.show',   'label' => 'Lihat Faktur Penjualan'],
            ['name' => 'penjualan.create', 'label' => 'Tambah Faktur Penjualan'],
            ['name' => 'penjualan.edit',   'label' => 'Edit Faktur Penjualan'],
            ['name' => 'penjualan.batal', 'label' => 'Batalkan Faktur Penjualan'],
            ['name' => 'penjualan.approve','label' => 'Approve Pelunasan'],
            ['name' => 'penjualan.unapprove','label' => 'Unapprove Pelunasan'],

            ['name' => 'retur-penjualan.show',   'label' => 'Lihat Retur Penjualan'],
            ['name' => 'retur-penjualan.create', 'label' => 'Tambah Retur Penjualan'],
            ['name' => 'retur-penjualan.destroy', 'label' => 'Hapus Retur Penjualan'],

            ['name' => 'histori-harga-jual.index', 'label' => 'Lihat Histori Harga Penjualan'],

            // Pembelian
            ['name' => 'pembelian.show',   'label' => 'Lihat Faktur Pembelian'],
            ['name' => 'pembelian.create', 'label' => 'Tambah Faktur Pembelian'],
            ['name' => 'pembelian.edit',   'label' => 'Edit Faktur Pembelian'],
            ['name' => 'pembelian.batal', 'label' => 'Batalkan Faktur Pembelian'],
            ['name' => 'pembelian.approve','label' => 'Approve Pelunasan Pembelian'],
            ['name' => 'pembelian.unapprove','label' => 'Unapprove Pelunasan Pembelian'],

            ['name' => 'retur-pembelian.show',   'label' => 'Lihat Retur Pembelian'],
            ['name' => 'retur-pembelian.create', 'label' => 'Tambah Retur Pembelian'],
            ['name' => 'retur-pembelian.destroy', 'label' => 'Hapus Retur Pembelian'],

            ['name' => 'histori-harga-beli.index', 'label' => 'Lihat Histori Harga Pembelian'],

            // Laporan
            ['name' => 'sales_report.index', 'label' => 'Lihat Laporan Penjualan'],
            ['name' => 'purchases_report.index', 'label' => 'Lihat Laporan Pembelian'],

            // Daftar pihak
            ['name' => 'customers.show',   'label' => 'Lihat Pelanggan'],
            ['name' => 'customers.create', 'label' => 'Tambah Pelanggan'],
            ['name' => 'customers.edit',   'label' => 'Edit Pelanggan'],
            ['name' => 'customers.destroy', 'label' => 'Hapus Pelanggan'],

            ['name' => 'suppliers.show',   'label' => 'Lihat Pemasok'],
            ['name' => 'suppliers.create', 'label' => 'Tambah Pemasok'],
            ['name' => 'suppliers.edit',   'label' => 'Edit Pemasok'],
            ['name' => 'suppliers.destroy', 'label' => 'Hapus Pemasok'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm['name']], $perm);
        }

        // mapping default role-permission
        $superadmin = Role::where('name', 'superadmin')->first();
        $admin      = Role::where('name', 'admin')->first();
        $staf       = Role::where('name', 'staf')->first();

        // superadmin -> semua permission
        if ($superadmin) {
            $superadmin->permissions()->sync(Permission::all()->pluck('id'));
        }

        // admin -> hampir semua, kecuali hapus kritikal (boleh kamu atur)
        if ($admin) {
            $adminPerms = Permission::whereNotIn('name', [
                'master_produk.destroy',
                'retur-penjualan.destroy',
                'retur-pembelian.destroy',
            ])->pluck('id');
            $admin->permissions()->sync($adminPerms);
        }

        // staf -> fokus transaksi + view master + view laporan
        if ($staf) {
            $stafPerms = Permission::whereIn('name', [
                'master_produk.show',

                'penjualan.show',
                'penjualan.create',
                'penjualan.edit',

                'retur-penjualan.show',
                'retur-penjualan.create',

                'pembelian.show',
                'pembelian.create',
                'pembelian.edit',

                'retur-pembelian.show',
                'retur-pembelian.create',

                'sales_report.index',
                'purchases_report.index',
            ])->pluck('id');

            $staf->permissions()->sync($stafPerms);
        }
    }
}
