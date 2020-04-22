<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $users = [
            [
                'name' => 'پوریا هنرمند',
                'email' => 'honarmandpooria@gmail.com',
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'password' => bcrypt('password'), // secret
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('users')->insert($users);
    }
}
