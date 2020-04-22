<?php

namespace Tests\Unit;

use App\Http\Requests\User\UserExperienceRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Validator;

class UserExperienceValidationTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    protected $rules;

    public function setUp(): void
    {
        parent::setUp();

        $this->rules = (new UserExperienceRequest())->rules();
    }


    /** @test */
    public function valid_work_place_name()
    {
        $this->good('work_place_name', 'شرکت سازه گستر');
        $this->good('work_place_name', 'apple');
        /*===========*/
        $this->bad('work_place_name', '');
        $this->bad('work_place_name', 1234567890);
        $this->bad('work_place_name', str_repeat('a', 41));
        $this->bad('work_place_name', str_repeat('a', 2));
    }

    /** @test */
    public function valid_job_role()
    {
        $this->good('job_role', 'مدیر');
        $this->good('job_role', 'طراح برنامه نویس و امنیت');
        /*===========*/
        $this->bad('job_role', '');
        $this->bad('job_role', 1234567890);
        $this->bad('job_role', str_repeat('a', 41));
        $this->bad('job_role', str_repeat('a', 2));
    }

    /** @test */
    public function valid_started_at()
    {
        $this->good('started_at', '2020/02/03');
        $this->good('started_at', '05-06-2015');
        /*===========*/
        $this->bad('started_at', '2025/02/03');
        $this->bad('started_at', 'ده سال پیش');
        $this->bad('started_at', '10 years ago');
    }


    /** @test */
    public function valid_finished_at()
    {
        $this->good('finished_at', '2020/02/03');
        $this->good('finished_at', '05-06-2015');
        /*===========*/
        $this->bad('finished_at', '2025/02/03');
        $this->bad('finished_at', 'ده سال پیش');
        $this->bad('finished_at', '10 years ago');
    }



}
