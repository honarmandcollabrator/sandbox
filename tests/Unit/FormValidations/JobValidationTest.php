<?php

namespace Tests\Unit\Job;

use App\Http\Requests\Job\CompanyRequest;
use App\Http\Requests\Job\JobRequest;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class JobValidationTest extends TestCase
{
    protected $rules;

    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->rules = (new JobRequest())->rules();
    }
    /***********************************-----------
     *
     *              Validation           =======================
     *
     * /**********************************/

    /** @test */
    public function valid_job_title()
    {
        $this->bad('title', '');
        $this->bad('title', 123);
        $this->bad('title', str_repeat('a', 4));
        $this->bad('title', str_repeat('a', 141));
        /*===========*/
        $this->good('title', str_repeat('a', 50));
    }


    /** @test */
    public function valid_job_phone()
    {
        $this->bad('phone', 9127010310);
        $this->bad('phone', 45645);
        $this->bad('phone', '۱۳۹۸۱۱۱۴');
        $this->bad('phone', '0912 699 45 34');
        $this->bad('phone', '026 36801152');
        $this->bad('phone', str_repeat('1', 3));
        $this->bad('phone', str_repeat('1', 21));
        $this->bad('phone', 'پوریا');
        $this->bad('phone', 'فعلا نداریم');
        /*===========*/
        $this->good('phone', '');
        $this->good('phone', '09127010310');
        $this->good('phone', '9192899787');
        $this->good('phone', '02133162654');
        $this->good('phone', '4426');
    }


    /** @test */
    public function valid_job_telegram()
    {
        $this->bad('telegram', 123456);
        $this->bad('telegram', '123456');
        $this->bad('telegram', 'user@name');
        $this->bad('telegram', 'user#name');
        $this->bad('telegram', 'user*name');
        $this->bad('telegram', '1username');
        $this->bad('telegram', 'poo ria');
        $this->bad('telegram', '_username');
        $this->bad('telegram', '-username');
        $this->bad('telegram', 'username-');
        $this->bad('telegram', 'username_');
        $this->bad('telegram', 'user-name');
        $this->bad('telegram', 'user--name');
        $this->bad('telegram', 'user__name');
        /*===========*/
        $this->good('telegram', '');
        $this->good('telegram', 'username');
        $this->good('telegram', 'user1name');
        $this->good('telegram', 'username1');
        $this->good('telegram', 'user_name');
    }

    /** @test */
    public function valid_job_email()
    {
        $this->bad('email', 123456);
        $this->bad('email', str_repeat('a', 50) . '@gmail.com');
        $this->bad('email', 'username');
        $this->bad('email', '@gmail.com');
        $this->bad('email', 'pooria@gmail.');
        /*===========*/
        $this->good('email', '');
        $this->good('email', 'pooria@gmail.com');
    }

    /** @test */
    public function valid_job_description()
    {
        $this->bad('description', '');
        $this->bad('description', 123456);
        $this->bad('description', str_repeat('a', 9));
        $this->bad('description', str_repeat('a', 2001));
        /*===========*/
    }

    /** @test */
    public function valid_job_required_skills()
    {
        $this->bad('required_skills', '');
        $this->bad('required_skills', 123456);
        $this->bad('required_skills', str_repeat('a', 9));
        $this->bad('required_skills', str_repeat('a', 2001));
        /*===========*/
    }

    /** @test */
    public function valid_job_can_send_resume()
    {
        $this->bad('can_send_resume', '');
        $this->bad('can_send_resume', 123);
        $this->bad('can_send_resume', 'yes');
        /*===========*/
        $this->good('can_send_resume', 1);
        $this->good('can_send_resume', 0);
        $this->good('can_send_resume', true);
        $this->good('can_send_resume', false);
    }

    /** @test */
    public function valid_job_is_premium()
    {
        $this->bad('is_premium', '');
        $this->bad('is_premium', 123);
        $this->bad('is_premium', 'yes');
        /*===========*/
        $this->good('is_premium', 1);
        $this->good('is_premium', 0);
        $this->good('is_premium', true);
        $this->good('is_premium', false);
    }

    /** @test */
    public function valid_job_relationships()
    {
        $this->bad('job_time_status_id', 1);
        $this->bad('job_category_id', 1);
        $this->bad('province_id', 1);
        $this->bad('city_id', 1);
        $this->bad('job_payment_id', 1);
        $this->bad('gender_id', 1);
        $this->bad('duty_status_id', 1);
        $this->bad('job_degree_id', 1);
        $this->bad('work_experience_years_id', 1);

        $this->bad('job_time_status_id', '');
        $this->bad('job_category_id', '');
        $this->bad('province_id', '');
        $this->bad('city_id', '');
        $this->bad('job_payment_id', '');
        $this->bad('gender_id', '');
        $this->bad('duty_status_id', '');
        $this->bad('job_degree_id', '');
        $this->bad('work_experience_years_id', '');
        /*===========*/
    }

}
