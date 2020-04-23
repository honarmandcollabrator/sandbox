<?php

use Illuminate\Database\Seeder;

class QualitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $qualities = [
            ['name' => 'normal'],
            ['name' => 'silver'],
            ['name' => 'gold'],
            ['name' => 'super'],
        ];

        DB::table('qualities')->insert($qualities);
    }
}
