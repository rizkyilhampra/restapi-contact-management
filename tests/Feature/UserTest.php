<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
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

    public function testLoginSuccess()
    {
        $this->seed([
            UserSeeder::class,
        ]);

        $this->post('/api/users/login', [
            'username' => 'ilham',
            'password' => '12345678',
        ])->assertStatus(200)->assertJson([
            'data' => [
                'username' => 'ilham',
                'name' => 'Ilham',
            ]
        ]);

        $user = User::where('username', 'ilham')->first();
        $this->assertNotNull($user->token);
    }

    public function testLoginFailedUsernameNotExist()
    {
        $this->post('/api/users/login', [
            'username' => 'ilham',
            'password' => '12345678',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                'username' => [
                    'The selected username is invalid.'
                ]
            ]
        ]);
    }

    public function testLoginFailedPasswordDontMatch()
    {
        $this->seed([
            UserSeeder::class,
        ]);

        $this->post('/api/users/login', [
            'username' => 'ilham',
            'password' => 'ilham23423',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                'message' => [
                    'Password is incorrect'
                ]
            ]
        ]);
    }

    public function testGetCurrentUsersSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
            'Authorization' => 'ilham-token'
        ])->assertStatus(200)->assertJson([
            'data' => [
                'username' => 'ilham',
                'name' => 'Ilham',
            ]
        ]);
    }

    public function testGetUnauthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current')->assertStatus(401)->assertJson([
            'errors' => [
                'message' => [
                    'Unauthorized'
                ]
            ]
        ]);
    }

    public function testGetInvalidToken()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
            'Authorization' => 'invalid-token'
        ])->assertStatus(401)->assertJson([
            'errors' => [
                'message' => [
                    'Unauthorized'
                ]
            ]
        ]);
    }
}
