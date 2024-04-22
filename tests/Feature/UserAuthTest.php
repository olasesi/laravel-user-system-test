<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;


class UserAuthTest extends TestCase
{
    use RefreshDatabase;
    
    public function testUserRegistrationSuccess()
    {
        $this->artisan('db:seed'); 
        
        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',

        ];

        $response = $this->json('POST', '/api/register', $userData);

        $response->assertJsonStructure([
                'status',
                'message',
            ])
            ->assertJson(['status' => 200, 'message' => 'Registration was successful']);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
        ]);
    }

    public function testInputFieldError()
{
    $userData = [
        'name' => '', 
        'email' => '', 
        'password' => '', 
        'password_confirmation' => '',

    ];
    
    $response = $this->json('POST', '/api/register', $userData);
    
    $response->assertJson([
        'status' => 200,
        'message' => [
            'name' => ['The name field is required.'],
            'email' => ['The email field is required.'],
            'password' => ["The password must be at least 6 characters.",
            'The password field is required.'],
           
        ],
    ]);
}

public function testEmailAddressError()
{
    $userData = [
        'name' => 'John Doe',
        'email' => 'invalid_email', // Invalid email format
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ];
    
    $response = $this->json('POST', '/api/register', $userData);
    
    $response->assertJson([
            'status' => 200,
           'message' => [
                'email' => ['The email must be a valid email address.'],
            ]]);
    
}

public function testPasswordIsTheSameError(){
    $userData = [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'different_password', // Mismatching confirmation
    ];
    
    $response = $this->json('POST', '/api/register', $userData);
    
    $response->assertJson([
            'status'=> 200,
            'message' => [
                'password' => ['The password confirmation does not match.'],
            ],
        ]);
    
}

public function testEmailIsUniqueError(){
    $this->artisan('db:seed');  
    
    $userData = [
        'name' => 'John Doe',
        'email' => 'olusesianita@gmail.com', // Existing email
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ];
    
    $response = $this->json('POST', '/api/register', $userData);
    
    $response->assertJson([
            'status'=> 200,
            'message' => [
                'email' => ['The email has already been taken.'],
            ],
        ]);
    
}

public function testLoginSuccess()
{
    $this->artisan('db:seed');  //seed data
    Artisan::call('passport:install');  
    
    // User data with correct credentials
    $userData = [
        'email' => 'olusesianita@gmail.com',  // correct email address
        'password' => "123456",        // correct password
    ];

    $response = $this->json('POST', '/api/login', $userData);

    $response->assertStatus(200)
    ->assertJsonStructure([
        'status',
        'token', 
    ]);
}

public function testWrongValidationInputError()
{
    $this->artisan('db:seed');
    
    $userData = [
        'email' => 'olusesianitagmail.com',  // invalid email pattern
        'password' => "",        // empty password
    ];

    $response = $this->json('POST', '/api/login', $userData);

    $response->assertJson([
                'status' => 200,
                'message' => [
                    'password' => ['The password field is required.'],
                    'email'=>['The email must be a valid email address.']
                ],
            ]);
}

public function testLoginOutSuccess()
{
    $this->artisan('db:seed');
    Artisan::call('passport:install');
    
    // User data with valid credentials 
    $userData = [
        'email' => 'olusesianita@gmail.com', 
        'password' => '123456',        
    ];

    $loginResponse = $this->json('POST', '/api/login', $userData);
    $token = json_decode($loginResponse->getContent())->token;

    $response = $this->withHeaders([
        'Authorization' => "Bearer $token",
    ])->json('POST', '/api/logout');

    $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out'
            ]);
}

}