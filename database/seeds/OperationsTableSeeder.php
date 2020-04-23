<?php

use Illuminate\Database\Seeder;

class OperationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $operations = [
            ['name' => 'english_to_persian'],
            ['name' => 'persian_to_english'],
        ];

        DB::table('operations')->insert($operations);
    }
}
