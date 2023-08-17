<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Member;
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

        // Member::create([
        //     'name'      => 'Agus',
        //     'address'   => 'Solo',
        // ]);

        // Member::create([
        //     'name'      => 'Joko',
        //     'address'   => 'Bandung',
        // ]);

        Category::create([
            'name' => 'Rokok'
        ]);

        Category::create([
            'name' => 'Tembakau Potong'
        ]);
    }
}
