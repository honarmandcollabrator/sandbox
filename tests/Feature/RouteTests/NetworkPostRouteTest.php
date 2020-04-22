<?php

namespace Tests\Feature\FunctionalityTests;

use App\Http\Resources\Network\CommentResource;
use App\Models\Network\Hashtag;
use App\Models\Network\Post;
use App\Models\Network\Timeline;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class NetworkPostRouteTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $dataWithPicture;
    private $dataWithVideo;

    public function setUp(): void
    {
        parent::setUp();

        $this->createUsers();

        Storage::fake('public');
    }


    /*
       |--------------------------------------------------------------------------
       | Post
       |--------------------------------------------------------------------------
       |
       | 8 Routes
       |
       |
       |
       */

    /** @test
     * #1
     */
    public function network_post_store()
    {
        $this->withoutExceptionHandling();
        /** 1
         * Create a Post
         */
        $response = $this->createPost();

        /** 2
         * Assert
         */
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertCount(1, Post::all());
        $this->postAssertJsonFragments($response);
        Storage::disk('public')->assertExists(Post::first()->media);
    }

//    /** @test
//     * #2
//     */
//    public function can_get_a_post()
//    {
//        /** 1
//         * Create a Post
//         */
//        $this->createPost();
//
//
//        /** 2
//         * Get the Post
//         */
//        $url = route('network.post.show', [1, 1]);
//        $response = $this->json('get', $url, [
//            'token' => $this->getToken($this->normalUser),
//        ]);
//
//        /** 3
//         * Assert
//         */
//        $response->assertStatus(Response::HTTP_OK);
//        $this->postAssertJsonFragments($response);
//    }


    /** @test
     * #2
     */
    public function network_post_update()
    {
        /** 1
         * Create a Post
         */
        $this->createPost();
        $oldFile = Post::first()->media;


        /** 2
         * Update the Post
         */
        $response = $this->updatePost();
        $newFile = Post::first()->media;


        /** 3
         * Assert
         */
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->updatePostAssertJsonFragments($response);
        Storage::disk('public')->assertExists($newFile);
        Storage::disk('public')->assertMissing($oldFile);
    }

    /** @test
     * #3
     */
    public function network_post_destroy()
    {
        /** 1
         * Create a Post
         */
        $this->createPost();
        $file = Post::first()->media;

        /** 2
         * Delete the Post
         */
        $url = route('network.post.destroy', [1, 1]);
        $response = $this->json('delete', $url, [
            'token' => $this->getToken($this->normalUser),
        ]);

        /** 3
         * Assert
         */
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertCount(0, Post::all());
        Storage::disk('public')->assertMissing($file);
    }

    /** @test
     * #4
     */
    public function network_post_index()
    {
        $this->withoutExceptionHandling();
        /** 1
         * Create 10 Posts
         */
        factory(Post::class, 4)->create([
            'user_id' => $this->normalUser,
            'timeline_id' => $this->normalUser->timeline_id
        ]);


        /** 2
         * Get index Post
         */
        $url = route('network.post.index', [1]);
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->normalUser),
        ]);


        /** 3
         * Assert
         */
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(4, 'data');
    }

    /** #5
     * @test
     */
    public function network_post_like()
    {
        /** 1
         * Create a Post
         */
        $this->createPost();

        /** 2
         * Like the Post
         */
        $url = route('network.post.like', [1, 1]);
        $response = $this->json('put', $url, [
            'token' => $this->getToken($this->silverUser),
        ]);

        /** 3
         * Assert
         */
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertCount(1, Post::first()->likes);
    }


    /** #6
     * @test
     */
    public function network_post_share()
    {
        /** 1
         * Create a Post
         */
        $this->createPost();

        /** 2
         * Like the Post
         */
        $url = route('network.post.share', [1, 1]);
        $response = $this->json('put', $url, [
            'token' => $this->getToken($this->silverUser),
        ]);

        /** 3
         * Assert
         */
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertCount(1, Post::first()->shares);
    }

    /** #7
     * @test
     */
    public function network_follow_hashtag()
    {
        $url = route('network.post.store', [1]);
        $this->json('post', $url, [
            'token' => $this->getToken($this->normalUser),
            'title' => 'new title',
            'description' => 'new description #followme #two #three',
            'media' => UploadedFile::fake()->image('image.png')->size(1000),
        ]);
        $this->assertCount(1, Post::all());
        $this->assertCount(3, Hashtag::all());

        $this->withoutExceptionHandling();

        /*===== Follow a hashtag =====*/
        $url = route('network.post.follow.hashtag', [1]);
        $response = $this->json('put', $url, ['token' => $this->getToken($this->goldUser)]);
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertCount(1, $this->goldUser->hashtags);

        /*===== Follow a another hashtag =====*/
        $url = route('network.post.follow.hashtag', [2]);
        $this->json('put', $url, ['token' => $this->getToken($this->goldUser)]);
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertCount(2, $this->goldUser->fresh()->hashtags);

        /*===== toggle follow =====*/
        $url = route('network.post.follow.hashtag', [1]);
        $response = $this->json('put', $url, ['token' => $this->getToken($this->goldUser)]);
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertCount(1, $this->goldUser->fresh()->hashtags);

        /*===== toggle follow =====*/
        $url = route('network.post.follow.hashtag', [2]);
        $response = $this->json('put', $url, ['token' => $this->getToken($this->goldUser)]);
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertCount(0, $this->goldUser->fresh()->hashtags);

    }


    /** @test 8 */
    public function network_post_manage_index()
    {
        $url = route('network.post.manage.index');
        $response = $this->json('get', $url, ['token' => $this->getToken($this->superAdmin)]);

        $response->assertOk();
        $response->assertJsonStructure(['data', 'links', 'meta']);
    }

    /***********************************-----------
     *
     *              Additional Tests                      =======================
     *
     * /**********************************/

    /** @test
     * #
     */
    public function network_post_index_with_hashtag()
    {
        /** 1
         * Create 2 users and each 2 posts all with #hello
         */
        factory(User::class, 2)->create()->each(function ($user) {
            $user->posts()->saveMany(factory(Post::class, 2)->make([
                'timeline_id' => $user->timeline_id,
                'description' => 'hello #hello',
            ]));
        });

        /** 2
         * Get index Post with hashtag
         */
        $url = route('network.post.index', [1]);
        $response = $this->json('get', $url . '?hashtag=hello', [
            'token' => $this->getToken($this->normalUser),
        ]);


        /** 3
         * Assert
         */
        $response->assertJsonCount(4, 'data');
    }


    /** @test */
    public function media_video_file_exists_after_saving()
    {
        $this->withoutExceptionHandling();
        $url = route('network.post.store', [3]);
        $this->post($url, [
            'token' => $this->getToken($this->goldUser),
            'title' => 'my title',
            'description' => 'my description',
            'media' => UploadedFile::fake()->create('video.mp4')->size(100),
        ]);

        Storage::disk('public')->assertExists(Post::first()->media);
    }


    /***********************************-----------
     *
     *              Refactors           =======================
     *
     * /**********************************/

    /**
     * @return TestResponse
     */
    private function createPost(): TestResponse
    {
        $url = route('network.post.store', [1]);
        return $this->json('post', $url, [
            'token' => $this->getToken($this->normalUser),
            'title' => 'new title',
            'description' => 'new description',
            'media' => UploadedFile::fake()->image('image.png')->size(1000),
        ]);
    }

    /**
     * @return TestResponse
     */
    private function updatePost(): TestResponse
    {
        $url = route('network.post.update', [1, 1]);
        return $this->json('put', $url, [
            'token' => $this->getToken($this->normalUser),
            'title' => 'updated title',
            'description' => 'updated description',
            'media' => UploadedFile::fake()->image('image.png')->size(100)
        ]);
    }


    /**
     * @param TestResponse $response
     */
    private function postAssertJsonFragments(TestResponse $response): void
    {
        $response->assertJsonFragment(['title' => 'new title']);
        $response->assertJsonFragment(['description' => 'new description']);
        $response->assertJsonFragment(['id' => $this->normalUser->id]);
        $response->assertJsonFragment(['name' => $this->normalUser->name]);
        $response->assertJsonFragment(['username' => $this->normalUser->username]);
        $response->assertJsonFragment(['avatar' => null]);
        $response->assertJsonFragment(['path' => Storage::disk('public')->url(Post::first()->media)]);
        $response->assertJsonFragment(['type' => 'image']);
        $response->assertJsonFragment(['comments' => []]);
        $response->assertJsonFragment(['id' => Post::first()->id]);
        $response->assertJsonFragment(['timeline_id' => Post::first()->timeline->id]);
        $response->assertJsonFragment(['share_count' => 0]);
        $response->assertJsonFragment(['like_count' => 0]);
        $response->assertJsonFragment(['comment_count' => 0]);
        $response->assertJsonFragment(['is_mine' => true]);
        $response->assertJsonFragment(['is_liked_by_me' => false]);
        $response->assertJsonFragment(['is_shared_by_me' => false]);
        $response->assertJsonFragment(['created_at' => Post::first()->created_at->diffForHumans()]);
        $response->assertJsonFragment(['updated_at' => Post::first()->updated_at->diffForHumans()]);
    }

    /**
     * @param TestResponse $response
     */
    private function updatePostAssertJsonFragments(TestResponse $response): void
    {
        $response->assertJsonFragment([
            'title' => 'updated title',
            'description' => 'updated description',
            'path' => Storage::disk('public')->url(Post::first()->media),
            'updated_at' => Post::first()->updated_at->diffForHumans()
        ]);
    }


}
