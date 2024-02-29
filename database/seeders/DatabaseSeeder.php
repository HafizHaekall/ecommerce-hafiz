<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'is_admin' => true
        ]);

        DB::table('users')->insert([
            'id' => 2,
            'name' => 'User',
            'email' => 'user@gmail.com',
            'password' => Hash::make('user1234'),
            'is_admin' => false
        ]);

        DB::table('discounts')->insert([
            [
                'code' => 'DISCOUNT10',
                'percentage' => 10,
                'start_date' => now(),
                'end_date' => now()->addMonth(), // Diskon berlaku selama satu bulan dari saat ini
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'DISCOUNT20',
                'percentage' => 20,
                'start_date' => now(),
                'end_date' => now()->addMonth(), // Diskon berlaku selama satu bulan dari saat ini
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        
    }
}
