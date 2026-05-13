<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
        ]);

        $admin = User::factory()->create([
            'name' => 'Admin POS',
            'email' => 'admin@testing.com',
        ]);
        $admin->assignRole('super-admin');

        $pemilik = User::factory()->create([
            'name' => 'pemilik Toko',
            'email' => 'pemilik@testing.com',
        ]);
        $pemilik->assignRole('pemilik');

        $kasir = User::factory()->create([
            'name' => 'Kasir Toko',
            'email' => 'kasir@testing.com',
        ]);
        $kasir->assignRole('kasir');

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            TransactionSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
