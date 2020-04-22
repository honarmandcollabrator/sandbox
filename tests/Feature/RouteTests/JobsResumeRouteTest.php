<?php

namespace Tests\Feature\Job;

use App\Models\Globals\City;
use App\Models\Globals\Country;
use App\Models\Globals\Gender;
use App\Models\Globals\Province;
use App\Models\Globals\Religion;
use App\Models\Job\DutyStatus;
use App\Models\Job\Job;
use App\Models\Job\JobCategory;
use App\Models\Job\JobDegree;
use App\Models\Job\JobPayment;
use App\Models\Job\JobTimeStatus;
use App\Models\Job\Resume;
use App\Models\Job\WorkExperienceYears;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class JobsResumeRouteTest extends TestCase
{
    /***********************************-----------
     *
     *              set Up                      =======================
     *
     * /**********************************/

    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->createUsers();

        $this->createResumeEssentials();
    }


    /*
       |--------------------------------------------------------------------------
       | Resume Routes
       |--------------------------------------------------------------------------
       |
       | 5 routes
       |
       |
       |
       */

    /** #1
     * @test
     */
    public function jobs_resume_store()
    {
        /** 1
         * Create a Resume
         */
        $response = $this->createResume();
        /** 2
         * Assert
         */
        $response->assertStatus(Response::HTTP_CREATED);
        $this->JobAssertJsonFragment($response);
        $this->assertCount(1, Resume::all());
    }


    /** #2
     * @test
     */
    public function jobs_resume_show()
    {
        $this->withoutExceptionHandling();
        /** 1
         * Create a Resume
         */
        $this->createResume();

        /** 2
         * Get the Resume
         */
        $url = route('jobs.resume.show', [1]);
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->normalUser),
        ]);

        /** 3
         * Assert
         */

        $response->assertStatus(Response::HTTP_OK);
        $this->JobAssertJsonFragment($response);
    }


    /** #3
     * @test
     */
    public function jobs_resume_update()
    {
        /** 1
         * Create a Resume
         */
        $this->createResume();

        /** 2
         * Update the Resume
         */
        $response = $this->updateResume();

        /** 3
         * Assert
         */
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->UpdatedJobAssertJsonFragment($response);
    }


    /** #4
     * @test
     */
    public function jobs_resume_pdf()
    {
        // todo : design it
        $this->assertTrue(true);
    }

    /** @test #5*/
    public function jobs_resume_index()
    {
        $url = route('jobs.resume.index');
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->superAdmin)
        ]);
        $response->assertOk();
        $response->assertJsonStructure([
            'data','links','meta'
        ]);
    }

    /***********************************-----------
     *
     *              Refactors                      =======================
     *
     * /**********************************/


    private function createResumeEssentials(): void
    {
        factory(City::class, 2)->create();
        factory(Country::class, 2)->create();
        factory(Province::class, 2)->create();
        factory(JobTimeStatus::class, 2)->create();
        factory(JobCategory::class, 2)->create();
        factory(JobPayment::class, 2)->create();
        factory(Gender::class, 3)->create();
        factory(DutyStatus::class, 2)->create();
        factory(JobDegree::class, 2)->create();
        factory(WorkExperienceYears::class, 2)->create();
        factory(Religion::class, 2)->create();
    }


    /**
     * @return TestResponse
     */
    private function createResume()
    {
        $url = route('jobs.resume.store');
        return $this->json('post', $url, [
            'token' => $this->getToken($this->normalUser),
            'full_name_persian' => 'پوریا       هنرمند',
            'full_name_english' => 'pooria       honarmand',
            'father_name' => 'محمد    هنرمند',
            'birthday' => '1/29/1992',
            'national_code' => '123465798',
            'identity_number' => '123465798',
            'dependants_count' => '0',
            'is_married' => '0',
            'work_experience_years_id' => 1,
            'job_category_id' => 1,
            'job_degree_id' => 1,
            'country_id' => 1,
            'city_id' => 1,
            'gender_id' => 2,
            'duty_status_id' => 1,
            'religion_id' => 1,
        ]);
    }

    /**
     * @return TestResponse
     */
    private function updateResume()
    {
        $url = route('jobs.resume.update', [1]);
        return $this->json('put', $url, [
            'token' => $this->getToken($this->normalUser),
            'full_name_persian' => 'سپیده         بهشتی',
            'full_name_english' => 'sepide            beheshti',
            'father_name' => 'مهدی           اکبری',
            'birthday' => '1/29/1990',
            'national_code' => '123456798',
            'identity_number' => '123456798',
            'dependants_count' => '3',
            'is_married' => '1',
            'work_experience_years_id' => 2,
            'job_category_id' => 2,
            'job_degree_id' => 2,
            'country_id' => 2,
            'city_id' => 2,
            'gender_id' => 3,
            'duty_status_id' => 2,
            'religion_id' => 2,
        ]);
    }

    /**
     * @param TestResponse $response
     */
    private function JobAssertJsonFragment(TestResponse $response): void
    {
        $response->assertJsonFragment([
            'id' => Resume::first()->id,
            'full_name_persian' => 'پوریا هنرمند',
            'full_name_english' => 'pooria honarmand',
            'father_name' => 'محمد هنرمند',
            'birthday' => jdate(Resume::first()->birthday)->format('%d %B %Y'),
            'national_code' => '123465798',
            'identity_number' => '123465798',
            'dependants_count' => '0',
            'is_married' => '0',
            'work_experience_years' => [
                'id' => WorkExperienceYears::first()->id,
                'name' => WorkExperienceYears::first()->name,
            ],
            'job_category' => [
                'id' => JobCategory::first()->id,
                'name' => JobCategory::first()->name,
            ],
            'job_degree' => [
                'id' => JobDegree::first()->id,
                'name' => JobDegree::first()->name,
            ],
            'country' => [
                'id' => Country::first()->id,
                'name' => Country::first()->name,
            ],
            'city' => [
                'id' => City::first()->id,
                'name' => City::first()->name,
            ],
            'gender' => [
                'id' => Gender::find(2)->id,
                'name' => Gender::find(2)->name,
            ],
            'duty_status' => [
                'id' => DutyStatus::first()->id,
                'name' => DutyStatus::first()->name,
            ],
            'religion' => [
                'id' => Religion::first()->id,
                'name' => Religion::first()->name,
            ],
        ]);
    }

    /**
     * @param TestResponse $response
     */
    private function UpdatedJobAssertJsonFragment(TestResponse $response): void
    {
        $response->assertJsonFragment([
            'id' => Resume::first()->id,
            'full_name_persian' => 'سپیده بهشتی',
            'full_name_english' => 'sepide beheshti',
            'father_name' => 'مهدی اکبری',
            'birthday' => jdate(Resume::first()->birthday)->format('%d %B %Y'),
            'national_code' => '123456798',
            'identity_number' => '123456798',
            'dependants_count' => '3',
            'is_married' => '1',
            'work_experience_years' => [
                'id' => WorkExperienceYears::find(2)->id,
                'name' => WorkExperienceYears::find(2)->name,
            ],
            'job_category' => [
                'id' => JobCategory::find(2)->id,
                'name' => JobCategory::find(2)->name,
            ],
            'job_degree' => [
                'id' => JobDegree::find(2)->id,
                'name' => JobDegree::find(2)->name,
            ],
            'country' => [
                'id' => Country::find(2)->id,
                'name' => Country::find(2)->name,
            ],
            'city' => [
                'id' => City::find(2)->id,
                'name' => City::find(2)->name,
            ],
            'gender' => [
                'id' => Gender::find(3)->id,
                'name' => Gender::find(3)->name,
            ],
            'duty_status' => [
                'id' => DutyStatus::find(2)->id,
                'name' => DutyStatus::find(2)->name,
            ],
            'religion' => [
                'id' => Religion::find(2)->id,
                'name' => Religion::find(2)->name,
            ],
        ]);
    }

}
