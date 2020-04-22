<?php

namespace Tests\Unit\Job;

use App\Models\Globals\City;
use App\Models\Globals\Country;
use App\Models\Globals\Gender;
use App\Models\Globals\Province;
use App\Models\Globals\Religion;
use App\Models\Job\DutyStatus;
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

class JobsResumeSecurityTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $roleError;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createUsers();

        $this->roleError = config('app.role_error');

        $this->createCredentials();
    }


    /***********************************-----------
     *
     *              Middleware           =======================
     *
     * /**********************************/

    /** @test */
    public function resume_jwt_access()
    {
        $this->factoryResume($this->normalUser);

        $this->getResume(null)->assertUnauthorized();
        $this->storeResume(null)->assertUnauthorized();
        $this->updateResume(null)->assertUnauthorized();
    }





    /***********************************-----------
     *
     *              Policies           =======================
     *
     * /**********************************/

    /** @test */
    public function cannot_create_two_resumes()
    {
        $this->factoryResume($this->superAdmin);

        $this->storeResume($this->superAdmin)->assertForbidden();
    }


    /** @test */
    public function cannot_get_someone_else_resume()
    {
        $this->factoryResume($this->silverUser);

        $this->getResume($this->normalUser)->assertForbidden();
    }


    /** @test */
    public function cannot_update_someone_else_resume()
    {
        $this->factoryResume($this->goldUser);

        $this->updateResume($this->silverUser)->assertForbidden();
    }

    /***********************************-----------
     *
     *              Manager Policies           =======================
     *
     * /**********************************/

    /** @test */
    public function super_admin_can_get_someone_else_resume()
    {
        $this->factoryResume($this->goldUser);

        $this->getResume($this->superAdmin)->assertOk();
    }

    /** @test */
    public function admin_can_get_someone_else_resume()
    {
        $this->factoryResume($this->goldUser);

        $this->getResume($this->admin)->assertOk();
    }

    /** @test */
    public function job_manager_can_get_someone_else_resume()
    {
        $this->factoryResume($this->goldUser);

        $this->getResume($this->jobManager)->assertOk();
    }


    /** @test */
    public function super_admin_can_update_someone_else_resume()
    {
        $this->factoryResume($this->goldUser);

        $this->updateResume($this->superAdmin)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function admin_can_update_someone_else_resume()
    {
        $this->factoryResume($this->goldUser);

        $this->updateResume($this->admin)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function job_manager_can_update_someone_else_resume()
    {
        $this->factoryResume($this->goldUser);

        $this->updateResume($this->jobManager)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }


    /** @test */
    public function only_correct_managers_can_get_resumes_index()
    {
        $this->getResumeIndex($this->normalUser)->assertForbidden();
        $this->getResumeIndex($this->silverUser)->assertForbidden();
        $this->getResumeIndex($this->goldUser)->assertForbidden();
        $this->getResumeIndex($this->networkManager)->assertForbidden();
        $this->getResumeIndex($this->contactManager)->assertForbidden();


        $this->getResumeIndex($this->superAdmin)->assertOk();
        $this->getResumeIndex($this->admin)->assertOk();
        $this->getResumeIndex($this->jobManager)->assertOk();

    }


    /***********************************-----------
     *
     *              Refactors           =======================
     *
     * /**********************************/
    private function storeResume($user): TestResponse
    {
        $url = route('jobs.resume.store');
        return $this->json('post', $url, ['token' => $this->getToken($user)]);
    }

    private function updateResume($user): TestResponse
    {
        $url = route('jobs.resume.update', [1]);
        return $this->json('put', $url, ['token' => $this->getToken($user)]);
    }

    private function getResume($user): TestResponse
    {
        $url = route('jobs.resume.show', [1]);
        return $this->json('get', $url, ['token' => $this->getToken($user)]);
    }

    private function getResumeIndex($user): TestResponse
    {
        $url = route('jobs.resume.index');
        return $this->json('get', $url, ['token' => $this->getToken($user)]);
    }


    /**
     * @param $user
     */
    private function factoryResume($user): void
    {
        factory(Resume::class)->create(['user_id' => $user->id]);
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
