<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'ilham',
            'password' => Hash::make('12345678'),
            'name' => 'Ilham',
            'token' => 'ilham-token'
        ]);

        User::create([
            'username' => 'ilham2',
            'password' => Hash::make('12345678'),
            'name' => 'Ilham2',
            'token' => 'ilham-token2'
        ]);
    }
}
