<?php

namespace Tests\Feature\User;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserSecurityTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->createUsers();

        Storage::fake('public');
    }


    /***********************************-----------
     *
     *              jwt           =======================
     *
     * /**********************************/

    /** @test */
    public function user_jwt_access()
    {
        /** open // temporary closed */
        $this->showUser(null)->assertUnauthorized();

        /** protected */
        $this->tryUpdateUser(null)->assertUnauthorized();
        $this->ban(null, $this->normalUser)->assertUnauthorized();
    }


    /** @test */
    public function correct_roles_can_index_all_users()
    {
        $this->indexUsers($this->normalUser)->assertForbidden();
        $this->indexUsers($this->goldUser)->assertForbidden();
        $this->indexUsers($this->silverUser)->assertForbidden();
        $this->indexUsers($this->contactManager)->assertForbidden();
        $this->indexUsers($this->jobManager)->assertForbidden();
        $this->indexUsers($this->networkManager)->assertForbidden();
        $this->indexUsers($this->admin)->assertForbidden();

        $this->indexUsers($this->superAdmin)->assertOk();
    }


    /** @test */
    public function correct_roles_can_change_role()
    {
        $this->changeRole($this->normalUser, $this->silverUser)->assertForbidden();
        $this->changeRole($this->goldUser, $this->normalUser)->assertForbidden();
        $this->changeRole($this->silverUser, $this->normalUser)->assertForbidden();
        $this->changeRole($this->contactManager, $this->normalUser)->assertForbidden();
        $this->changeRole($this->jobManager, $this->normalUser)->assertForbidden();
        $this->changeRole($this->networkManager, $this->normalUser)->assertForbidden();
        $this->changeRole($this->admin, $this->normalUser)->assertForbidden();

        $this->changeRole($this->superAdmin, $this->normalUser)->assertStatus(Response::HTTP_ACCEPTED);
    }

    /***********************************-----------
     *
     *              Policies           =======================
     *
     * /**********************************/

    /** @test */
    public function user_cannot_update_someone_else_info()
    {
        $this->tryUpdateUser($this->silverUser)->assertForbidden();
    }

    /** @test */
    public function super_admin_can_update_some_one_else_info()
    {
        $this->tryUpdateUser($this->superAdmin)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }


    /**
     * BAN Policies
     */

    /** @test */
    public function other_users_cannot_ban_anyone()
    {
        $this->ban($this->silverUser, $this->superAdmin)->assertForbidden();
        $this->ban($this->goldUser, $this->admin)->assertForbidden();
        $this->ban($this->normalUser, $this->networkManager)->assertForbidden();
        $this->ban($this->goldUser, $this->normalUser)->assertForbidden();
        $this->ban($this->normalUser, $this->silverUser)->assertForbidden();
    }

    /** @test */
    public function super_admin_can_ban_anyone_bellow_his_rank()
    {
        $this->ban($this->superAdmin, $this->superAdmin)->assertForbidden();
        $this->ban($this->superAdmin, $this->admin)->assertStatus(Response::HTTP_ACCEPTED);
        $this->ban($this->superAdmin, $this->jobManager)->assertStatus(Response::HTTP_ACCEPTED);
        $this->ban($this->superAdmin, $this->goldUser)->assertStatus(Response::HTTP_ACCEPTED);
    }


    /** @test */
    public function other_managers_cannot_ban_anyone()
    {
        $this->ban($this->networkManager, $this->normalUser)->assertForbidden();
        $this->ban($this->jobManager, $this->admin)->assertForbidden();
        $this->ban($this->jobManager, $this->goldUser)->assertForbidden();
        $this->ban($this->jobManager, $this->networkManager)->assertForbidden();
    }


    /** @test */
    public function other_managers_and_admin_cannot_destroy_anyone()
    {
        $this->destroy($this->admin, $this->goldUser)->assertForbidden();
        $this->destroy($this->networkManager, $this->normalUser)->assertForbidden();
        $this->destroy($this->jobManager, $this->admin)->assertForbidden();
        $this->destroy($this->jobManager, $this->networkManager)->assertForbidden();
    }


    /** @test */
    public function super_admin_can_destroy_anyone_bellow_his_rank()
    {
        $this->destroy($this->superAdmin, $this->superAdmin)->assertForbidden();
        $this->destroy($this->superAdmin, $this->admin)->assertStatus(Response::HTTP_ACCEPTED);
        $this->destroy($this->superAdmin, $this->jobManager)->assertStatus(Response::HTTP_ACCEPTED);
        $this->destroy($this->superAdmin, $this->goldUser)->assertStatus(Response::HTTP_ACCEPTED);
    }


    /***********************************-----------
     *
     *              Refactors           =======================
     *
     * /*********************************/

    private function indexUsers($user): TestResponse
    {
        $url = route('user.index');
        return $this->json('get', $url, ['token' => $this->getToken($user)]);
    }

    private function showUser($user): TestResponse
    {
        $url = route('user.show', [1]);
        return $this->json('get', $url, ['token' => $this->getToken($user)]);
    }

    private function tryUpdateUser($user): TestResponse
    {
        $url = route('user.update', [1]);
        return $this->json('put', $url, ['token' => $this->getToken($user)]);
    }

    private function ban($user, $banTarget): TestResponse
    {
        $url = route('user.ban', ['user' => $banTarget->id]);
        return $this->json('put', $url, ['token' => $this->getToken($user)]);
    }

    private function changeRole($user, $target): TestResponse
    {
        $url = route('user.role.change', ['user' => $target->id, 'role' => 4]);
        return $this->json('put', $url, ['token' => $this->getToken($user)]);
    }

    private function destroy($user, $target): TestResponse
    {
        $url = route('user.destroy', ['user' => $target->id]);
        return $this->json('delete', $url, ['token' => $this->getToken($user)]);
    }


    /***********************************-----------
     *
     *              BigComment           =======================
     *
     * /**********************************/

}
