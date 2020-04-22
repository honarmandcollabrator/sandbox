<?php

namespace Tests\Feature\FunctionalityTests;

use App\Models\Network\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class NetworkJoinSecurityTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createUsers();
    }


    /***********************************-----------
     *
     *              JWT           =======================
     *
     * /**********************************/

    /** @test */
    public function jwt_is_protecting_group_routes()
    {
        $this->storeGroup($this->silverUser);

        $this->request(null)->assertUnauthorized();
        $this->accept(null, 1)->assertUnauthorized();
        $this->remove(null, 1)->assertUnauthorized();
        $this->getGroupies(null)->assertUnauthorized();
        $this->getRequests(null)->assertUnauthorized();
    }

    /***********************************-----------
     *
     *              In controller Securities   =======================
     *
     * /**********************************/

    /** @test */
    public function cannot_request_to_your_own_group()
    {
        $this->storeGroup($this->goldUser);

        $this->request($this->goldUser)->assertForbidden();
    }


    /** @test */
    public function cannot_request_if_already_requested()
    {
        $this->storeGroup($this->silverUser);
        $this->normalUser->groups()->attach([Group::first()->id => ['status' => 'pending']]);

        $this->request($this->normalUser)->assertForbidden();
    }

    /** @test */
    public function cannot_request_if_already_joined()
    {
        $this->storeGroup($this->silverUser);
        $this->normalUser->groups()->attach([Group::first()->id => ['status' => 'approved']]);

        $this->request($this->normalUser)->assertForbidden();
    }

    /** @test */
    public function cannot_accept_someone_if_there_is_no_join_request()
    {
        $this->storeGroup($this->silverUser);

        $this->accept($this->silverUser, 1)->assertForbidden();
    }

    /** @test */
    public function cannot_remove_someone_if_there_is_no_join_or_request()
    {
        $this->storeGroup($this->silverUser);

        $this->remove($this->silverUser, 1)->assertForbidden();
    }

    /** @test */
    public function cannot_remove_yourself_from_group()
    {
        $this->storeGroup($this->goldUser);

        $this->remove($this->goldUser, $this->goldUser->id)->assertForbidden();
    }

    /***********************************-----------
     *
     *              Policies           =======================
     *
     * /**********************************/


    /** @test */
    public function non_owner_0_access_to_joins()
    {
        $this->storeGroup($this->silverUser);
        $this->normalUser->groups()->attach([Group::first()->id => ['status' => 'pending']]);

        $this->getGroupies($this->goldUser)->assertForbidden();
        $this->getRequests($this->goldUser)->assertForbidden();
        $this->accept($this->goldUser ,1)->assertForbidden();
        $this->remove($this->goldUser, 1)->assertForbidden();
    }

    /** @test */
    public function owner_full_access_to_joins()
    {
        $this->storeGroup($this->silverUser);
        $this->normalUser->groups()->attach([Group::first()->id => ['status' => 'pending']]);

        $this->getGroupies($this->silverUser)->assertOk();
        $this->getRequests($this->silverUser)->assertOk();
        $this->accept($this->silverUser ,1)->assertStatus(Response::HTTP_ACCEPTED);
        $this->remove($this->silverUser, 1)->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function non_owner_but_super_admin_still_can_only_see_groupies()
    {
        $this->storeGroup($this->silverUser);
        $this->normalUser->groups()->attach([Group::first()->id => ['status' => 'pending']]);

        $this->getGroupies($this->superAdmin)->assertOk();
        $this->getRequests($this->superAdmin)->assertOk();
        $this->accept($this->superAdmin ,1)->assertForbidden();
        $this->remove($this->superAdmin, 1)->assertForbidden();
    }


    /** @test */
    public function non_owner_but_admin_still_can_only_see_groupies()
    {
        $this->storeGroup($this->silverUser);
        $this->normalUser->groups()->attach([Group::first()->id => ['status' => 'pending']]);

        $this->getGroupies($this->admin)->assertOk();
        $this->getRequests($this->admin)->assertOk();
        $this->accept($this->admin ,1)->assertForbidden();
        $this->remove($this->admin, 1)->assertForbidden();
    }


    /** @test */
    public function non_owner_but_network_manager_still_can_only_see_groupies()
    {
        $this->storeGroup($this->silverUser);
        $this->normalUser->groups()->attach([Group::first()->id => ['status' => 'pending']]);

        $this->getGroupies($this->networkManager)->assertOk();
        $this->getRequests($this->networkManager)->assertOk();
        $this->accept($this->networkManager ,1)->assertForbidden();
        $this->remove($this->networkManager, 1)->assertForbidden();
    }



    /***********************************-----------
     *
     *              Refactors           =======================
     *
     * /**********************************/

    private function request($user)
    {
        $url = route('join.request', ['group' => 1]);
        return $this->json('put', $url, ['token' => $this->getToken($user)]);
    }


    private function accept($user, $id)
    {
        $url = route('join.accept', ['group' => 1, $id]);
        return $this->json('put', $url, ['token' => $this->getToken($user)]);
    }


    private function remove($user, $id)
    {
        $url = route('join.remove', ['group' => 1, $id]);
        return $this->json('delete', $url, ['token' => $this->getToken($user),]);

    }

    private function getGroupies($user)
    {
        $url = route('join.groupies', [1]);
        return $this->json('get', $url, ['token' => $this->getToken($user)]);
    }


    private function getRequests($user)
    {
        $url = route('join.requests', [1]);
        return $this->json('get', $url, ['token' => $this->getToken($user)]);
    }



}
