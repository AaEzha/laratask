<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        Role::create(
            ['name' => 'Administrator'],
        );
        Role::create(
            ['name' => 'Staf'],
        );
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'role_id' => 1,
            'password' => bcrypt('password')
        ]);
        User::create([
            'name' => 'Staf',
            'email' => 'staf@gmail.com',
            'role_id' => 2,
            'password' => bcrypt('password')
        ]);
    }
}
