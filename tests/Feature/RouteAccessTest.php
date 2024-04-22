<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;


class RouteAccessTest extends TestCase
{
    use RefreshDatabase;
    
    public function testLoggedInAdminTryToAccessLogin()
{

    $this->artisan('db:seed');
    Artisan::call('passport:install');
    // Data with valid admin credentials
    $userData = [
        'email' => 'olusesia@gmail.com',  // Valid admin email
        'password' => '123456',        // Valid admin password
    ];

    $loginResponse = $this->json('POST', '/api/admin-login', $userData);
    $token = json_decode($loginResponse->getContent())->token;

    $response = $this->withHeaders([
        'Authorization' => "Bearer $token",
    ])->json('POST', '/api/login');


    $response->assertJson(
        ['status'=>200,'error' => 'Authenticated users are not allowed to access this route.']);
}

public function testLoggedInUserTryToAccessLogin()
{

    $this->artisan('db:seed');
    Artisan::call('passport:install');
    // Data with valid user credentials
    $userData = [
        'email' => 'olusesianita@gmail.com',  // Valid user email
        'password' => '123456',        // Valid user password
    ];

    $loginResponse = $this->json('POST', '/api/login', $userData);
    $token = json_decode($loginResponse->getContent())->token;

    $response = $this->withHeaders([
        'Authorization' => "Bearer $token",
    ])->json('POST', '/api/register');


    $response->assertJson(
        ['status'=>200,'error' => 'Authenticated users are not allowed to access this route.']);
}

public function testUserTypesCannotAccessEachOtherWhenLoggedIn()
{

    $this->artisan('db:seed');
    Artisan::call('passport:install');
    // Data with valid user credentials
    $userData = [
        'email' => 'olusesianita@gmail.com',  // Valid user email
        'password' => '123456',        // Valid user password
    ];

    $loginResponse = $this->json('POST', '/api/login', $userData);
    $token = json_decode($loginResponse->getContent())->token;

    $response = $this->withHeaders([
        'Authorization' => "Bearer $token",
    ])->json('GET', '/api/get-users');


    $response->assertJson(
        ['status'=>200,'error' => 'Insufficient scope']);
}


}