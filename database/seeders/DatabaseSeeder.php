<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\SettingSeeder;
use Database\Seeders\TransactionSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::factory()->create([
            'name' => 'Admin POS',
            'email' => 'admin@testing.com',
        ]);

        // Kasir user
        User::factory()->create([
            'name' => 'Kasir Toko',
            'email' => 'kasir@testing.com',
        ]);

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            TransactionSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
