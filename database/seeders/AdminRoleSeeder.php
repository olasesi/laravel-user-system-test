<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;

class AdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $adminRole = Role::firstOrCreate(['type' => 'admin']);

$users = [
    [
        'name' => 'olusesi ahmed',
        'email' => 'olusesia@gmail.com',
        'password' => bcrypt('123456'),
        'role_id' => $adminRole->id, 
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
    [
        'name' => 'aaliyah olusesi',
        'email' => 'olusesiaaliyah@gmail.com',
        'password' => bcrypt('123456'),
        'role_id' => $adminRole->id,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
];

User::insert($users);
         
    }
}