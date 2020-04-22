<?php

namespace Tests\Feature\User;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class FriendshipRouteTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->createUsers();
    }

    /*
       |--------------------------------------------------------------------------
       | Friendship Routes
       |--------------------------------------------------------------------------
       |
       | 6 Routes
       |
       |
       |
       */

    /** #1
     * @test
     */
    public function friendship_request()
    {
        $url = route('friendship.request', [1]);
        $response = $this->json('put', $url, [
            'token' => $this->getToken($this->silverUser),
        ]);

        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertCount(1, $this->normalUser->request_senders);

        $this->assertEquals('pending', $this->normalUser->request_senders()->first()->pivot->status);
    }

    /** #2
     * @test
     */
    public function friendship_accept()
    {
        $url = route('friendship.request', ['user' => $this->normalUser]);
        $this->json('put', $url, [
            'token' => $this->getToken($this->silverUser),
        ]);

        $url = route('friendship.accept', ['user' => $this->silverUser]);
        $response = $this->json('put', $url, [
            'token' => $this->getToken($this->normalUser),
        ]);

        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertEquals('approved', $this->normalUser->request_senders()->first()->pivot->status);

    }

    /** #3
     * @test
     */
    public function friendship_deny()
    {
        $url = route('friendship.request', ['user' => $this->normalUser]);
        $this->json('put', $url, [
            'token' => $this->getToken($this->silverUser),
        ]);

        $url = route('friendship.deny', ['user' => $this->silverUser]);
        $response = $this->json('put', $url, [
            'token' => $this->getToken($this->normalUser),
        ]);

        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertCount(0, $this->normalUser->request_senders);

    }

    /** #4
     * @test
     */
    public function friendship_unfriend()
    {
        $url = route('friendship.request', ['user' => $this->normalUser]);
        $this->json('put', $url, [
            'token' => $this->getToken($this->silverUser),
        ]);

        $url = route('friendship.accept', ['user' => $this->silverUser]);
        $this->json('put', $url, [
            'token' => $this->getToken($this->normalUser),
        ]);

        $url = route('friendship.unfriend', ['user' => $this->silverUser]);
        $response = $this->json('put', $url, [
            'token' => $this->getToken($this->normalUser),
        ]);

        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertCount(0, $this->normalUser->request_senders);
    }

    /** #5
     * @test
     */
    public function friendship_my_friends()
    {
        /** 1
         * Creating Two friends for normal user
         */

        $this->normalUser->request_senders()->attach([
            $this->silverUser->id => [
                'status' => 'approved'
            ]
        ]);
        $this->normalUser->request_senders()->attach([
            $this->goldUser->id => [
                'status' => 'approved'
            ]
        ]);
        $this->assertCount(2, $this->normalUser->request_senders);


        /** 2
         * Getting friends
         */
        $url = route('friendship.my.friends');
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->normalUser),
        ]);


        /** 3
         * Assert
         */
        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    /** #6
     * @test
     */
    public function friendship_my_requests()
    {
        /** 1
         * Creating Two friends for normal user
         */

        $this->normalUser->request_senders()->attach([
            $this->silverUser->id => [
                'status' => 'pending'
            ]
        ]);
        $this->normalUser->request_senders()->attach([
            $this->goldUser->id => [
                'status' => 'pending'
            ]
        ]);
        $this->assertCount(2, $this->normalUser->request_senders);


        /** 2
         * Getting pending requests
         */
        $url = route('friendship.my.requests');
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->normalUser),
        ]);


        /** 3
         * Assert
         */
        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }


    /***********************************-----------
     *
     *              Refactors           =======================
     *
     * /**********************************/


}
