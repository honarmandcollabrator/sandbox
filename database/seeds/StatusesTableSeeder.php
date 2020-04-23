<?php

use Illuminate\Database\Seeder;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            ['name' => 'calculating'],
            ['name' => 'waiting_for_payment'],
            ['name' => 'translating'],
            ['name' => 'delivered'],
            ['name' => 'finished'],
        ];

        DB::table('statuses')->insert($statuses);
    }
}
