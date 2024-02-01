<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = DB::collection('roles')->first();
        $roleId = (string) $role['_id'];
        DB::table('users')->insert([
            'role_id' => $roleId,
            'name' => 'Pawcare.Admin',
            'email' => 'admin@mail.com',
            'password' => bcrypt('rootadmin'),
            'remember_token' => Str::random(10),
            'created_at' => '2022-05-10',
            'updated_at' => '2022-05-10',
            'address' => 'Pawcare Admin',
            'phone' => '+6285974211040'
        ]);
    }
}
