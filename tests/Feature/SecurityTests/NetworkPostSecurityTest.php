<?php

namespace Tests\Unit\Network;

use App\Models\Network\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class NetworkPostSecurityTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $picture;
    private $video;

    private $roleError;


    public function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->roleError = config('app.role_error');

        $this->createUsers();

        $this->picture = [
            'media' => UploadedFile::fake()->image('picture.jpeg')->size(100),
        ];

        $this->video = [
            'media' => UploadedFile::fake()->create('video.mp4')->size(100),
        ];

    }

    /***********************************-----------
     *
     *              Middleware           =======================
     *
     * /**********************************/

    /** @test */
    public function post_jwt_access()
    {
        $this->factoryPost($this->superAdmin, 'image');

        /** Protected */
        $this->tryStorePost(null, 'picture')->assertUnauthorized();
        $this->tryUpdatePost(null, 'picture')->assertUnauthorized();
        $this->indexPosts(null)->assertUnauthorized();
        $this->toggleLike(null)->assertUnauthorized();
        $this->toggleShare(null)->assertUnauthorized();
        $this->deletePost(null)->assertUnauthorized();
    }



    /** @test */
    public function correct_roles_can_use_network_post_manage_index()
    {
        $this->postManageIndex($this->normalUser)->assertForbidden();
        $this->postManageIndex($this->goldUser)->assertForbidden();
        $this->postManageIndex($this->silverUser)->assertForbidden();
        $this->postManageIndex($this->contactManager)->assertForbidden();
        $this->postManageIndex($this->jobManager)->assertForbidden();


        $this->postManageIndex($this->networkManager)->assertOk();
        $this->postManageIndex($this->superAdmin)->assertOk();
        $this->postManageIndex($this->admin)->assertOk();

    }


    /***********************************-----------
     *
     *              Policies           =======================
     *
     * /**********************************/

    /** @test */
    public function cannot_create_more_than_15_posts_per_day()
    {
        factory(Post::class, 15)->create(['user_id' => $this->normalUser->id, 'timeline_id' => $this->normalUser->timeline->id]);

        $response = $this->tryStorePost($this->normalUser, 'image');
        $response->assertForbidden();
    }


    /** @test */
    public function absolutely_nobody_will_store_to_someone_else_timeline()
    {
        $url = route('network.post.store', [1]);

        $this->json('post', $url, ['token' => $this->getToken($this->superAdmin)])->assertForbidden();
    }


    /** @test */
    public function cannot_manage_someone_else_post()
    {
        $this->factoryPost($this->normalUser, 'image');

        $this->tryUpdatePost($this->goldUser, 'image')->assertForbidden();
        $this->deletePost($this->goldUser)->assertForbidden();
    }

    /** @test */
    public function super_admin_can_manage_someone_else_post()
    {
        $this->factoryPost($this->normalUser, 'image');

        $this->tryUpdatePost($this->superAdmin, 'image')->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->deletePost($this->superAdmin)->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function admin_can_manage_someone_else_post()
    {
        $this->factoryPost($this->normalUser, 'image');

        $this->tryUpdatePost($this->admin, 'image')->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->deletePost($this->admin)->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function network_manager_can_manage_someone_else_post()
    {
        $this->factoryPost($this->normalUser, 'image');

        $this->tryUpdatePost($this->networkManager, 'image')->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->deletePost($this->networkManager)->assertStatus(Response::HTTP_NO_CONTENT);
    }


    /***********************************-----------
     *
     *              Picture & Video           =======================
     *
     * /**********************************/


    /** @test */
    public function normal_and_silver_users_cannot_store__post_with_video()
    {
        $this->tryStorePost($this->normalUser, 'video')->assertSee($this->roleError);
        $this->tryStorePost($this->silverUser, 'video')->assertSee($this->roleError);
    }

    /** @test */
    public function gold_and_super_admin_users_can_store_post_with_video()
    {
        $this->tryStorePost($this->goldUser, 'video')->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->tryStorePost($this->superAdmin, 'video')->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function normal_user_cannot_update_his_post_with_video()
    {
        $this->factoryPost($this->normalUser, 'image');

        $this->tryUpdatePost($this->normalUser, 'video')->assertSee($this->roleError);
    }

    /** @test */
    public function silver_user_cannot_update_his_post_with_video()
    {
        $this->factoryPost($this->silverUser, 'image');

        $this->tryUpdatePost($this->silverUser, 'video')->assertSee($this->roleError);
    }

    /** @test */
    public function gold_user_can_update_his_post_with_video()
    {
        $this->factoryPost($this->goldUser, 'image');

        $this->tryUpdatePost($this->goldUser, 'video')->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function super_admin_user_can_update_his_post_with_video()
    {
        $this->factoryPost($this->superAdmin, 'image');

        $this->tryUpdatePost($this->superAdmin, 'video')->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function admin_user_can_update_his_post_with_video()
    {
        $this->factoryPost($this->admin, 'image');

        $this->tryUpdatePost($this->admin, 'video')->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function network_manager_user_can_update_his_post_with_video()
    {
        $this->factoryPost($this->networkManager, 'image');

        $this->tryUpdatePost($this->networkManager, 'video')->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }




    /***********************************-----------
     *
     *              Refactors                      =======================
     *
     * /**********************************/

    /**
     * @param $user
     * @return TestResponse
     */
    private function postManageIndex($user): TestResponse
    {
        $url = route('network.post.manage.index');
        return $this->json('get', $url, ['token' => $this->getToken($user)]);
    }


    /**
     * @param $user
     * @param String $type
     * @return TestResponse
     */
    private function tryStorePost($user, $type): TestResponse
    {
        $timeline_id = $user === null ? 1 : $user->timeline->id;
        $url = route('network.post.store', [$timeline_id]);
        $media = $type == 'image' ? $this->picture : $this->video;
        return $this->json('post', $url, array_merge($media, ['token' => $this->getToken($user)]));
    }

    /**
     * @param $user
     * @param String $type
     * @return TestResponse
     */
    private function tryUpdatePost($user, $type): TestResponse
    {
        $url = route('network.post.update', [Post::first()->timeline->id, 1]);
        $media = $type == 'image' ? $this->picture : $this->video;
        return $this->json('put', $url, array_merge($media, ['token' => $this->getToken($user)]));
    }

    /**
     * @param $user
     * @return TestResponse
     */
    private function deletePost($user): TestResponse
    {
        $url = route('network.post.destroy', [Post::first()->timeline->id, 1]);
        return $this->json('delete', $url, ['token' => $this->getToken($user)]);
    }


    /**
     * @param $user
     * @return TestResponse
     */
    private function indexPosts($user): TestResponse
    {
        $url = route('network.post.index', [1]);
        return $this->json('get', $url, ['token' => $this->getToken($user)]);
    }


    /**
     * @param $user
     * @return TestResponse
     */
    private function toggleLike($user): TestResponse
    {
        $url = route('network.post.like', [1, 1]);
        return $this->json('put', $url, ['token' => $this->getToken($user)]);
    }

    /**
     * @param $user
     * @return TestResponse
     */
    private function toggleShare($user): TestResponse
    {
        $url = route('network.post.share', [1, 1]);
        return $this->json('put', $url, ['token' => $this->getToken($user)]);
    }


    /**
     * @param $user
     * @param String $type
     * @return TestResponse
     */
    private function factoryPost($user, $type): TestResponse
    {
        /**
         * We cannot actually use post factory, Because the file created in a factory would be Real File.
         * We do not want to create real files in our tests.
         */

        $url = route('network.post.store', [$user->timeline_id]);
        $media = $type == 'image' ? $this->picture : $this->video;
        return $this->json('post', $url, array_merge($media, [
            'token' => $this->getToken($user),
            'title' => 'test title',
            'description' => 'description'
        ]));
    }


}
