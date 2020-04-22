<?php

namespace Tests\Unit\Job;

use App\Http\Requests\Job\JobRequest;
use App\Http\Requests\Job\ResumeRequest;
use App\Models\Globals\Country;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class ResumeValidationTest extends TestCase
{
    protected $rules;

    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->createRoles();

        $this->rules = (new ResumeRequest())->rules();
    }
    /***********************************-----------
     *
     *              Validation           =======================
     *
     * /**********************************/

    /** @test */
    public function valid_resume_full_name_persian()
    {
        $this->good('full_name_persian', 'پوریا هنرمند');
        /*===========*/
        $this->bad('full_name_persian', '');
        $this->bad('full_name_persian', 'pooria honarmand');
        $this->bad('full_name_persian', '1234');
        $this->bad('full_name_persian', 'پوریا 1234');
        $this->bad('full_name_persian', 'پوریا honarmand');
        $this->bad('full_name_persian', str_repeat('ش', 51));
    }

    /** @test */
    public function valid_resume_full_name_english()
    {
        $this->good('full_name_english', 'pooria honarmand');
        /*===========*/
        $this->bad('full_name_english', '');
        $this->bad('full_name_english', 'پوریا هنرمند');
        $this->bad('full_name_english', 'پوریا honarmand');
        $this->bad('full_name_english', 'pooria 123');
        $this->bad('full_name_english', str_repeat('a', 51));
    }

    /** @test */
    public function valid_resume_father_name()
    {
        $this->good('father_name', 'محمد');
        /*===========*/
        $this->bad('father_name', '');
        $this->bad('father_name', 'mohammad');
        $this->bad('father_name', 123);
        $this->bad('father_name', '1234567');
        $this->bad('father_name', 'mohammad123');
        $this->bad('father_name', str_repeat('a', 36));
    }

    /** @test */
    public function valid_resume_national_code()
    {
        $this->good('national_code', '0014607441');
        $this->good('national_code', '856');
        /*===========*/
        $this->bad('national_code', '');
        $this->bad('identity_number', '123 456');
        $this->bad('national_code', 0014607441);
        $this->bad('national_code', 'something');
        $this->bad('national_code', 'نمی دانم');
        $this->bad('national_code', 'شماره');
        $this->bad('national_code', 'i dont know');
        $this->bad('national_code', str_repeat('1', 2));
        $this->bad('national_code', str_repeat('1', 13));
    }

    /** @test */
    public function valid_resume_identity_number()
    {
        $this->good('identity_number', '0014607441');
        $this->good('identity_number', '856');
        /*===========*/
        $this->bad('identity_number', '');
        $this->bad('identity_number', '123 456');
        $this->bad('identity_number', 0014607441);
        $this->bad('identity_number', 'something');
        $this->bad('identity_number', 'نمی دانم');
        $this->bad('identity_number', 'شماره');
        $this->bad('identity_number', 'i dont know');
        $this->bad('identity_number', str_repeat('1', 2));
        $this->bad('identity_number', str_repeat('1', 13));
    }

    /** @test */
    public function valid_resume_dependants_count()
    {
        $this->good('dependants_count', 0);
        $this->good('dependants_count', 9);
        /*===========*/
        $this->bad('dependants_count', 10);
        $this->bad('dependants_count', 100);
        $this->bad('dependants_count', 'ده نفر');
        $this->bad('dependants_count', '10');
        $this->bad('dependants_count', '1000');
    }

    /** @test */
    public function valid_resume_is_married()
    {
        $this->good('is_married', 0);
        $this->good('is_married', 1);
        $this->good('is_married', true);
        $this->good('is_married', false);
        /*===========*/
        $this->bad('is_married', 10);
        $this->bad('is_married', 100);
        $this->bad('is_married', 'yes');
    }


    /** @test */
    public function valid_resume_relationships()
    {
        factory(Country::class)->create();
        $this->good('country_id', 1);

        /*===========*/
        $this->bad('job_category_id', 1);
        $this->bad('city_id', 1);
        $this->bad('gender_id', 1);
        $this->bad('duty_status_id', 1);
        $this->bad('job_degree_id', 1);
        $this->bad('work_experience_years_id', 1);
        $this->bad('religion_id', 1);
    }

    /** @test */
    public function valid_birthday_date()
    {
        $this->good('birthday', '1992/05/04');

        /*===========*/
        $this->bad('birthday','2015/05/04' ); // too young
        $this->bad('birthday', '1398/8/8');
        $this->bad('birthday', '2312313');
        $this->bad('birthday', 156156);
    }

}
