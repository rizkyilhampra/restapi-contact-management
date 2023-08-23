<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            'first_name' => 'Rizky',
            'last_name' => 'Ilham',
            'email' => 'rizkyilhamp16@gmail.com',
            'phone' => '081234567890',
        ], [
            'Authorization' => 'ilham-token'
        ])->assertStatus(201)->assertJson([
            'data' => [
                'first_name' => 'Rizky',
                'last_name' => 'Ilham',
                'email' => 'rizkyilhamp16@gmail.com',
                'phone' => '081234567890',
            ]
        ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'phone' => '',
        ], [
            'Authorization' => 'ilham-token'
        ])->assertStatus(422)->assertJson([
            'errors' => [
                'first_name' => [
                    'The first name field is required.'
                ],
                'last_name' => [
                    'The last name field is required.'
                ],
            ]
        ]);
    }

    public function testCreateUnauthorize()
    {
        $this->post('/api/contacts', [
            'first_name' => 'Rizky',
            'last_name' => 'Ilham',
            'email' => 'rizkyilhamp16@gmail.com',
            'phone' => '081234567890',
        ])->assertStatus(401)->assertJson([
            'errors' => [
                'message' => ['Unauthorized']
            ]
        ]);
    }
}
