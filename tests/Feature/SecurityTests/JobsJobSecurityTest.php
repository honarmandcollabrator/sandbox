<?php

namespace Tests\Unit\Job;

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
use App\Models\Job\Resume;
use App\Models\Job\WorkExperienceYears;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class JobsJobSecurityTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $roleError;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createUsers();

        $this->createCredentials();

        $this->roleError = config('app.role_error');
    }














    /***********************************-----------
     *
     *              Middlewares           =======================
     *
     * /**********************************/

    /** @test */
    public function jwt_is_protecting_job_routes()
    {
        /** 1
         * factory a Job (+company) : reason is to not get model not found error.
         */
        $this->factoryJob($this->goldUser);


        /** 2
         * Asserts
         */

        /** open routes = temporary closed! */
//        $this->indexJobs(null)->assertOk();
//        $this->getJob(null)->assertOk();
//        $this->getUserJobs(null, $this->goldUser)->assertOk();

        /** protected routes */
        $this->tryStoreJob(null)->assertUnauthorized();
        $this->tryUpdateJob(null)->assertUnauthorized();
        $this->deleteJob(null)->assertUnauthorized();
        $this->getFilterOptions(null)->assertUnauthorized();
        $this->filterJobs(null)->assertUnauthorized();
        $this->getAppliedResumes(null)->assertUnauthorized();
        $this->applyToJob(null)->assertUnauthorized();
    }

    /** @test */
    public function need_silver_job_routes()
    {
        /** normal user */
//        $this->getFilterOptions($this->normalUser)->assertSee($this->roleError);
        $this->filterJobs($this->normalUser)->assertSee($this->roleError);

        /** silver user */
        $this->getFilterOptions($this->silverUser)->assertOk();
        $this->filterJobs($this->silverUser)->assertOk();

        /** gold user */
        $this->getFilterOptions($this->goldUser)->assertOk();
        $this->filterJobs($this->goldUser)->assertOk();

        /** admin user */
        $this->getFilterOptions($this->superAdmin)->assertOk();
        $this->filterJobs($this->superAdmin)->assertOk();
    }

    /** @test */
    public function normal_and_silver_users_no_access_to_gold_routes()
    {
        /** 1
         * factory a Job (+company) : reason is to not get model not found error.
         */
        $this->factoryJob($this->goldUser);


        /** normal user */
        $this->tryStoreJob($this->normalUser)->assertSee($this->roleError);
        $this->tryUpdateJob($this->normalUser)->assertSee($this->roleError);
        $this->getAppliedResumes($this->normalUser)->assertSee($this->roleError);
        $this->deleteJob($this->normalUser)->assertSee($this->roleError);

        /** silver user */
        $this->tryStoreJob($this->silverUser)->assertSee($this->roleError);
        $this->tryUpdateJob($this->silverUser)->assertSee($this->roleError);
        $this->getAppliedResumes($this->silverUser)->assertSee($this->roleError);
        $this->deleteJob($this->silverUser)->assertSee($this->roleError);
    }

    /** @test */
    public function gold_user_has_access_to_gold_routes()
    {
        /** 1
         * factory a Job (+company)
         */
        $this->factoryJob($this->goldUser);

        /** gold user */
        $this->getAppliedResumes($this->goldUser)->assertOk();
        $this->tryStoreJob($this->goldUser)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->tryUpdateJob($this->goldUser)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->deleteJob($this->goldUser)->assertStatus(Response::HTTP_NO_CONTENT);

    }

    /** @test */
    public function super_admin_has_access_to_gold_routes()
    {
        /** 1
         * factory a Job (+company) and a Resume then apply to job:
         */
        $this->factoryJob($this->superAdmin);

        /** admin user */
        $this->getAppliedResumes($this->superAdmin)->assertOk();
        $this->tryStoreJob($this->superAdmin)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->tryUpdateJob($this->superAdmin)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->deleteJob($this->superAdmin)->assertStatus(Response::HTTP_NO_CONTENT);

    }


    /** @test */
    public function admin_has_access_to_gold_routes()
    {
        /** 1
         * factory a Job (+company) and a Resume then apply to job:
         */
        $this->factoryJob($this->admin);

        /** admin user */
        $this->getAppliedResumes($this->admin)->assertOk();
        $this->tryStoreJob($this->admin)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->tryUpdateJob($this->admin)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->deleteJob($this->admin)->assertStatus(Response::HTTP_NO_CONTENT);

    }

    /** @test */
    public function job_manager_has_access_to_gold_routes()
    {
        /** 1
         * factory a Job (+company) and a Resume then apply to job:
         */
        $this->factoryJob($this->jobManager);

        /** admin user */
        $this->getAppliedResumes($this->jobManager)->assertOk();
        $this->tryStoreJob($this->jobManager)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->tryUpdateJob($this->jobManager)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->deleteJob($this->jobManager)->assertStatus(Response::HTTP_NO_CONTENT);

    }












    /***********************************-----------
     *
     *              Policies           =======================
     *
     * /**********************************/


    /** @test */
    public function cannot_create_more_that_10_jobs_per_day()
    {
        /*===== 1- We create 10 jobs for gold user =====*/
        factory(Company::class)->create(['user_id' => $this->goldUser->id]);
        factory(Job::class, 10)->create(['company_id' => $this->goldUser->company->id]);
        $this->assertCount(10, Job::all());

        /*===== 2- Assert that we can't create more jobs =====*/
        $response = $this->tryStoreJob($this->goldUser)->assertForbidden();
    }


    /** @test */
    public function cannot_update_someone_else_job()
    {
        $this->factoryJob($this->superAdmin);

        $this->tryUpdateJob($this->goldUser)->assertForbidden();
    }

    /** @test */
    public function cannot_delete_someone_else_job()
    {
        $this->factoryJob($this->superAdmin);

        $this->deleteJob($this->goldUser)->assertForbidden();
    }

    /** @test */
    public function cannot_store_job_if_not_having_company()
    {
        $this->tryStoreJob($this->superAdmin)->assertForbidden();
    }

    /** @test */
    public function cannot_apply_if_not_having_resume()
    {
        $this->factoryJob($this->goldUser);

        $this->applyToJob($this->normalUser)->assertForbidden();
    }

    /** @test */
    public function cannot_apply_to_your_own_job()
    {
        $this->factoryJob($this->goldUser);
        $this->factoryResume($this->goldUser);

        $this->applyToJob($this->goldUser)->assertForbidden();
    }


    /** @test */
    public function cannot_apply_to_your_own_job_even_as_admin()
    {
        $this->factoryJob($this->superAdmin);
        $this->factoryResume($this->superAdmin);

        $this->applyToJob($this->superAdmin)->assertForbidden();
    }


    /** @test */
    public function still_can_apply_to_someone_else_job()
    {
        $this->factoryJob($this->superAdmin);
        $this->factoryResume($this->goldUser);

        $this->applyToJob($this->goldUser)->assertStatus(Response::HTTP_ACCEPTED);
    }

    /** @test */
    public function still_can_store_job_if_have_company()
    {
        $this->factoryCompany($this->superAdmin);

        $this->tryStoreJob($this->superAdmin)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function cannot_get_someone_else_applied_resumes()
    {
        $this->factoryJob($this->superAdmin);

        $this->getAppliedResumes($this->goldUser)->assertForbidden();
    }




    /***********************************-----------
     *
     *              Managers policies          =======================
     *
     * /**********************************/


    /** @test */
    public function super_admin_can_update_someone_else_job()
    {
        $this->factoryJob($this->goldUser);

        $this->tryUpdateJob($this->superAdmin)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function admin_can_update_someone_else_job()
    {
        $this->factoryJob($this->goldUser);

        $this->tryUpdateJob($this->admin)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function job_manager_can_update_someone_else_job()
    {
        $this->factoryJob($this->goldUser);

        $this->tryUpdateJob($this->jobManager)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function super_admin_can_delete_someone_else_job()
    {
        $this->factoryJob($this->goldUser);

        $this->deleteJob($this->superAdmin)->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function admin_can_delete_someone_else_job()
    {
        $this->factoryJob($this->goldUser);

        $this->deleteJob($this->admin)->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function job_manager_can_delete_someone_else_job()
    {
        $this->factoryJob($this->goldUser);

        $this->deleteJob($this->jobManager)->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function super_admin_can_get_someone_else_applied_resumes()
    {
        $this->factoryJob($this->goldUser);

        $this->getAppliedResumes($this->superAdmin)->assertOk();
    }

    /** @test */
    public function admin_can_get_someone_else_applied_resumes()
    {
        $this->factoryJob($this->goldUser);

        $this->getAppliedResumes($this->admin)->assertOk();
    }

    /** @test */
    public function job_manager_can_get_someone_else_applied_resumes()
    {
        $this->factoryJob($this->goldUser);

        $this->getAppliedResumes($this->jobManager)->assertOk();
    }
































    /***********************************-----------
     *
     *              Refactors           =======================
     *
     * /**********************************/


    /** 1
     * @param $user
     * @return TestResponse
     */
    private function indexJobs($user): TestResponse
    {
        $url = route('jobs.job.index');
        return $this->json('get', $url, ['token' => $this->getToken($user)]);
    }

    /** 2
     * @param $user
     * @return TestResponse
     */
    private function getJob($user): TestResponse
    {
        $url = route('jobs.job.show', [1]);
        return $this->json('get', $url, ['token' => $this->getToken($user)]);
    }


    /** 3
     * @param $user
     * @return TestResponse
     */
    private function tryStoreJob($user): TestResponse
    {
        $url = route('jobs.job.store');
        return $this->json('post', $url, ['token' => $this->getToken($user)]);
    }

    /** 4
     * @param $user
     * @return TestResponse
     */
    private function tryUpdateJob($user): TestResponse
    {
        $url = route('jobs.job.update', [1]);
        return $this->json('put', $url, ['token' => $this->getToken($user)]);
    }


    /** 5
     * @param $user
     * @return TestResponse
     */
    private function deleteJob($user): TestResponse
    {
        $url = route('jobs.job.destroy', ['job' => 1]);
        return $this->json('delete', $url, ['token' => $this->getToken($user)]);
    }

    /** 6
     * @param $user
     * @return TestResponse
     */
    private function getFilterOptions($user): TestResponse
    {
        $url = route('jobs.job.filter.options');
        return $this->json('get', $url, ['token' => $this->getToken($user)]);
    }


    /** 7
     * @param $user
     * @return TestResponse
     */
    private function filterJobs($user): TestResponse
    {
        $url = route('jobs.job.filter');
        return $this->json('get', $url, ['token' => $this->getToken($user)]);
    }


    /** 8
     * @param $user
     * @return TestResponse
     */
    private function applyToJob($user): TestResponse
    {
        $url = route('jobs.job.apply', ['job' => 1]);
        return $this->json('put', $url, ['token' => $this->getToken($user)]);
    }

    /** 9
     * @param $user
     * @return TestResponse
     */
    private function getAppliedResumes($user): TestResponse
    {
        $url = route('jobs.job.applied.resumes', [1]);
        return $this->json('get', $url, ['token' => $this->getToken($user)]);
    }

    /** 10
     * @param $user
     * @param $owner
     * @return TestResponse
     */
    private function getUserJobs($user, $owner): TestResponse
    {
        $url = route('jobs.job.user', ['user' => $owner->id]);
        return $this->json('get', $url, ['token' => $this->getToken($user)]);
    }


    /**
     * @param $user
     */
    private function factoryJob($user): void
    {
        factory(Company::class)->create(['user_id' => $user->id]);
        factory(Job::class)->create(['company_id' => $user->company->id]);
    }

    /**
     * @param $user
     */
    private function factoryResume($user): void
    {
        factory(Resume::class)->create(['user_id' => $user->id]);
    }

    /**
     * @param $user
     */
    private function factoryCompany($user): void
    {
        factory(Company::class)->create(['user_id' => $user->id]);
    }


    private function createCredentials()
    {
        factory(City::class)->create();
        factory(Country::class)->create();
        factory(Province::class)->create();
        factory(JobTimeStatus::class)->create();
        factory(JobCategory::class)->create();
        factory(JobPayment::class)->create();
        factory(Gender::class)->create();
        factory(DutyStatus::class)->create();
        factory(JobDegree::class)->create();
        factory(WorkExperienceYears::class)->create();
        factory(Religion::class)->create();
    }


}
