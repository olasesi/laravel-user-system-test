<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;


class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGetAllUsersSuccess()
    {
        $this->artisan('db:seed');
        Artisan::call('passport:install');
    
       // Admin data with valid credentials 
    $userData = [
        'email' => 'olusesia@gmail.com',  
        'password' => '123456', 
    ];

    $loginResponse = $this->json('POST', '/api/admin-login', $userData);
    $token = json_decode($loginResponse->getContent())->token;

    $response = $this->withHeaders([
        'Authorization' => "Bearer $token",
    ])->json('GET', '/api/get-users');


    // Assert JSON structure with users array
    $response->assertJsonStructure([
        'status',  
        'users' => [
            '*' => [
                'name',
                'email',
                'type'
            ]
            
        ]  
    ]);
    }

    public function testIfNoUser()
    {
        Artisan::call('db:seed', ['--class' => 'AdminRoleSeeder']);  //seeding only the admins      
        Artisan::call('passport:install');
    
       // Admin data with valid credentials 
    $userData = [
        'email' => 'olusesia@gmail.com',  
        'password' => '123456', 
    ];

    $loginResponse = $this->json('POST', '/api/admin-login', $userData);
    $token = json_decode($loginResponse->getContent())->token;

    $response = $this->withHeaders([
        'Authorization' => "Bearer $token",
    ])->json('GET', '/api/get-users');


    $response->assertStatus(200)->assertJsonStructure([
        'status',  
        'users' => []  
    ]);
    }

    public function testGetUserByIdSuccess()
    {
        $this->artisan('db:seed');  
        Artisan::call('passport:install');
    
       // Admin data with valid credentials 
    $userData = [
        'email' => 'olusesia@gmail.com',  
        'password' => '123456', 
    ];

    $loginResponse = $this->json('POST', '/api/admin-login', $userData);
    $token = json_decode($loginResponse->getContent())->token;

    $userId = User::where('role_id', 2)->inRandomOrder()->first()->id;  //is a random user with user role
    
    $response = $this->withHeaders([
        'Authorization' => "Bearer $token",
    ])->json('GET', "/api/get-user/$userId");


    // Assert JSON structure with users array
    $response->assertStatus(200)->assertJsonStructure([
        'status',  
        'user' => [
            'name',
            'email',
            'type'
        ]  
    ]);
    }
    
    public function testUserByIdDoesNotExist()
    {
        $this->artisan('db:seed');  
        Artisan::call('passport:install');
    
       // Admin data with valid credentials 
    $userData = [
        'email' => 'olusesia@gmail.com',  
        'password' => '123456', 
    ];

    $loginResponse = $this->json('POST', '/api/admin-login', $userData);
    $token = json_decode($loginResponse->getContent())->token;


    $maxUserId = User::max('id');
    $newUserId = $maxUserId + 1; //user id that does not exist in a given time   

    
    $response = $this->withHeaders([
        'Authorization' => "Bearer $token",
    ])->json('GET', "/api/get-user/$newUserId");


    $response->assertJson(['status'=>200, 'error'=>'User not found']);
    }

    public function testChangeUserInfoByAdminSuccess()
    {
        $this->artisan('db:seed');  
        Artisan::call('passport:install');
    
       // Admin data with valid credentials 
    $userData = [
        'email' => 'olusesia@gmail.com',  
        'password' => '123456', 
    ];

    $loginResponse = $this->json('POST', '/api/admin-login', $userData);
    $token = json_decode($loginResponse->getContent())->token;

    $updateData = [
        'name' => 'Jane Doe',
        'email' => 'jane.doe@example.com', 
    ];
      
    $userId = User::where('role_id', 2)->inRandomOrder()->first()->id;  //is a random user with user role

    $response = $this->withHeaders([
        'Authorization' => "Bearer $token",
    ])->json('PATCH', "/api/update-user/$userId", $updateData);


    $response->assertJson(['status'=>200, 'message' => 'User updated successfully']);
    }
    
    public function testUserInfoChangeValidation()
    {
        $this->artisan('db:seed');  
        Artisan::call('passport:install');
    
       // Admin data with valid credentials 
    $userData = [
        'email' => 'olusesia@gmail.com',  
        'password' => '123456', 
    ];

    $loginResponse = $this->json('POST', '/api/admin-login', $userData);
    $token = json_decode($loginResponse->getContent())->token;

    $updateData = [
        'name' => '', 
        'email' => 'jane.doeexample.com', //invalid email pattern
    ];
      
    $userId = User::where('role_id', 2)->inRandomOrder()->first()->id;  //is a random user with user role

    $response = $this->withHeaders([
        'Authorization' => "Bearer $token",
    ])->json('PATCH', "/api/update-user/$userId", $updateData);


    $response->assertJson(['status'=>200, 'message' => [
        'name'=>['The name field is required.'], 'email'=>['The email must be a valid email address.']
    ]]);
    }
    

    public function testDeleteUserSuccess()
{
    $this->artisan('db:seed');
     Artisan::call('passport:install');
    
    // Admin user with valid credentials 
    $userData = [
        'email' => 'olusesia@gmail.com',  
        'password' => '123456',        
    ];

    // 1. Login to obtain a token 
    $loginResponse = $this->json('POST', '/api/admin-login', $userData);
    $token = json_decode($loginResponse->getContent())->token;
      
    $userId = User::where('role_id', 2)->inRandomOrder()->first()->id;  //is a random user with user role
    
    $response = $this->withHeaders([
        'Authorization' => "Bearer $token",
    ])->json('DELETE', "/api/delete-user/$userId");

    // Assert successful deletion
    $response->assertJson([
                'status' => 200,
                'message' => 'User deleted successfully'
            ]);
}
    

}