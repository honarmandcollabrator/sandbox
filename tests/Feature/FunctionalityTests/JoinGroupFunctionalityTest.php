<?php

namespace Tests\Feature\FunctionalityTests;

use App\Models\Network\Comment;
use App\Models\Network\Group;
use App\Models\Network\Hashtag;
use App\Models\Network\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class JoinGroupFunctionalityTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createUsers();
    }


    /** @test */
    public function owner_is_joined_automatically_to_his_group()
    {
        $this->storeGroup($this->silverUser);

        $this->assertCount(1, $this->silverUser->groups);
    }


    /** @test */
    public function when_we_get_posts_with_certain_hashtag_then_group_posts_are_filtered_and_not_showing()
    {
        /*===== We create a group and we store a post with a hashtag in it.  =====*/
        $this->storeGroup($this->normalUser);
        $url = route('network.post.store', ['timeline' => $this->groupTimelineId()]);
        $this->json('post', $url, [
            'token' => $this->getToken($this->normalUser),
            'title' => 'group post',
            'description' => 'group description #one',
            'media' => UploadedFile::fake()->image('image.png')->size(1000),
        ]);
        $this->assertCount(1, Group::first()->timeline->posts);
        $this->assertCount(1, Hashtag::all());


        /*===== Another user outside group in his own timeline has a post with same hashtag =====*/
        $url = route('network.post.store', ['timeline' => $this->silverUser->timeline->id]);
        $this->json('post', $url, [
            'token' => $this->getToken($this->silverUser),
            'title' => 'user post',
            'description' => 'user description #one',
            'media' => UploadedFile::fake()->image('image.png')->size(1000),
        ]);
        $this->assertCount(2, Post::all());
        $this->assertCount(1, Hashtag::all());


        /*===== Now a different user tries to get that hashtag, now he should get only one post that is outside the group =====*/
        $url = route('network.post.index', ['timeline' => $this->goldUser->timeline->id]);
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->goldUser),
            'hashtag' => 'one'
        ]);

        $response->assertJsonCount(1, 'data');
        $response->assertSee('user post');
        $response->assertDontSee('group post');
    }



    /***********************************-----------
     *
     *              Timeline           =======================
     *
     * /**********************************/

    /** @test */
    public function if_joined_can_get_timeline()
    {
        $this->storeGroup($this->silverUser);
        $this->joinGroup($this->normalUser);

        $this->getGroupTimeline($this->normalUser)->assertOk();
    }

    /** @test */
    public function if_not_joined_cannot_get_timeline()
    {
        $this->storeGroup($this->silverUser);

        $this->getGroupTimeline($this->normalUser)->assertForbidden();
    }


    /** @test */
    public function super_admin_still_can_get_timeline()
    {
        $this->storeGroup($this->silverUser);

        $this->getGroupTimeline($this->superAdmin)->assertOk();
    }

    /** @test */
    public function admin_still_can_get_timeline()
    {
        $this->storeGroup($this->silverUser);

        $this->getGroupTimeline($this->admin)->assertOk();
    }

    /** @test */
    public function network_manager_still_can_get_timeline()
    {
        $this->storeGroup($this->silverUser);

        $this->getGroupTimeline($this->networkManager)->assertOk();
    }



    /***********************************-----------
     *
     *              Posts           =======================
     *
     * /**********************************/

    /** @test */
    public function owner_can_remove_any_post_inside_his_group()
    {
        $this->storeGroup($this->normalUser);

        $this->joinGroup($this->silverUser);
        $this->factoryGroupPost($this->silverUser);

        $this->deleteGroupPost($this->normalUser)->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function non_owner_cannot_remove_other_posts_after_he_joined()
    {
        $this->storeGroup($this->goldUser);

        $this->joinGroup($this->normalUser);
        $this->factoryGroupPost($this->normalUser);

        $this->joinGroup($this->silverUser);

        $this->deleteGroupPost($this->silverUser)->assertForbidden();
    }

    /** @test */
    public function if_owner_remove_someone_from_group_also_removes_his_posts()
    {
        $this->storeGroup($this->goldUser);

        $this->joinGroup($this->normalUser);

        $this->factoryGroupPost($this->normalUser);
        $this->factoryGroupPost($this->normalUser);
        $this->factoryGroupPost($this->normalUser);
        $this->factoryGroupPost($this->normalUser);

        $this->assertCount(4, Post::all());

        $url = route('join.remove', ['group' => 1, 'user' => $this->normalUser->id]);
        $this->json('delete', $url, ['token' => $this->getToken($this->goldUser)])->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertCount(0, Post::all());
    }



    /**
     * get, store, update, delete and share posts. AKA manage posts.
     */

    /** @test */
    public function if_not_joined_cannot_manage_post()
    {
        $this->storeGroup($this->silverUser);

        $this->factoryGroupPost($this->goldUser);

        $this->tryStoreGroupPost($this->goldUser)->assertForbidden();
        $this->tryUpdateGroupPost($this->goldUser)->assertForbidden();
        $this->shareGroupPost($this->goldUser)->assertForbidden();
        $this->likeGroupPost($this->goldUser)->assertForbidden();
        $this->deleteGroupPost($this->goldUser)->assertForbidden();
    }

    /** @test */
    public function if_just_requested_cannot_manage_post()
    {
        $this->storeGroup($this->silverUser);
        $this->goldUser->groups()->attach([1 => ['status' => 'pending']]);

        $this->factoryGroupPost($this->goldUser);

        $this->tryStoreGroupPost($this->goldUser)->assertForbidden();
        $this->tryUpdateGroupPost($this->goldUser)->assertForbidden();
        $this->shareGroupPost($this->goldUser)->assertForbidden();
        $this->likeGroupPost($this->goldUser)->assertForbidden();
        $this->deleteGroupPost($this->goldUser)->assertForbidden();
    }


    /** @test */
    public function if_joined_can_manage_post()
    {
        $this->storeGroup($this->silverUser);

        $this->joinGroup($this->normalUser);
        $this->factoryGroupPost($this->normalUser);

        $this->managePostIsOk($this->normalUser);
    }


    /** @test */
    public function if_not_joined_but_super_admin_still_can_manage_posts()
    {
        $this->storeGroup($this->goldUser);
        $this->factoryGroupPost($this->goldUser);

        $this->tryStoreGroupPost($this->superAdmin)->assertForbidden();
        $this->managePostIsOk($this->superAdmin);
    }

    /** @test */
    public function if_not_joined_but_admin_still_can_manage_posts()
    {
        $this->storeGroup($this->silverUser);
        $this->factoryGroupPost($this->silverUser);

        $this->tryStoreGroupPost($this->admin)->assertForbidden();
        $this->managePostIsOk($this->admin);
    }

    /** @test */
    public function if_not_joined_but_network_manager_still_can_manage_posts()
    {
        $this->storeGroup($this->normalUser);
        $this->factoryGroupPost($this->normalUser);

        $this->tryStoreGroupPost($this->networkManager)->assertForbidden();
        $this->managePostIsOk($this->networkManager);
    }


    /***********************************-----------
     *
     *              Comments           =======================
     *
     * /**********************************/

    private function tryStoreGroupComment($user): TestResponse
    {
        $url = route('network.comment.store', ['timeline' => $this->groupTimelineId(), 'post' => 1]);
        return $this->json('post', $url, ['token' => $this->getToken($user)]);
    }

    private function tryUpdateGroupComment($user): TestResponse
    {
        $url = route('network.comment.update', ['timeline' => $this->groupTimelineId(), 1, 1]);
        return $this->json('put', $url, ['token' => $this->getToken($user)]);
    }

    private function deleteGroupComment($user): TestResponse
    {
        $url = route('network.comment.destroy', ['timeline' => $this->groupTimelineId(), 1, 1]);
        return $this->json('delete', $url, ['token' => $this->getToken($user)]);
    }

    private function factoryGroupComment($user): void
    {
        factory(Comment::class)->create(['user_id' => $user->id, 'post_id' => 1]);
    }

    private function manageCommentIsOk($user): void
    {
        $this->tryStoreGroupComment($user)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->tryUpdateGroupComment($user)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->deleteGroupComment($user)->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function if_not_joined_cannot_manage_comment()
    {
        $this->storeGroup($this->silverUser);
        $this->factoryGroupPost($this->silverUser);

        $this->factoryGroupComment($this->goldUser);

        $this->tryStoreGroupComment($this->goldUser)->assertForbidden();
        $this->tryUpdateGroupComment($this->goldUser)->assertForbidden();
        $this->deleteGroupComment($this->goldUser)->assertForbidden();
    }


    /** @test */
    public function if_joined_can_manage_comment()
    {
        $this->storeGroup($this->silverUser);
        $this->factoryGroupPost($this->silverUser);

        $this->joinGroup($this->goldUser);
        $this->factoryGroupComment($this->goldUser);

        $this->manageCommentIsOk($this->goldUser);
    }


    /** @test */
    public function if_not_joined_but_super_admin_can_manage_comment()
    {
        $this->storeGroup($this->silverUser);
        $this->factoryGroupPost($this->silverUser);

        $this->joinGroup($this->goldUser);
        $this->factoryGroupComment($this->goldUser);

        $this->manageCommentIsOk($this->superAdmin);
    }


    /** @test */
    public function if_not_joined_but_admin_can_manage_comment()
    {
        $this->storeGroup($this->silverUser);
        $this->factoryGroupPost($this->silverUser);

        $this->joinGroup($this->goldUser);
        $this->factoryGroupComment($this->goldUser);

        $this->manageCommentIsOk($this->admin);
    }


    /** @test */
    public function if_not_joined_but_network_manager_can_manage_comment()
    {
        $this->storeGroup($this->silverUser);
        $this->factoryGroupPost($this->silverUser);

        $this->joinGroup($this->goldUser);
        $this->factoryGroupComment($this->goldUser);

        $this->manageCommentIsOk($this->networkManager);
    }




    /***********************************-----------
     *
     *              Like & Share           =======================
     *
     * /**********************************/

    /** @test */
    public function share_post_is_impossible_if_timeline_is_group()
    {
        $this->storeGroup($this->superAdmin);

        $this->factoryGroupPost($this->superAdmin);

        $this->joinGroup($this->normalUser);

        $this->shareGroupPost($this->superAdmin)->assertForbidden();
    }

    /** @test */
    public function like_post_is_possible_in_groups()
    {
        $this->storeGroup($this->superAdmin);

        $this->factoryGroupPost($this->superAdmin);

        $this->joinGroup($this->normalUser);

        $this->likeGroupPost($this->normalUser)->assertStatus(Response::HTTP_ACCEPTED);
    }


    /***********************************-----------
     *
     *              Wrong Timeline           =======================
     *
     * /**********************************/

    /** @test */
    public function using_wrong_timeline_id_is_forbidden_for_store_comment()
    {
        $this->storeGroup($this->normalUser);

        $this->joinGroup($this->silverUser);
        $this->factoryGroupPost($this->silverUser);

        /*===== Normally this is forbidden =====*/
        $this->tryStoreGroupComment($this->goldUser)->assertForbidden();

        /*===== Changing timeline id and this is still forbidden =====*/
        $url = route('network.comment.store', ['timeline' => 1, 'post' => 1]);
        $this->json('post', $url, ['token' => $this->getToken($this->goldUser)])->assertForbidden();
    }


    /** @test */
    public function using_wrong_timeline_id_is_forbidden_for_update_post()
    {
        $this->storeGroup($this->normalUser);

        $this->factoryGroupPost($this->silverUser);


        /*===== Normally this is forbidden =====*/
        $this->tryUpdateGroupPost($this->silverUser)->assertForbidden();

        /*===== Changing timeline id and this is still forbidden =====*/
        $url = route('network.post.update', ['timeline' => 1, 'post' => 1]);
        $this->json('put', $url, ['token' => $this->getToken($this->silverUser)])->assertForbidden();
    }


    /***********************************-----------
     *
     *              Refactors                      =======================
     *
     * /**********************************/

    private function factoryGroupPost($user)
    {
        factory(Post::class)->create(['user_id' => $user->id, 'timeline_id' => $this->groupTimelineId()]);
    }

    private function joinGroup($user): void
    {
        /*===== We take a user and join him to first group we find in database =====*/
        $user->groups()->attach([Group::first()->id => ['status' => 'approved']]);
    }

    private function tryStoreGroupPost($user): TestResponse
    {
        $url = route('network.post.store', ['timeline' => $this->groupTimelineId()]);
        return $this->json('post', $url, ['token' => $this->getToken($user)]);
    }

    private function getGroupPost($user): TestResponse
    {
        $url = route('network.post.show', ['timeline' => $this->groupTimelineId(), 'post' => 1]);
        return $this->json('get', $url, ['token' => $this->getToken($user)]);
    }

    private function tryUpdateGroupPost($user): TestResponse
    {
        $url = route('network.post.update', ['timeline' => $this->groupTimelineId(), 'post' => 1]);
        return $this->json('put', $url, ['token' => $this->getToken($user)]);
    }


    private function getGroupTimeline($user): TestResponse
    {
        $url = route('network.timeline', ['timeline' => $this->groupTimelineId()]);
        return $this->json('get', $url, ['token' => $this->getToken($user)]);
    }

    private function groupTimelineId()
    {
        return Group::first()->timeline->id;
    }

    private function deleteGroupPost($user): TestResponse
    {
        $url = route('network.post.destroy', ['timeline' => $this->groupTimelineId(), 'post' => 1]);
        return $this->json('delete', $url, ['token' => $this->getToken($user)]);
    }

    private function shareGroupPost($user): TestResponse
    {
        $url = route('network.post.share', ['timeline' => $this->groupTimelineId(), 'post' => 1]);
        return $this->json('put', $url, ['token' => $this->getToken($user)]);
    }

    private function likeGroupPost($user): TestResponse
    {
        $url = route('network.post.like', ['timeline' => $this->groupTimelineId(), 'post' => 1]);
        return $this->json('put', $url, ['token' => $this->getToken($user)]);
    }

    private function managePostIsOk($user): void
    {
        $this->tryUpdateGroupPost($user)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->shareGroupPost($user)->assertForbidden();
        $this->likeGroupPost($user)->assertStatus(Response::HTTP_ACCEPTED);
        $this->deleteGroupPost($user)->assertStatus(Response::HTTP_NO_CONTENT);
    }


}
