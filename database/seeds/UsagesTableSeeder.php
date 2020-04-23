<?php

use Illuminate\Database\Seeder;

class UsagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usages = [
            ['name' => 'student_project'],
            ['name' => 'lecture_or_journal'],
            ['name' => 'article_or_thesis'],
            ['name' => 'publishing_or_book'],
            ['name' => 'website'],
            ['name' => 'commercial_or_company'],
            ['name' => 'work_sample'],
        ];

        DB::table('usages')->insert($usages);
    }
}
