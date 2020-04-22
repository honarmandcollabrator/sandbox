<?php

namespace Tests\Feature\FunctionalityTests;

use App\Models\Network\Group;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class NetworkGroupSecurityTest extends TestCase
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
     *              JWT auth           =======================
     *
     * /**********************************/

    /** @test */
    public function jwt_is_protecting_group_routes()
    {
        $this->storeGroup($this->normalUser);

        $this->indexGroup(null)->assertUnauthorized();
        $this->tryStoreGroup(null)->assertUnauthorized();
        $this->tryUpdateGroup(null)->assertUnauthorized();
        $this->deleteGroup(null)->assertUnauthorized();
    }



    /***********************************-----------
     *
     *              Policies           =======================
     *
     * /**********************************/

    /** @test */
    public function cannot_create_more_than_5_groups()
    {
        factory(Group::class, 5)->create(['admin_id' => $this->normalUser->id]);
        $this->normalUser->groups()->attach([1 => ['status' => 'approved']]);
        $this->normalUser->groups()->attach([2 => ['status' => 'approved']]);
        $this->normalUser->groups()->attach([3 => ['status' => 'approved']]);
        $this->normalUser->groups()->attach([4 => ['status' => 'approved']]);
        $this->normalUser->groups()->attach([5 => ['status' => 'approved']]);
        $this->assertCount(5, $this->normalUser->groups);

        $this->storeGroup($this->normalUser)->assertForbidden();
    }

    /** @test */
    public function cannot_update_someone_else_group()
    {
        $this->storeGroup($this->goldUser);

        $this->tryUpdateGroup($this->normalUser)->assertForbidden();
    }

    /** @test */
    public function cannot_delete_someone_else_group()
    {
        $this->storeGroup($this->goldUser);

        $this->deleteGroup($this->normalUser)->assertForbidden();
    }


    /** @test */
    public function correct_users_can_index_groups()
    {
        $this->indexGroup($this->normalUser)->assertForbidden();
        $this->indexGroup($this->silverUser)->assertForbidden();
        $this->indexGroup($this->goldUser)->assertForbidden();
        $this->indexGroup($this->contactManager)->assertForbidden();
        $this->indexGroup($this->jobManager)->assertForbidden();

        $this->indexGroup($this->networkManager)->assertOk();
        $this->indexGroup($this->admin)->assertOk();
        $this->indexGroup($this->superAdmin)->assertOk();

    }








    /***********************************-----------
     *
     *              Manager Policies           =======================
     *
     * /**********************************/


    /** @test */
    public function manager_can_update_group_for_someone_else()
    {
        $this->storeGroup($this->goldUser);

        $this->tryUpdateGroup($this->networkManager)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    }

    /** @test */
    public function manager_can_delete_group_for_someone_else()
    {
        $this->storeGroup($this->goldUser);

        $this->deleteGroup($this->superAdmin)->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function non_managers_cannot_update_groups()
    {
        $this->storeGroup($this->silverUser);

        $this->tryUpdateGroup($this->goldUser)->assertForbidden();
    }

   /** @test */
    public function non_managers_cannot_delete_groups()
    {
        $this->storeGroup($this->contactManager);

        $this->deleteGroup($this->goldUser)->assertForbidden();
    }

    /***********************************-----------
     *
     *              Refactors                      =======================
     *
     * /**********************************/


    private function indexGroup($user): TestResponse
    {
        $url = route('network.group.index');
        return $this->json('get', $url, ['token' => $this->getToken($user)]);
    }

    private function tryStoreGroup($user): TestResponse
    {
        $url = route('network.group.store');
        return $this->json('post', $url, ['token' => $this->getToken($user)]);
    }

    private function tryUpdateGroup($user): TestResponse
    {
        $url = route('network.group.update', [1]);
        return $this->json('put', $url, ['token' => $this->getToken($user)]);
    }

    private function deleteGroup($user): TestResponse
    {
        $url = route('network.group.destroy', [1]);
        return $this->json('delete', $url, ['token' => $this->getToken($user)]);
    }

}
