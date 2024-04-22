<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
               
    $roles = [
        [
            'id'=>1,
            'type'=>'admin',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ],
        [
            'id'=>2,
            'type'=>'user',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ],
    ];
    
    Role::insert($roles);
    
    }
}