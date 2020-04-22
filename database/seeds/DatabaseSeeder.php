<?php

use App\Models\Contact\Contact;
use App\Models\Job\Company;
use App\Models\Job\Job;
use App\Models\Job\Resume;
use App\Models\Network\Comment;
use App\Models\Network\Group;
use App\Models\Network\Hashtag;
use App\Models\Network\Post;
use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {


        $this->applicationRequirements();

        $this->usersSeeding();

        $this->allDataSeeding();

    }


    private function applicationRequirements()
    {
        /**
         *  Requirements
         */

    }


    private function usersSeeding()
    {
        /*
      |--------------------------------------------------------------------------
      | USERS
      |--------------------------------------------------------------------------
      |
      | Creating users with all roles
      */


    }

    private function allDataSeeding()
    {



    }


}
