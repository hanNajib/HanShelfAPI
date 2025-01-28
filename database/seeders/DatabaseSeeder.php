<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Seed users
        DB::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seed books
        DB::table('books')->insert([
            [
                'title' => 'Book One',
                'author' => 'Author One',
                'stock' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Book Two',
                'author' => 'Author Two',
                'stock' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more books as needed
        ]);

        // Seed loans
        DB::table('loans')->insert([
            [
                'book_id' => 1,
                'member_id' => 1,
                'loan_date' => now(),
                'due_date' => now()->addDays(14),
                'status' => 'borrowed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more loans as needed
        ]);
    }
}
