<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $this->post('/api/users', [
            'username' => 'rizky',
            'password' => '12345678',
            'name' => 'Rizky',
        ])->assertStatus(201)->assertJson([
            'data' => [
                'username' => 'rizky',
                'name' => 'Rizky',
            ]
        ]);
    }

    public function testRegisterFail()
    {
        $this->post('/api/users', [
            'username' => '',
            'password' => '',
            'name' => '',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                'username' => [
                    'The username field is required.'
                ],
                'password' => [
                    'The password field is required.'
                ],
                'name' => [
                    'The name field is required.'
                ],
            ]
        ]);
    }

    public function testRegisterUsernameAlreadyExists()
    {
        $this->testRegisterSuccess();
        $this->post('/api/users', [
            'username' => 'rizky',
            'password' => '12345678',
            'name' => 'Rizky',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                'username' => [
                    'The username has already been taken.'
                ]
            ]
        ]);
    }
}
