<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'ilham')->first();
        Contact::create([
            'first_name' => 'rizky',
            'last_name' => 'ilham',
            'email' => 'example@email.com',
            'phone' => '081234567890',
            'user_id' => $user->id
        ]);
    }
}
