<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::firstOrCreate(['email' => 'super@admin.com'], [
            'name' => 'Super Admin',
            'password' => bcrypt('password'),
            'user_type' => 'super_admin',
        ]);

        User::firstOrCreate(['email' => 'sachin@gmail.com'], [
            'name' => 'Sachin',
            'password' => bcrypt('password'),
            'user_type' => 'user',
        ]);

        User::firstOrCreate(['email' => 'sumit@gmail.com'], [
            'name' => 'Sumit',
            'password' => bcrypt('password'),
            'user_type' => 'user',
        ]);

        User::firstOrCreate(['email' => 'subscriber@bwuc.in'], [
            'name' => 'Harry Potter',
            'password' => bcrypt('password'),
            'user_type' => 'user',
        ]);

        User::firstOrCreate(['email' => 'free@bwuc.in'], [
            'name' => 'Bill Gates',
            'password' => bcrypt('password'),
            'user_type' => 'user',
        ]);

        \App\Models\User::factory(10)->create();
    }
}
