<?php

namespace Tests\Feature\FunctionalityTests;

use App\Models\Network\Hashtag;
use App\Models\Network\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;

class NetworkHashtagFunctionalityTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUsers();
    }

    /** @test */
    public function hashtags_are_automatically_saved_to_database_after()
    {
        $url = route('network.post.store', [1]);
        $response = $this->json('post', $url, [
            'token' => $this->getToken($this->normalUser),
            'title' => 'new title',
            'description' => 'new description #first #دوم #third #چهارم',
            'media' => UploadedFile::fake()->image('image.png')->size(1000),
        ]);

        /** 2
         * Assert
         */
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertCount(1, Post::all());

        /*===== Must have 4 hashtags now =====*/
        $this->assertCount(4, Hashtag::all());
    }


    /** @test */
    public function can_get_posts_belong_to_a_hashtag()
    {
        factory(Post::class, 5)->state('hashtag')->create(['user_id' => $this->goldUser->id, 'timeline_id' => $this->goldUser->timeline->id]);

        $url = route('network.post.index', ['timeline' => $this->normalUser->timeline->id]);
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->normalUser),
            'hashtag' => Hashtag::first()->name,
        ]);
        $response->assertOk();
        $response->assertJsonCount(1, 'data');

    }

}
