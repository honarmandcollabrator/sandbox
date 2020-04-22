<?php

namespace Tests\Unit\Job;

use App\Models\Job\Company;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class JobsCompanySecurityTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $roleError;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createUsers();

        $this->roleError = config('app.role_error');
    }



    /***********************************-----------
     *
     *              Middlewares           =======================
     *
     * /**********************************/

    /** @test */
    public function company_jwt_access()
    {
        $this->factoryCompany($this->goldUser);

        /** open // Temporary Closed*/
        $this->getCompany(null)->assertUnauthorized();

        /** protected */
        $this->storeCompany(null)->assertUnauthorized();
        $this->updateCompany(null)->assertUnauthorized();
    }


    /** @test */
    public function normal_and_silver_users_no_access_to_gold_company_routes()
    {
        $this->factoryCompany($this->goldUser);

        /** normal user */
        $this->storeCompany($this->normalUser)->assertSee($this->roleError);
        $this->updateCompany($this->normalUser)->assertSee($this->roleError);

        /** silver user */
        $this->storeCompany($this->silverUser)->assertSee($this->roleError);
        $this->updateCompany($this->silverUser)->assertSee($this->roleError);
    }

    /** @test */
    public function gold_user_has_access_to_gold_company_routes()
    {
        $this->storeCompany($this->goldUser)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->factoryCompany($this->goldUser);
        $this->updateCompany($this->goldUser)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }


    /** @test */
    public function super_admin_user_has_access_to_gold_company_routes()
    {
        $this->storeCompany($this->superAdmin)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->factoryCompany($this->superAdmin);
        $this->updateCompany($this->superAdmin)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }



    /***********************************-----------
     *
     *              Policies           =======================
     *
     * /**********************************/

    /** @test */
    public function cannot_update_someone_else_company()
    {
        $this->factoryCompany($this->superAdmin);

        $this->updateCompany($this->goldUser)->assertForbidden();
    }

    /** @test */
    public function cannot_create_two_companies()
    {
        $this->factoryCompany($this->superAdmin);

        $this->storeCompany($this->superAdmin)->assertForbidden();
    }

    /** @test */
    public function super_admin_can_update_someone_else_company()
    {
        $this->factoryCompany($this->goldUser);

        $this->updateCompany($this->superAdmin)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }


    /** @test */
    public function admin_can_update_someone_else_company()
    {
        $this->factoryCompany($this->goldUser);

        $this->updateCompany($this->admin)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function job_manager_can_update_someone_else_company()
    {
        $this->factoryCompany($this->goldUser);

        $this->updateCompany($this->jobManager)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }









    /***********************************-----------
     *
     *              Refactors           =======================
     *
     * /**********************************/


    /**
     * @param $user
     * @return TestResponse
     */
    private function storeCompany($user): TestResponse
    {
        $url = route('jobs.company.store');
        return $this->json('post', $url, ['token' => $this->getToken($user)]);
    }

    /**
     * @param $user
     * @return TestResponse
     */
    private function updateCompany($user): TestResponse
    {
        $url = route('jobs.company.update', [1]);
        return $this->json('put', $url, ['token' => $this->getToken($user)]);
    }

    /**
     * @param $user
     * @return TestResponse
     */
    private function getCompany($user): TestResponse
    {
        $url = route('jobs.company.show', [1]);
        return $this->json('get', $url, ['token' => $this->getToken($user)]);
    }


    /**
     * @param $user
     */
    private function factoryCompany($user): void
    {
        factory(Company::class)->create(['user_id' => $user->id]);
    }


}
