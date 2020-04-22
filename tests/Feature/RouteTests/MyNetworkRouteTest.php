<?php

namespace Tests\Feature\MyNetwork;

use App\Models\Network\Group;
use App\Models\Network\Hashtag;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class MyNetworkRouteTest extends TestCase
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
       | MyNetwork Routes
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
    public function my_network_users()
    {
        $this->goldUser->request_senders()->attach([$this->normalUser->id => ['status' => 'approved']]);
        $this->goldUser->request_senders()->attach([$this->silverUser->id => ['status' => 'pending']]);
        $this->goldUser->request_recipients()->attach([$this->networkManager->id => ['status' => 'pending']]);

        $url = route('my.network.users');
        $response = $this->json('get', $url, ['token' => $this->getToken($this->goldUser)]);

        /*===== We should get all users except myself and 3 other guys, one already friend, one pending request sent and one pending request received  =====*/
        $response->assertOk();
        $response->assertJsonCount(User::all()->count() - 4, 'data');
        $response->assertDontSee($this->goldUser->username);
        $response->assertDontSee($this->normalUser->username);
        $response->assertDontSee($this->silverUser->username);
        $response->assertDontSee($this->networkManager->username);

        $response->assertSee($this->superAdmin->username);
        $response->assertSee($this->admin->username);
        $response->assertSee($this->jobManager->username);
        $response->assertSee($this->contactManager->username);
        $response->assertStatus(Response::HTTP_OK);
    }


    /** #2
     * @test
     */
    public function my_network_groups()
    {
        $group1 = factory(Group::class)->create(['admin_id' => $this->normalUser->id]);
        $this->normalUser->groups()->attach([1 => ['status' => 'approved']]);
        $group2 = factory(Group::class)->create(['admin_id' => $this->normalUser->id]);
        $this->normalUser->groups()->attach([2 => ['status' => 'approved']]);

        $group3 = factory(Group::class)->create(['admin_id' => $this->silverUser->id]);
        $this->normalUser->groups()->attach([3 => ['status' => 'approved']]);
        $group4 = factory(Group::class)->create(['admin_id' => $this->goldUser->id]); //3
        $this->normalUser->groups()->attach([4 => ['status' => 'pending']]);

        $group5 = factory(Group::class)->create(['admin_id' => $this->networkManager->id]); //6
        $group6 = factory(Group::class)->create(['admin_id' => $this->jobManager->id]); //7
        $group7 = factory(Group::class)->create(['admin_id' => $this->superAdmin->id]); //4

        $this->assertCount(7, Group::all());
        $this->assertCount(4, $this->normalUser->groups);

        $this->withoutExceptionHandling();
        $url = route('my.network.groups');
        $response = $this->json('get', $url, ['token' => $this->getToken($this->normalUser)]);

        /*===== We have 7 groups, normal user owns 2 of them, and already joined in one, and requested to another one. We dont
        Want any of these groups except the one is already requested. so it is going to be 7-3=4 =====*/
        $response->assertOk();
        $response->assertJsonCount(4, 'data');
        $response->assertDontSee(Group::find(1)->first()->name);
        $response->assertDontSee(Group::find(2)->first()->name);
        $response->assertDontSee($group3->name);
        $response->assertDontSee($group4->name);

        $response->assertSee('"admin_id":3');
        $response->assertSee('"admin_id":6');
        $response->assertSee('"admin_id":7');
        $response->assertSee('"admin_id":4');
    }


    /** #3
     * @test
     */
    public function my_network_joined_groups()
    {
        /*===== two owned groups =====*/
        $group1 = factory(Group::class)->create(['admin_id' => $this->normalUser->id]);
        $this->normalUser->groups()->attach([1 => ['status' => 'approved']]);
        $group2 = factory(Group::class)->create(['admin_id' => $this->normalUser->id]);
        $this->normalUser->groups()->attach([2 => ['status' => 'approved']]);

        /*===== one joined group =====*/
        $group3 = factory(Group::class)->create(['admin_id' => $this->silverUser->id]);
        $this->normalUser->groups()->attach([3 => ['status' => 'approved']]);

        /*===== one requested but not joined group =====*/
        $group4 = factory(Group::class)->create(['admin_id' => $this->goldUser->id]); //3
        $this->normalUser->groups()->attach([4 => ['status' => 'pending']]);

        /*===== three not related groups =====*/
        $group5 = factory(Group::class)->create(['admin_id' => $this->networkManager->id]); //6
        $group6 = factory(Group::class)->create(['admin_id' => $this->jobManager->id]); //7
        $group7 = factory(Group::class)->create(['admin_id' => $this->superAdmin->id]); //4

        $this->assertCount(7, Group::all());
        $this->assertCount(4, $this->normalUser->groups);

        $this->withoutExceptionHandling();
        $url = route('my.network.joined.groups');
        $response = $this->json('get', $url, ['token' => $this->getToken($this->normalUser)]);

        /*===== We have 7 groups, normal user owns 2 of them, and already joined in one, and requested to another one.
        So it is going to be 7-4=3 =====*/
        $response->assertOk();
        $response->assertJsonCount(3, 'data');
        $response->assertSee('"admin_id":1');
        $response->assertSee('"admin_id":1');
        $response->assertSee($group3->username);

        $response->assertDontSee($group4->name);
        $response->assertDontSee('"admin_id":3');
        $response->assertDontSee('"admin_id":6');
        $response->assertDontSee('"admin_id":7');
        $response->assertDontSee('"admin_id":4');
    }

    /** #4
     * @test
     */
    public function my_network_hashtags()
    {
        factory(Hashtag::class, 6)->create();
        $this->normalUser->hashtags()->attach([1, 2, 3]);
        $this->assertCount(3, $this->normalUser->hashtags);


        $url = route('my.network.hashtags');
        $response = $this->json('get', $url, ['token' => $this->getToken($this->normalUser)]);

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }


    /** @test #5 */
    public function my_network_my_hashtags()
    {
        factory(Hashtag::class, 6)->create();
        $this->normalUser->hashtags()->attach([1, 2]);
        $this->assertCount(2, $this->normalUser->hashtags);

        $url = route('my.network.my.hashtags');
        $response = $this->json('get', $url, ['token' => $this->getToken($this->normalUser)]);

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }
}
