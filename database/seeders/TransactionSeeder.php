<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->warn('Tidak ada produk, lewati TransactionSeeder.');

            return;
        }

        // Create 20 sample transactions over the past 30 days
        for ($i = 0; $i < 20; $i++) {
            $user = $users->random();
            $itemCount = rand(1, 5);
            $selectedProducts = $products->random($itemCount);
            $totalAmount = 0;
            $items = [];

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 5);
                $unitPrice = $product->price;
                $subtotal = $quantity * $unitPrice;
                $totalAmount += $subtotal;

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ];
            }

            $paidAmount = $totalAmount + rand(0, 5) * 1000;

            // Spread transactions across the past 30 days
            $createdAt = now()->subDays(rand(0, 30))->setTime(rand(8, 20), rand(0, 59));

            $transaction = Transaction::create([
                'customer' => fake()->name,
                'invoice_number' => 'INV-'.$createdAt->format('YmdHis').'-'.strtoupper(substr(uniqid(), -4)),
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'change_amount' => $paidAmount - $totalAmount,
                'payment_method' => 'cash',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            foreach ($items as $item) {
                TransactionItem::create(array_merge($item, ['transaction_id' => $transaction->id]));
            }
        }
    }
}
