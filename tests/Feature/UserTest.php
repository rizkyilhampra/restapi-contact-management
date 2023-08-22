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

    public function testUpdatePasswordSuccess()
    {
        $this->seed([UserSeeder::class]);
        $oldUserPassword = User::where('username', 'ilham')->first();

        $this->patch('/api/users/current', [
            'password' => 'memek123534534'
        ], [
            'Authorization' => 'ilham-token'
        ])->assertStatus(200)->assertJson([
            'data' => [
                'username' => 'ilham',
                'name' => 'Ilham',
            ]
        ]);

        $newUserPassword = User::where('username', 'ilham')->first();
        $this->assertNotEquals($oldUserPassword->password, $newUserPassword->password);
    }

    public function testUpdateNameFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->patch('/api/users/current', [
            'name' => 'volutpat est velit egestas dui id ornare arcu odio ut sem nulla pharetra diam sit amet nisl suscipit adipiscing bibendum est ultricies integer quis auctor elit sed vulputate mi sit amet mauris commodo quis imperdiet massa tincidunt nunc pulvinar sapien et ligula ullamcorper malesuada proin libero nunc consequat interdum varius sit amet mattis vulputate enim nulla aliquet porttitor lacus luctus accumsan tortor posuere ac ut consequat semper viverra nam libero justo laoreet sit amet cursus sit amet dictum sit amet justo donec enim diam vulputate ut pharetra sit amet aliquam id diam maecenas ultricies mi eget mauris pharetra et ultrices neque ornare aenean euismod elementum nisi quis eleifend quam adipiscing vitae proin sagittis nisl rhoncus mattis rhoncus urna neque viverra justo nec ultrices dui sapien eget mi proin sed libero enim sed faucibus turpis in eu mi bibendum neque egestas congue quisque egestas diam in arcu cursus euismod quis viverra nibh cras pulvinar mattis nunc sed blandit libero volutpat sed cras ornare arcu dui vivamus arcu felis bibendum ut tristique et egestas quis ipsum suspendisse ultrices gravida dictum fusce ut placerat orci nulla pellentesque dignissim enim sit amet venenatis urna cursus eget nunc scelerisque viverra mauris in aliquam sem fringilla ut morbi tincidunt augue interdum velit euismod in pellentesque massa placerat duis ultricies lacus sed turpis tincidunt id aliquet risus feugiat in ante metus dictum at tempor commodo ullamcorper a lacus vestibulum sed arcu non odio euismod lacinia at quis risus sed vulputate odio ut enim blandit volutpat maecenas volutpat blandit aliquam etiam erat velit',
        ], [
            'Authorization' => 'ilham-token'
        ])->assertStatus(422)->assertJson([
            'errors' => [
                'name' => [
                    'The name field must not be greater than 100 characters.'
                ]
            ]
        ]);
    }

    public function testLogoutSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => 'ilham-token'
        ])->assertStatus(200)->assertJson([
            'data' => [
                'message' => 'Logout success'
            ]
        ]);
    }

    public function testLogoutFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->delete(uri: '/api/users/logout', headers: [
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
