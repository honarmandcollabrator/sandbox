<?php

namespace Tests\Feature\FunctionalityTests;

use App\Models\Network\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class NetworkJoinRouteTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createUsers();

    }

    /*
       |--------------------------------------------------------------------------
       | Join
       |--------------------------------------------------------------------------
       |
       | 5 Routes
       |
       |
       |
       */

    /** #1
     * @test
     */
    public function join_request()
    {
        $this->withoutExceptionHandling();
        factory(Group::class)->create(['admin_id' => $this->silverUser->id]);

        $url = route('join.request', ['group' => 1]);
        $response = $this->json('put', $url, [
            'token' => $this->getToken($this->normalUser),
        ]);


        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertCount(1, $this->normalUser->groups);
        $this->assertEquals('pending', $this->normalUser->groups->first()->pivot->status);
    }


    /** #2
     * @test
     */
    public function join_accept()
    {
        factory(Group::class)->create(['admin_id' => $this->silverUser->id]);
        $this->normalUser->groups()->attach([Group::first()->id => ['status' => 'pending']]);

        $url = route('join.accept', ['group' => 1, 'user' => 1]);
        $response = $this->json('put', $url, [
            'token' => $this->getToken($this->silverUser),
        ]);


        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertCount(1, $this->normalUser->groups);
        $this->assertEquals('approved', $this->normalUser->groups->first()->pivot->status);
    }


    /** #3
     * @test
     */
    public function join_remove()
    {
        $this->withoutExceptionHandling();
        factory(Group::class)->create(['admin_id' => $this->silverUser->id]);
        $this->normalUser->groups()->attach([Group::first()->id => ['status' => 'pending']]);

        $url = route('join.remove', ['group' => 1, 'user' => 1]);
        $response = $this->json('delete', $url, [
            'token' => $this->getToken($this->silverUser),
        ]);


        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertCount(0, $this->normalUser->groups);
    }


    /** #4
     * @test
     */
    public function join_groupies()
    {
        $this->withoutExceptionHandling();
        $group = factory(Group::class)->create(['admin_id' => $this->silverUser->id]);

        $this->goldUser->groups()->attach([$group->id => ['status' => 'approved']]);
        $this->normalUser->groups()->attach([$group->id => ['status' => 'approved']]);
        $this->assertCount(2, $group->users);


        $url = route('join.groupies', ['group' => 1]);
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->silverUser),
        ]);


        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    /** #5
     * @test
     */
    public function join_requests()
    {
        $this->withoutExceptionHandling();
        $group = factory(Group::class)->create(['admin_id' => $this->silverUser->id]);

        $this->goldUser->groups()->attach([$group->id => ['status' => 'pending']]);
        $this->normalUser->groups()->attach([$group->id => ['status' => 'pending']]);
        $this->assertCount(2, $group->users);


        $url = route('join.requests', ['group' => 1]);
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->silverUser),
        ]);


        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }


}
