<?php

use Illuminate\Database\Seeder;

class GendersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seed = [
            [
                'name' => 'مهم نیست'
            ],
            [
                'name' => 'مرد'
            ],
            [
                'name' => 'زن'
            ]
        ];

        DB::table('genders')->insert($seed);
    }
}
