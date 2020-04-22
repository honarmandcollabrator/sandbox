<?php

namespace Tests\Feature\User;

use App\Models\User\UserExperience;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserExperienceSecurityTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->createUsers();
    }

    /***********************************-----------
     *
     *              Middlewares           =======================
     *
     * /**********************************/

    /** @test */
    public function jwt_is_protecting_user_experience_routes()
    {
        $this->factoryExperience($this->normalUser);

        $this->storeExperience(null)->assertUnauthorized();
        $this->updateExperience(null)->assertUnauthorized();
        $this->destroyExperience(null)->assertUnauthorized();
    }


    /***********************************-----------
     *
     *              Policies           =======================
     *
     * /**********************************/


    /** @test */
    public function cannot_create_more_than_20_experiences()
    {
        factory(UserExperience::class, 20)->create(['user_id' => $this->superAdmin->id]);

        $this->storeExperience($this->superAdmin)->assertForbidden();
    }


    /** @test */
    public function user_cannot_update_and_delete_someone_else_experience()
    {
        $this->factoryExperience($this->normalUser);

        $this->updateExperience($this->goldUser)->assertForbidden();
        $this->destroyExperience($this->goldUser)->assertForbidden();
    }


    /** @test */
    public function super_admin_can_update_and_delete_someone_else_experience()
    {
        $this->factoryExperience($this->goldUser);

        $this->updateExperience($this->superAdmin)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->destroyExperience($this->superAdmin)->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function admin_can_update_and_delete_someone_else_experience()
    {
        $this->factoryExperience($this->goldUser);

        $this->updateExperience($this->admin)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->destroyExperience($this->admin)->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function job_manager_can_update_and_delete_someone_else_experience()
    {
        $this->factoryExperience($this->goldUser);

        $this->updateExperience($this->jobManager)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->destroyExperience($this->jobManager)->assertStatus(Response::HTTP_NO_CONTENT);
    }



    /***********************************-----------
     *
     *              Refactors           =======================
     *
     * /**********************************/

    private function storeExperience($user)
    {
        $url = route('user.experiences.store', [1]);
        return $this->json('post', $url, ['token' => $this->getToken($user)]);
    }

    private function updateExperience($user)
    {
        $url = route('user.experiences.update', [1, 1]);
        return $this->json('put', $url, ['token' => $this->getToken($user)]);
    }

    private function destroyExperience($user)
    {
        $url = route('user.experiences.destroy', [1, 1]);
        return $this->json('delete', $url, ['token' => $this->getToken($user)]);
    }

    private function factoryExperience($user)
    {
        factory(UserExperience::class)->create(['user_id' => $user->id]);
    }



}
