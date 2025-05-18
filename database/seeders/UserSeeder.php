<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        User::create ([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => 1	
        ]);

        User::create ([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'janebaby@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'is_active' => 1
        ]);
    
    */

    User::create ([
        'first_name' => 'Oluwafemi',
        'last_name' => 'Adebayo',
        'email' => 'adebayocharles7@gmail.com',
        'password' => bcrypt('Ayanfe767#'),
        'role' => 'admin',
        'is_active' => 1
        ]); 
    } 

    
}
