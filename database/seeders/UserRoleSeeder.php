<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
          
        $defaultRole = Role::where('id', 2)->first();
        
$users = [
    [
        'name' => 'olusesi anita',
        'email' => 'olusesianita@gmail.com',
        'password' => bcrypt('123456'),
        'role_id' => $defaultRole->id,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
    [
        'name' => 'olusesi eniola',
        'email' => 'olusesieniola@gmail.com',
        'password' => bcrypt('123456'),
        'role_id' => $defaultRole->id,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
];

User::insert($users);
    }
}