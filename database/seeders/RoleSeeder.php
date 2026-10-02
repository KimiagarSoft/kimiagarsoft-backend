<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::updateOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Administrator']
        );

        Role::updateOrCreate(
            ['slug' => 'author'],
            ['name' => 'Author']
        );
    }
}

