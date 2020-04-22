<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
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
                'name' => 'super_admin'
            ],
            [
                'name' => 'admin'
            ],
            [
                'name' => 'network_manager'
            ],
            [
                'name' => 'job_manager'
            ],
            [
                'name' => 'contact_manager'
            ],
            [
                'name' => 'gold'
            ],
            [
                'name' => 'silver'
            ],
            [
                'name' => 'normal'
            ],
        ];

        DB::table('roles')->insert($roles);
    }
}
