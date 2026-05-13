<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $productsByCategory = [
            'Makanan' => [
                ['name' => 'Nasi Goreng Instan', 'price' => 15000, 'sku' => 'SKU-MKN-001'],
                ['name' => 'Mie Instant Goreng', 'price' => 5000, 'sku' => 'SKU-MKN-002'],
                ['name' => 'Roti Tawar', 'price' => 12000, 'sku' => 'SKU-MKN-003'],
                ['name' => 'Biskuit Coklat', 'price' => 8000, 'sku' => 'SKU-MKN-004'],
                ['name' => 'Sarden Kaleng', 'price' => 18000, 'sku' => 'SKU-MKN-005'],
            ],
            'Minuman' => [
                ['name' => 'Air Mineral 600ml', 'price' => 3000, 'sku' => 'SKU-MNM-001'],
                ['name' => 'Teh Botol', 'price' => 7000, 'sku' => 'SKU-MNM-002'],
                ['name' => 'Kopi Sachet', 'price' => 2000, 'sku' => 'SKU-MNM-003'],
                ['name' => 'Jus Jeruk Kemasan', 'price' => 10000, 'sku' => 'SKU-MNM-004'],
                ['name' => 'Susu UHT 250ml', 'price' => 8000, 'sku' => 'SKU-MNM-005'],
            ],
            'Snack' => [
                ['name' => 'Keripik Kentang', 'price' => 12000, 'sku' => 'SKU-SNC-001'],
                ['name' => 'Wafer Coklat', 'price' => 10000, 'sku' => 'SKU-SNC-002'],
                ['name' => 'Kacang Garuda', 'price' => 8000, 'sku' => 'SKU-SNC-003'],
                ['name' => 'Permen Asem', 'price' => 5000, 'sku' => 'SKU-SNC-004'],
                ['name' => 'Coklat Batang', 'price' => 15000, 'sku' => 'SKU-SNC-005'],
            ],
            'Alat Tulis' => [
                ['name' => 'Pensil 2B', 'price' => 3000, 'sku' => 'SKU-ALT-001'],
                ['name' => 'Buku Tulis 40 Lembar', 'price' => 5000, 'sku' => 'SKU-ALT-002'],
                ['name' => 'Pulpen Biru', 'price' => 4000, 'sku' => 'SKU-ALT-003'],
                ['name' => 'Penghapus', 'price' => 2000, 'sku' => 'SKU-ALT-004'],
                ['name' => 'Spidol Hitam', 'price' => 8000, 'sku' => 'SKU-ALT-005'],
            ],
            'Perawatan Diri' => [
                ['name' => 'Sabun Mandi 100ml', 'price' => 15000, 'sku' => 'SKU-PRW-001'],
                ['name' => 'Shampo Sachet', 'price' => 2000, 'sku' => 'SKU-PRW-002'],
                ['name' => 'Pasta Gigi', 'price' => 12000, 'sku' => 'SKU-PRW-003'],
                ['name' => 'Deodorant', 'price' => 18000, 'sku' => 'SKU-PRW-004'],
                ['name' => 'Hand Body Lotion', 'price' => 22000, 'sku' => 'SKU-PRW-005'],
            ],
        ];

        foreach ($productsByCategory as $categoryName => $products) {
            $category = Category::where('name', $categoryName)->first();
            if (! $category) {
                continue;
            }

            foreach ($products as $product) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $product['name'],
                    'slug' => str()->slug($product['name']),
                    'sku' => $product['sku'],
                    'price' => $product['price'],
                    'stock' => rand(10, 50),
                    'description' => "Produk {$product['name']}",
                    'is_active' => true,
                ]);
            }
        }
    }
}
