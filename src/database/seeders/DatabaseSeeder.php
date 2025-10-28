<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User 1',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin@test.com')
        ]);

        User::factory()->create([
            'name' => 'Test User 2',
            'email' => 'user@test.com',
            'password' => Hash::make('user@test.com')
        ]);
    }
}
