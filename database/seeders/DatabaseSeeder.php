<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
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
        User::create([
            'name'      => 'admin',
            'email'     => 'admin@gmail.com',
            'role'      => 'admin',
            'password'  => bcrypt('password'),
        ]);

        User::create([
            'name'      => 'kasir',
            'email'     => 'kasir@gmail.com',
            'role'      => 'kasir',
            'password'  => bcrypt('password'),
        ]);

        Category::create([
            'name' => 'Rokok'
        ]);

        Category::create([
            'name' => 'Tembakau Potong'
        ]);
    }
}
