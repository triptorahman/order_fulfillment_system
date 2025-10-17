<?php
// Developer: Md Samiur Rahman | Reviewed: 2025-10-17

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'user_id' => 2,
                'name' => 'Premium Gaming Keyboard',
                'price' => 129.99,
                'stock_quantity' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'user_id' => 3,
                'name' => 'Ergonomic Office Chair',
                'price' => 249.50,
                'stock_quantity' => 85,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'user_id' => 4,
                'name' => 'Reusable Coffee Mug',
                'price' => 9.99,
                'stock_quantity' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'user_id' => 2,
                'name' => '4K Ultra HD Monitor 27"',
                'price' => 399.00,
                'stock_quantity' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'user_id' => 2,
                'name' => 'Noise Cancelling Headphones',
                'price' => 199.95,
                'stock_quantity' => 45,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'user_id' => 3,
                'name' => 'Portable Bluetooth Speaker',
                'price' => 45.00,
                'stock_quantity' => 200,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'user_id' => 4,
                'name' => 'LED Desk Lamp with Wireless Charging',
                'price' => 65.75,
                'stock_quantity' => 55,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'user_id' => 2,
                'name' => 'High-Fidelity Stereo Amplifier',
                'price' => 699.00,
                'stock_quantity' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'user_id' => 3,
                'name' => 'External SSD 1TB - Rugged Edition',
                'price' => 95.50,
                'stock_quantity' => 70,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'user_id' => 4,
                'name' => 'Pack of 10 Microfiber Cleaning Cloths',
                'price' => 12.00,
                'stock_quantity' => 300,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('products')->insert($products);
    }
}
