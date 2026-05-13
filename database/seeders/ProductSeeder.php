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
            'Kopi & Espresso' => [
                ['name' => 'Espresso', 'price' => 18000, 'sku' => 'SKU-KPI-001', 'is_unlimited_stock' => true],
                ['name' => 'Americano', 'price' => 22000, 'sku' => 'SKU-KPI-002', 'is_unlimited_stock' => true],
                ['name' => 'Cappuccino', 'price' => 28000, 'sku' => 'SKU-KPI-003', 'is_unlimited_stock' => true],
                ['name' => 'Caffe Latte', 'price' => 30000, 'sku' => 'SKU-KPI-004', 'is_unlimited_stock' => true],
                ['name' => 'Mocha Latte', 'price' => 32000, 'sku' => 'SKU-KPI-005', 'is_unlimited_stock' => true],
                ['name' => 'Caramel Macchiato', 'price' => 35000, 'sku' => 'SKU-KPI-006', 'is_unlimited_stock' => true],
                ['name' => 'Cold Brew', 'price' => 25000, 'sku' => 'SKU-KPI-007', 'is_unlimited_stock' => true],
                ['name' => 'Affogato', 'price' => 28000, 'sku' => 'SKU-KPI-008', 'is_unlimited_stock' => true],
            ],
            'Non-Coffee' => [
                ['name' => 'Matcha Latte', 'price' => 30000, 'sku' => 'SKU-NCF-001', 'is_unlimited_stock' => true],
                ['name' => 'Taro Latte', 'price' => 30000, 'sku' => 'SKU-NCF-002', 'is_unlimited_stock' => true],
                ['name' => 'Chocolate Hazelnut', 'price' => 32000, 'sku' => 'SKU-NCF-003', 'is_unlimited_stock' => true],
                ['name' => 'Red Velvet Latte', 'price' => 32000, 'sku' => 'SKU-NCF-004', 'is_unlimited_stock' => true],
                ['name' => 'Vanilla Latte', 'price' => 28000, 'sku' => 'SKU-NCF-005', 'is_unlimited_stock' => true],
                ['name' => 'Bandrek Susu', 'price' => 25000, 'sku' => 'SKU-NCF-006', 'is_unlimited_stock' => true],
            ],
            'Makanan Ringan' => [
                ['name' => 'Croissant', 'price' => 20000, 'sku' => 'SKU-MRG-001', 'stock' => 8],
                ['name' => 'Banana Bread', 'price' => 15000, 'sku' => 'SKU-MRG-002', 'stock' => 12],
                ['name' => 'New York Cheesecake', 'price' => 35000, 'sku' => 'SKU-MRG-003', 'stock' => 3],
                ['name' => 'Chocolate Cookies', 'price' => 12000, 'sku' => 'SKU-MRG-004', 'stock' => 25],
                ['name' => 'Tiramisu', 'price' => 30000, 'sku' => 'SKU-MRG-005', 'stock' => 0],
                ['name' => 'Roti Bakar', 'price' => 18000, 'sku' => 'SKU-MRG-006', 'stock' => 5],
                ['name' => 'Pisang Goreng', 'price' => 12000, 'sku' => 'SKU-MRG-007', 'is_unlimited_stock' => true],
                ['name' => 'Kentang Goreng', 'price' => 18000, 'sku' => 'SKU-MRG-008', 'is_unlimited_stock' => true],
            ],
            'Makanan Berat' => [
                ['name' => 'Nasi Goreng Spesial', 'price' => 30000, 'sku' => 'SKU-MBR-001', 'is_unlimited_stock' => true],
                ['name' => 'Spaghetti Aglio Olio', 'price' => 35000, 'sku' => 'SKU-MBR-002', 'is_unlimited_stock' => true],
                ['name' => 'Chicken Steak', 'price' => 45000, 'sku' => 'SKU-MBR-003', 'is_unlimited_stock' => true],
                ['name' => 'French Fries', 'price' => 20000, 'sku' => 'SKU-MBR-004', 'is_unlimited_stock' => true],
                ['name' => 'Cireng Crispy', 'price' => 15000, 'sku' => 'SKU-MBR-005', 'is_unlimited_stock' => true],
                ['name' => 'Indomie Goreng', 'price' => 12000, 'sku' => 'SKU-MBR-006', 'is_unlimited_stock' => true],
            ],
            'Minuman Segar' => [
                ['name' => 'Jus Alpukat', 'price' => 20000, 'sku' => 'SKU-SGR-001', 'is_unlimited_stock' => true],
                ['name' => 'Jus Mangga', 'price' => 18000, 'sku' => 'SKU-SGR-002', 'is_unlimited_stock' => true],
                ['name' => 'Smoothie Berry', 'price' => 25000, 'sku' => 'SKU-SGR-003', 'is_unlimited_stock' => true],
                ['name' => 'Es Kelapa Muda', 'price' => 15000, 'sku' => 'SKU-SGR-004', 'stock' => 15],
                ['name' => 'Lemon Tea', 'price' => 12000, 'sku' => 'SKU-SGR-005', 'stock' => 20],
                ['name' => 'Es Cincau', 'price' => 13000, 'sku' => 'SKU-SGR-006', 'stock' => 10],
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
                    'stock' => $product['stock'] ?? 0,
                    'is_unlimited_stock' => $product['is_unlimited_stock'] ?? false,
                    'description' => $product['name'].' - Caffe Shop',
                    'is_active' => true,
                ]);
            }
        }
    }
}
