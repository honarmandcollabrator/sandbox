<?php

use Illuminate\Database\Seeder;

class TicketStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ticket_statuses = [
            ['name' => 'opened'],
            ['name' => 'support_asked'],
            ['name' => 'support_answered'],
            ['name' => 'user_message'],
            ['name' => 'closed'],
        ];

        DB::table('ticket_statuses')->insert($ticket_statuses);
    }
}
