<?php

namespace Tests\Feature\User;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class FriendshipSecurityTest extends TestCase
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
     *              Middleware           =======================
     *
     * /**********************************/

    /** @test */
    public function jwt_is_protecting_friendship()
    {
        $this->request(null, 1)->assertUnauthorized();
        $this->accept(null, 1)->assertUnauthorized();
        $this->deny(null, 1)->assertUnauthorized();
        $this->unfriend(null, 1)->assertUnauthorized();
        $this->getMyFriends(null)->assertUnauthorized();
        $this->getMyRequests(null)->assertUnauthorized();
    }


    /***********************************-----------
     *
     *              Policies           =======================
     *
     * /**********************************/

    /** @test */
    public function cannot_request_to_someone_already_requested()
    {
        $this->withoutExceptionHandling();
        $this->normalUser->request_senders()->attach([
            $this->silverUser->id => [
                'status' => 'pending'
            ]
        ]);

        $this->request($this->normalUser, 2)->assertForbidden();
    }

    /** @test */
    public function cannot_request_to_someone_already_is_friend()
    {
        $this->normalUser->request_senders()->attach([
            $this->silverUser->id => [
                'status' => 'approved'
            ]
        ]);

        $this->request($this->normalUser, 2)->assertForbidden();
    }

    /** @test */
    public function cannot_accept_someone_if_there_is_no_request()
    {
        $this->accept($this->normalUser, 2)->assertForbidden();
    }

    /** @test */
    public function cannot_deny_someone_if_there_is_no_request()
    {
        $this->deny($this->silverUser, 1)->assertForbidden();
    }


    /***********************************-----------
     *
     *              Refactors           =======================
     *
     * /**********************************/

    private function request($user, $id)
    {
        $url = route('friendship.request', [$id]);
        return $this->json('put', $url, ['token' => $this->getToken($user)]);
    }


    private function accept($user, $id)
    {
        $url = route('friendship.accept', [$id]);
        return $this->json('put', $url, ['token' => $this->getToken($user)]);
    }


    private function deny($user, $id)
    {
        $url = route('friendship.deny', [$id]);
        return $this->json('put', $url, ['token' => $this->getToken($user),]);

    }


    private function unfriend($user, $id)
    {
        $url = route('friendship.unfriend', [$id]);
        return $this->json('put', $url, ['token' => $this->getToken($user)]);
    }


    private function getMyFriends($user)
    {
        $url = route('friendship.my.friends');
        return $this->json('get', $url, ['token' => $this->getToken($user)]);
    }


    private function getMyRequests($user)
    {
        $url = route('friendship.my.requests');
        return $this->json('get', $url, ['token' => $this->getToken($user)]);
    }


}
