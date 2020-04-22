<?php

namespace Tests\Feature\FunctionalityTests;

use App\Models\Network\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class NetworkLikeFunctionalityTest extends TestCase
{

    /***********************************-----------
     *
     *              Test setUp                      =======================
     *
     * /**********************************/


    use RefreshDatabase;
    use WithFaker;

    private $post;


    public function setUp(): void
    {
        parent::setUp();

        $this->createUsers();

        $this->post = factory(Post::class)->create(['user_id' => $this->normalUser->id, 'timeline_id' => $this->normalUser->timeline_id]);
    }


    /***********************************-----------
     *
     *              Likes Management                      =======================
     *
     * /**********************************/

    /** @test */
    public function guests_cannot_like_a_post()
    {
        $url = route('network.post.like', [1, 1]);
        $response = $this->json('put', $url);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function user_can_like_a_post()
    {
        $url = route('network.post.like', [1, 1]);
        $response = $this->json('put', $url, [
            'token' => $this->getToken($this->normalUser)
        ]);
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertCount(1, Post::first()->likes()->get());
    }

    /** @test */
    public function user_can_toggle_like_a_post_to_remove_its_like()
    {
        $url = route('network.post.like', [1, 1]);

        $this->json('put', $url, [
            'token' => $this->getToken($this->normalUser)
        ]);
        $response = $this->json('put', $url, [
            'token' => $this->getToken($this->normalUser)
        ]);

        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertCount(0, Post::first()->likes()->get());

    }

    /** @test */
    public function a_post_likes_count_is_calculated_correctly()
    {
        $url = route('network.post.like', [1, 1]);

        $this->json('put', $url, [
            'token' => $this->getToken($this->normalUser)
        ]);
        $this->json('put', $url, [
            'token' => $this->getToken($this->silverUser)
        ]);

        $this->assertCount(2, Post::first()->likes()->get());
    }




    /***********************************-----------
     *
     *              Share Management                      =======================
     *
     * /**********************************/


    /** @test */
    public function guests_cannot_share_a_post()
    {
        $url = route('network.post.share', [1, 1]);
        $response = $this->json('put', $url);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function user_can_share_a_post()
    {
        $url = route('network.post.share', [1, 1]);
        $response = $this->json('put', $url, [
            'token' => $this->getToken($this->silverUser)
        ]);
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertCount(1, Post::first()->shares()->get());
    }

    /** @test */
    public function user_cannot_share_his_own_post()
    {
        $url = route('network.post.share', [1, 1]);
        $response = $this->json('put', $url, [
            'token' => $this->getToken($this->normalUser)
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $this->assertCount(0, Post::first()->shares()->get());
    }


    /** @test */
    public function user_can_toggle_share_a_post_to_remove_its_share()
    {
        $url = route('network.post.share', [1, 1]);

        $this->json('put', $url, [
            'token' => $this->getToken($this->silverUser)
        ]);
        $response = $this->json('put', $url, [
            'token' => $this->getToken($this->silverUser)
        ]);

        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertCount(0, Post::first()->shares);
    }

    /** @test */
    public function post_shares_is_calculated_correctly()
    {
        $url = route('network.post.share', [1, 1]);

        $this->json('put', $url, [
            'token' => $this->getToken($this->silverUser)
        ]);
        $this->json('put', $url, [
            'token' => $this->getToken($this->goldUser)
        ]);


        $this->assertCount(2, Post::first()->shares);
    }


}
