<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;


class AdminAuthTest extends TestCase
{
    use RefreshDatabase;
    
    public function testAdminLoginSuccess()
{

    $this->artisan('db:seed');
    Artisan::call('passport:install');
    // Data with valid admin credentials
    $userData = [
        'email' => 'olusesia@gmail.com',  // Valid admin email
        'password' => '123456',        // Valid admin password
    ];

    $response = $this->json('POST', '/api/admin-login', $userData);

    $response->assertStatus(200)
            ->assertJsonStructure(
                ['status',
                'token'
            ]);
}


    public function testAdminInvalidInputError()
{
    // User data with invalid input credentials
    $userData = [
        'email' => 'olusesiagmailcom',  // invalid input
        'password' => '',      // empty input
    ];

    $response = $this->json('POST', '/api/admin-login', $userData);

    $response->assertJson([
        'status' => 200,
        'message' => [
            'password' => ['The password field is required.'],
            'email'=>['The email must be a valid email address.']
        ],
    ]);
}

public function testAdminLoginOutSuccess()
{
    $this->artisan('db:seed');
    Artisan::call('passport:install');
    
    // User data with valid credentials 
    $userData = [
        'email' => 'olusesia@gmail.com',  
        'password' => '123456', 
    ];

    // 1. Login to obtain a token 
    $loginResponse = $this->json('POST', '/api/admin-login', $userData);
    $token = json_decode($loginResponse->getContent())->token;

    // 2. Send logout request with the obtained token
    $response = $this->withHeaders([
        'Authorization' => "Bearer $token",
    ])->json('POST', '/api/admin-logout');

    // Assertions
    $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out'
            ]);
}

}