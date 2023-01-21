<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'role_id' => '1',
            'name' => 'Pawcare.Admin',
            'email' => 'admin@mail.com',
            'password' => bcrypt('rootadmin'),
            'remember_token' => str_random(10),
            'created_at' => '2022-05-10',
            'updated_at' => '2022-05-10',
            'address' => 'Pawcare Admin',
            'phone' => '+6285974211040'
        ]);
    }
}
