<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;


class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    
    public function testGetUserSuccess()
{
    $this->artisan('db:seed');
    Artisan::call('passport:install');
    // User data with valid credentials
    $userData = [
        'email' => 'olusesianita@gmail.com',  
        'password' => '123456',        
    ];

    // 1. Login to obtain a token 
    $loginResponse = $this->json('POST', '/api/login', $userData);
    $token = json_decode($loginResponse->getContent())->token;

    // 2. Send GET request to retrieve user data with the obtained token
    $response = $this->withHeaders([
        'Authorization' => "Bearer $token",
    ])->json('GET', '/api/get-user');

    $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'name',
                'email'
            ]);
}

  public function testUpdateUserDataSuccess()
  {
      $this->artisan('db:seed');
      Artisan::call('passport:install');
     // User data with valid data
      $userData = [
          'email' => 'olusesianita@gmail.com', 
          'password'=> '123456'
      ];

      $loginResponse = $this->json('POST', '/api/login', $userData);
      $token = json_decode($loginResponse->getContent())->token;


      $updateData = [
        'name' => 'Jane Doe',
        'email' => 'jane.doe@example.com', 
    ];
      
      $response = $this->withHeaders([
          'Authorization' => "Bearer $token",
      ])->json('PATCH', '/api/update-user', $updateData);

      $response->assertJson(['status'=> 200,
                  'message' => 'User updated successfully'] 
              );
  }


public function testUpdateInvalidDataError()
{
    $this->artisan('db:seed');
     Artisan::call('passport:install');
    
    $userData = [
        'email' => 'olusesianita@gmail.com',  
        'password' => '123456',       
    ];

    // 1. Login to obtain a token 
    $loginResponse = $this->json('POST', '/api/login', $userData);
    $token = json_decode($loginResponse->getContent())->token;

    $updateData = [
        'email' => 'jane.doe@example.com', 
        'name' => ''
    ];

    $response = $this->withHeaders([
        'Authorization' => "Bearer $token",
    ])->json('PATCH', '/api/update-user', $updateData);

    $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'message' => [
                    'name'=>[]
                ]
            ]);
}


}