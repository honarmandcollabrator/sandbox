<?php

namespace Tests\Feature\FunctionalityTests;

use App\Models\Network\Comment;
use App\Models\Network\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class NetworkCommentRouteTest extends TestCase
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

    /*
       |--------------------------------------------------------------------------
       | Comment Routes
       |--------------------------------------------------------------------------
       |
       | 4 Routes
       |
       |
       |
       */

    /** @test 1*/
    public function network_comment_index()
    {
        $url = route('network.comment.index', ['timeline' => 1, 'post' => 1]);
        $response = $this->json('get', $url, ['token' => $this->getToken($this->normalUser)]);

        $response->assertOk();
        $response->assertJsonStructure([
            'data', 'links', 'meta'
        ]);
    }

    /** @test 2*/
    public function network_comment_store()
    {
        $url = route('network.comment.store', ['timeline' => 1, 'post' => 1]);
        $response = $this->json('post', $url, [
            'token' => $this->getToken($this->normalUser),
            'description' => 'try to store',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['description' => 'try to store']);

        $response->assertJsonFragment(['id' => $this->normalUser->id]);
        $response->assertJsonFragment(['name' => $this->normalUser->name]);
        $response->assertJsonFragment(['username' => $this->normalUser->username]);
        $response->assertJsonFragment(['avatar' => null]);

        $response->assertJsonFragment(['id' => Comment::first()->id]);
        $response->assertJsonFragment(['created_at' => Comment::first()->created_at->diffForHumans()]);
        $response->assertJsonFragment(['updated_at' => Comment::first()->updated_at->diffForHumans()]);
        $response->assertJsonFragment(['is_mine' => true]);
    }

    /** @test 3*/
    public function network_comment_update()
    {
        $url = route('network.comment.store', ['timeline' => 1, 'post' => 1]);
        $this->json('post', $url, [
            'token' => $this->getToken($this->normalUser),
            'description' => 'try to store',
        ]);

        $url = route('network.comment.update', ['timeline' => 1, 'post' => 1, 'comment' => 1]);
        $response = $this->put($url, [
            'token' => $this->getToken($this->normalUser),
            'description' => 'try to update',
        ]);

        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJsonFragment(['description' => 'try to update']);
    }

    /** @test 4*/
    public function network_comment_destroy()
    {
        $url = route('network.comment.store', ['timeline' => 1, 'post' => 1]);
        $this->json('post', $url, [
            'token' => $this->getToken($this->normalUser),
            'description' => 'try to store',
        ]);

        $url = route('network.comment.destroy', ['timeline' => 1, 'post' => 1, 'comment' => 1]);
        $response = $this->delete($url, [
            'token' => $this->getToken($this->normalUser),
        ]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertCount(0, Comment::all());
    }


}
