<?php
// Developer: Md Samiur Rahman | Reviewed: 2025-10-17

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = array(
            // Admin User
            array(
                'id' => 1,
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'balance' => 0.00,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ),

            // Seller User
            array(
                'id' => 2,
                'name' => 'Seller 1',
                'email' => 'seller1@example.com',
                'password' => Hash::make('password'),
                'role' => 'seller',
                'balance' => 5000.00,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'id' => 3,
                'name' => 'Seller 2',
                'email' => 'seller2@example.com',
                'password' => Hash::make('password'),
                'role' => 'seller',
                'balance' => 5000.00,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'id' => 4,
                'name' => 'Seller 3',
                'email' => 'seller3@example.com',
                'password' => Hash::make('password'),
                'role' => 'seller',
                'balance' => 5000.00,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ),

            // Buyer User
            array(
                'id' => 5,
                'name' => 'Buyer 1',
                'email' => 'buyer1@example.com',
                'password' => Hash::make('password'),
                'role' => 'buyer',
                'balance' => 150.00,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'id' => 6,
                'name' => 'Buyer 2',
                'email' => 'buyer2@example.com',
                'password' => Hash::make('password'),
                'role' => 'buyer',
                'balance' => 150.00,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'id' => 7,
                'name' => 'Buyer 3',
                'email' => 'buyer3@example.com',
                'password' => Hash::make('password'),
                'role' => 'buyer',
                'balance' => 150.00,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ),
        );

        DB::table('users')->insert($users);
    }
}
