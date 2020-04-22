<?php

namespace Tests\Unit\Network;

use App\Models\Network\Comment;
use App\Models\Network\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class NetworkCommentSecurityTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;


    public function setUp(): void
    {
        parent::setUp();

        $this->createUsers();

        factory(Post::class)->create(['user_id' => $this->normalUser->id, 'timeline_id' => $this->normalUser->timeline_id]);
    }

    /***********************************-----------
     *
     *              Middlewares           =======================
     *
     * /**********************************/

    /** @test */
    public function jwt_comments_access()
    {
        $this->factoryComment($this->normalUser);

        $this->getCommentIndex(null)->assertUnauthorized();
        $this->tryStoreComment(null)->assertUnauthorized();
        $this->updateComment(null)->assertUnauthorized();
        $this->deleteComment(null)->assertUnauthorized();
    }


    /***********************************-----------
     *
     *               Policies                      =======================
     *
     * /**********************************/

    /** @test */
    public function cannot_create_more_than_20_comments_per_day()
    {
        factory(Comment::class, 20)->create(['user_id' => $this->normalUser->id, 'post_id' => 1]);

        $this->tryStoreComment($this->normalUser)->assertForbidden();
    }

    /** @test */
    public function cannot_update_someone_else_comment()
    {
        $this->factoryComment($this->normalUser);

        $this->updateComment($this->goldUser)->assertForbidden();
    }


    /** @test */
    public function cannot_delete_someone_else_comment()
    {
        $this->factoryComment($this->normalUser);

        $this->deleteComment($this->goldUser)->assertForbidden();
    }


    /***********************************-----------
     *
     *              Manager Policies           =======================
     *
     * /**********************************/


    /** @test */
    public function super_admin_can_update_someone_else_comment()
    {
        $this->factoryComment($this->goldUser);

        $this->updateComment($this->superAdmin)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function admin_can_update_someone_else_comment()
    {
        $this->factoryComment($this->goldUser);

        $this->updateComment($this->admin)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function network_manager_can_update_someone_else_comment()
    {
        $this->factoryComment($this->goldUser);

        $this->updateComment($this->networkManager)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function super_admin_can_delete_someone_else_comment()
    {
        $this->factoryComment($this->goldUser);

        $this->deleteComment($this->superAdmin)->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function admin_can_delete_someone_else_comment()
    {
        $this->factoryComment($this->goldUser);

        $this->deleteComment($this->admin)->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function network_manager_can_delete_someone_else_comment()
    {
        $this->factoryComment($this->goldUser);

        $this->deleteComment($this->networkManager)->assertStatus(Response::HTTP_NO_CONTENT);
    }



    /***********************************-----------
     *
     *              Refactors                      =======================
     *
     * /**********************************/


    private function getCommentIndex($user): TestResponse
    {
        $url = route('network.comment.index', [1, 1]);
        return $this->json('post', $url, ['token' => $this->getToken($user)]);
    }

    private function tryStoreComment($user): TestResponse
    {
        $url = route('network.comment.store', [1, 1]);
        return $this->json('post', $url, ['token' => $this->getToken($user)]);
    }

    private function updateComment($user): TestResponse
    {
        $url = route('network.comment.update', [1, 1, 1]);
        return $this->json('put', $url, ['token' => $this->getToken($user)]);
    }

    private function deleteComment($user): TestResponse
    {
        $url = route('network.comment.destroy', [1, 1, 1]);
        return $this->json('delete', $url, ['token' => $this->getToken($user)]);
    }


    /**
     * @param $user
     */
    private function factoryComment($user): void
    {
        factory(Comment::class)->create(['user_id' => $user->id, 'post_id' => 1]);
    }

}
