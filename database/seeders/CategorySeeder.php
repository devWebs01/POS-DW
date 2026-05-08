<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan', 'slug' => 'makanan', 'description' => 'Makanan ringan dan berat'],
            ['name' => 'Minuman', 'slug' => 'minuman', 'description' => 'Minuman kemasan dan segar'],
            ['name' => 'Snack', 'slug' => 'snack', 'description' => 'Camilan dan kudapan'],
            ['name' => 'Alat Tulis', 'slug' => 'alat-tulis', 'description' => 'Peralatan kantor dan sekolah'],
            ['name' => 'Perawatan Diri', 'slug' => 'perawatan-diri', 'description' => 'Produk kebersihan dan kecantikan'],
            ['name' => 'Rumah Tangga', 'slug' => 'rumah-tangga', 'description' => 'Kebutuhan rumah tangga'],
            ['name' => 'Obat-obatan', 'slug' => 'obat-obatan', 'description' => 'Obat bebas dan vitamin'],
            ['name' => 'Elektronik', 'slug' => 'elektronik', 'description' => 'Aksesoris dan perangkat elektronik'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
