<?php

namespace Tests\Feature\Job;

use App\Models\Globals\City;
use App\Models\Globals\Country;
use App\Models\Globals\Gender;
use App\Models\Globals\Province;
use App\Models\Globals\Religion;
use App\Models\Job\Company;
use App\Models\Job\DutyStatus;
use App\Models\Job\Job;
use App\Models\Job\JobCategory;
use App\Models\Job\JobDegree;
use App\Models\Job\JobPayment;
use App\Models\Job\JobTimeStatus;
use App\Models\Job\WorkExperienceYears;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class JobsJobRouteTest extends TestCase
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

        $this->createJobEssentials();


    }

    /*
       |--------------------------------------------------------------------------
       | Job Routes
       |--------------------------------------------------------------------------
       |
       | 10 Routes
       |
       |
       |
       */

    /** #1
     * @test
     */
    public function jobs_job_store()
    {
        /** 1
         * Create a Job
         */
        $response = $this->createJob();


        /** 2
         * Assert
         */
        $response->assertStatus(Response::HTTP_CREATED);
        $this->jobJsonFragmentAssert($response);
        $this->assertCount(1, Job::all());
    }

    /** #2
     * @test
     */
    public function jobs_job_update()
    {
        /** 1
         * Create a Job
         */
        $this->createJob();


        /** 2
         * Update the Job
         */
        $response = $this->updateJob();


        /** 3
         * Assert
         */
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->updatedJobJsonFragmentAssert($response);
    }

    /** #3
     * @test
     */
    public function jobs_job_show()
    {
        /** 1
         * Create a Job
         */
        $this->createJob();


        /** 2
         * Get the Job
         */
        $url = route('jobs.job.show', [1]);
        $response = $this->json('get', $url);


        /** 3
         * Assert
         */
        $response->assertStatus(Response::HTTP_OK);
        $this->jobJsonFragmentAssert($response);
    }

    /** #4
     * @test
     */
    public function jobs_job_destroy()
    {
        /** 1
         * Create a Job
         */
        $this->createJob();


        /** 2
         * Delete the Job
         */
        $url = route('jobs.job.show', [1]);
        $response = $this->json('delete', $url, [
            'token' => $this->getToken($this->goldUser),
        ]);


        /** 3
         * Assert
         */
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertCount(0, Job::all());
    }

    /** #5
     * @test
     */
    public function jobs_job_index()
    {
        /** 1
         * Create a few jobs
         */
        factory(Job::class, 10)->create();

        /** 2
         * Get job Index
         */
        $url = route('jobs.job.index');
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->normalUser)
        ]);

        /** 3
         * Assert
         */
        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(10, Job::all());
    }

    /** #6
     * @test
     */
    public function jobs_job_filter_options()
    {
        $url = route('jobs.job.filter.options');
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->silverUser)
        ]);
        $response->assertStatus(Response::HTTP_OK);
    }

    /** #7
     * @test
     */
    public function jobs_job_filter()
    {
        /** 1
         * Create a Job
         */
        $this->createJob();

        /** 2
         * Filter jobs in a way our job don't get filtered.
         */
        $url = route('jobs.job.filter');
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->silverUser),
            'city_id' => 1,
            'job_category_id' => 1,
            'job_payment_id' => 1,
            'company_id' => 1,
            'work_experience_years_id' => 1,
            'job_degree_id' => 1,
            'job_time_status_id' => 1,
        ]);

        /** 3
         * Assert that there is still one job in response data
         */
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(1, 'data');
    }

    /** #8
     * @test
     */
    public function jobs_job_user()
    {
        /** 1
         * Create Two jobs for Gold user
         */
        $this->createJob();
        $this->createJob();


        /** 2
         * Get his Jobs
         */
        $url = route('jobs.job.user', ['user' => $this->goldUser->id]);
        $response = $this->json('get', $url, ['token' => $this->getToken($this->goldUser)]);

        /** 2
         * Assert
         */
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(2, 'data');
    }

    /** #9
     * @test
     */
    public function jobs_job_apply()
    {
        /** 1
         * Create a Job and a Resume (job for Gold user and resume for Normal user)
         */
        $this->createJob();
        $this->createResume();

        /** 2
         * Apply to the job
         */
        $url = route('jobs.job.apply', [1]);
        $response = $this->json('put', $url, [
            'token' => $this->getToken($this->normalUser)
        ]);

        /** 3
         * Assert
         */
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertCount(1, Job::first()->resumes);
        $response->assertJson([
            'success' => 'Resume applied successfully'
        ]);

    }

    /** #10
     * @test
     */
    public function jobs_job_applied_resumes()
    {
        /** 1
         * Create a Job and a Resume (job for Gold user and resume for Normal user) And Apply to it
         */
        $this->createJob();
        $this->createResume();
        $url = route('jobs.job.apply', [1]);
        $this->json('put', $url, [
            'token' => $this->getToken($this->normalUser)
        ]);


        /** 2
         * Get applied resumes
         */
        $url = route('jobs.job.applied.resumes', [1]);
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->goldUser)
        ]);

        /** 3
         * Assert
         */
        $response->assertStatus(Response::HTTP_OK);


    }


    /***********************************-----------
     *
     *              Refactors                      =======================
     *
     * /**********************************/

    private function createJobEssentials()
    {
        factory(Company::class)->create([
            'user_id' => $this->goldUser->id
        ]);

        factory(City::class, 2)->create();
        factory(Country::class, 2)->create();
        factory(Province::class, 2)->create();
        factory(JobTimeStatus::class, 2)->create();
        factory(JobCategory::class, 2)->create();
        factory(JobPayment::class, 2)->create();
        factory(Gender::class, 2)->create();
        factory(DutyStatus::class, 2)->create();
        factory(JobDegree::class, 2)->create();
        factory(WorkExperienceYears::class, 2)->create();
        factory(Religion::class, 2)->create();
    }

    /**
     * @return TestResponse
     */
    private function createJob(): TestResponse
    {
        $url = route('jobs.job.store');
        return $this->json('post', $url, [
            'token' => $this->getToken($this->goldUser),
            'title' => 'test title',
            'phone' => '12345678',
            'email' => 'test@gmail.com',
            'telegram' => 'telegram_id',
            'description' => 'test description',
            'required_skills' => 'test required skills',
            'can_send_resume' => 0,
            'is_premium' => 0,
            'job_time_status_id' => 1,
            'job_category_id' => 1,
            'city_id' => 1,
            'province_id' => 1,
            'job_payment_id' => 1,
            'gender_id' => 1,
            'duty_status_id' => 1,
            'job_degree_id' => 1,
            'work_experience_years_id' => 1,
        ]);
    }

    /**
     * @param TestResponse $response
     */
    private function jobJsonFragmentAssert(TestResponse $response): void
    {
        $response->assertJsonFragment([
            'title' => 'test title',
            'phone' => '12345678',
            'email' => 'test@gmail.com',
            'telegram' => 'telegram_id',
            'description' => 'test description',
            'required_skills' => 'test required skills',
            'can_send_resume' => 0,
            'is_premium' => 0,

            'job_time_status' => [
                'id' => JobTimeStatus::first()->id,
                'name' => JobTimeStatus::first()->name,
            ],
            'job_category' => [
                'id' => JobCategory::first()->id,
                'name' => JobCategory::first()->name,
            ],
            'city' => [
                'id' => City::first()->id,
                'name' => City::first()->name,
            ],
            'province' => [
                'id' => Province::first()->id,
                'name' => Province::first()->name,
            ],
            'job_payment' => [
                'id' => JobPayment::first()->id,
                'name' => JobPayment::first()->name,
            ],
            'gender' => [
                'id' => Gender::first()->id,
                'name' => Gender::first()->name,
            ],
            'duty_status' => [
                'id' => DutyStatus::first()->id,
                'name' => DutyStatus::first()->name,
            ],
            'job_degree' => [
                'id' => JobDegree::first()->id,
                'name' => JobDegree::first()->name,
            ],
            'work_experience_years' => [
                'id' => WorkExperienceYears::first()->id,
                'name' => WorkExperienceYears::first()->name,
            ],
        ]);
    }

    /**
     * @return TestResponse
     */
    private function updateJob(): TestResponse
    {
        $url = route('jobs.job.update', [1]);
        return $this->json('put', $url, [
            'token' => $this->getToken($this->goldUser),
            'title' => 'updated title',
            'phone' => '87654321',
            'email' => 'updated@gmail.com',
            'telegram' => 'updated_id',
            'description' => 'updated description',
            'required_skills' => 'updated required skills',
            'can_send_resume' => 1,
            'is_premium' => 1,
            'job_time_status_id' => 2,
            'job_category_id' => 2,
            'city_id' => 2,
            'province_id' => 2,
            'job_payment_id' => 2,
            'gender_id' => 2,
            'duty_status_id' => 2,
            'job_degree_id' => 2,
            'work_experience_years_id' => 2,
        ]);
    }

    /**
     * @param TestResponse $response
     */
    private function updatedJobJsonFragmentAssert(TestResponse $response): void
    {
        $response->assertJsonFragment([
            'title' => 'updated title',
            'phone' => '87654321',
            'email' => 'updated@gmail.com',
            'telegram' => 'updated_id',
            'description' => 'updated description',
            'required_skills' => 'updated required skills',
            'can_send_resume' => 1,
            'is_premium' => 1,

            'job_time_status' => [
                'id' => JobTimeStatus::get()->last()->id,
                'name' => JobTimeStatus::get()->last()->name,
            ],
            'job_category' => [
                'id' => JobCategory::get()->last()->id,
                'name' => JobCategory::get()->last()->name,
            ],
            'city' => [
                'id' => City::get()->last()->id,
                'name' => City::get()->last()->name,
            ],
            'province' => [
                'id' => Province::get()->last()->id,
                'name' => Province::get()->last()->name,
            ],
            'job_payment' => [
                'id' => JobPayment::get()->last()->id,
                'name' => JobPayment::get()->last()->name,
            ],
            'gender' => [
                'id' => Gender::get()->last()->id,
                'name' => Gender::get()->last()->name,
            ],
            'duty_status' => [
                'id' => DutyStatus::get()->last()->id,
                'name' => DutyStatus::get()->last()->name,
            ],
            'job_degree' => [
                'id' => JobDegree::get()->last()->id,
                'name' => JobDegree::get()->last()->name,
            ],
            'work_experience_years' => [
                'id' => WorkExperienceYears::get()->last()->id,
                'name' => WorkExperienceYears::get()->last()->name,
            ],
        ]);
    }

    /**
     * @return TestResponse
     */
    private function createResume()
    {
        $url = route('jobs.resume.store');
        return $this->json('post', $url, [
            'token' => $this->getToken($this->normalUser),
            'full_name_persian' => 'پوریا هنرنمد',
            'full_name_english' => 'pooria honarmand',
            'father_name' => 'محمد',
            'birthday' => '1/29/1992',
            'national_code' => '123465798',
            'identity_number' => '123465798',
            'dependants_count' => 1,
            'is_married' => 1,
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


}
