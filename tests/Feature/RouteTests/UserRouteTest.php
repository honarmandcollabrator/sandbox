<?php

namespace Tests\Feature\User;

use App\Models\Chat\Chat;
use App\Models\Chat\Message;
use App\Models\Chat\Session;
use App\Models\Contact\Contact;
use App\Models\Globals\City;
use App\Models\Globals\Country;
use App\Models\Globals\Gender;
use App\Models\Globals\Province;
use App\Models\Globals\Religion;
use App\Models\Job\Company;
use App\Models\Job\DutyStatus;
use App\Models\Job\Job;
use App\Models\Job\JobCategory;
use App\Models\Job\JobDegree;
use App\Models\Job\JobPayment;
use App\Models\Job\JobTimeStatus;
use App\Models\Job\Resume;
use App\Models\Job\WorkExperienceYears;
use App\Models\Network\Comment;
use App\Models\Network\Group;
use App\Models\Network\Post;
use App\Models\Network\Timeline;
use App\Models\User\UserExperience;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserRouteTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->createUsers();

        Storage::fake('public');
    }



    /*
       |--------------------------------------------------------------------------
       | User Routes
       |--------------------------------------------------------------------------
       |
       | 6 routes
       |
       |
       |
       */

    /** @test 1 */
    public function user_index()
    {
        $url = route('user.index');
        $response = $this->json('get', $url, ['token' => $this->getToken($this->superAdmin)]);
        $response->assertOk();
        $response->assertJsonStructure(['data']);
    }


    /** @test 2 */
    public function user_show()
    {
        factory(UserExperience::class, 3)->create([
            'user_id' => $this->normalUser->id
        ]);

        /*===== useful to test if resume creation has influence on complete percentage =====*/
//        $this->createResumeEssentials(); useful to test if resume creation has influence on complete percentage
//        factory(Resume::class)->create(['user_id' => $this->normalUser->id]);

        $url = route('user.show', ['user' => $this->normalUser->id, 'token' => $this->getToken($this->normalUser)]);
        $response = $this->json('get', $url);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(3, 'data.experiences');

        $response->assertJsonFragment([
            'name' => $this->normalUser->name,
            'username' => $this->normalUser->username,
            'email' => $this->normalUser->email,
            'about' => $this->normalUser->about,
            'id' => $this->normalUser->id,
            'role' => $this->normalUser->role->name,
            'province' => [
                'id' => null,
                'name' => null,
            ],
            'timeline_id' => $this->normalUser->timeline_id,
            'resume_id' => 0,
            'company_id' => 0,
        ]);

        $response->assertSee('is_mine');
        $response->assertDontSee('friend_status');
    }


    /** @test 3 */
    public function user_update()
    {
        $this->normalUser = factory(User::class)->create();

        $url = route('user.update', ['user' => $this->normalUser->id]);
        $response = $this->json('put', $url, [
            'token' => $this->getToken($this->normalUser),
            'name' => 'newName',
            'username' => 'newUserName',
            'about' => $about = $this->faker->sentence(10),
            'province_id' => (factory(Province::class)->create())->id,
            'avatar' => UploadedFile::fake()->image('avatar.jpg', 150, 150)->size(100),
        ]);
        $response->assertStatus(Response::HTTP_ACCEPTED);

        $response->assertJsonFragment([
            'name' => $this->normalUser->fresh()->name,
            'username' => $this->normalUser->fresh()->username,
            'about' => $about,
            'province' => [
                'id' => Province::first()->id,
                'name' => Province::first()->name,
            ],
        ]);

        Storage::disk('public')->assertExists($this->normalUser->fresh()->avatar);
    }


    /** #4
     * @test
     */
    public function user_ban()
    {
        $url = route('user.ban', ['user' => $this->normalUser->id]);
        $response = $this->json('put', $url, ['token' => $this->getToken($this->superAdmin)]);

        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertEquals(1, $this->normalUser->fresh()->is_ban);
    }


    /** #5
     * @test
     */
    public function user_destroy()
    {
        $this->withoutExceptionHandling();
        $url = route('user.destroy', ['user' => $this->goldUser->id]);
        $response = $this->json('delete', $url, ['token' => $this->getToken($this->superAdmin)]);

        $response->assertStatus(Response::HTTP_ACCEPTED);
    }

    /** @test 6 */
    public function user_role_change()
    {
        $url = route('user.role.change', [$this->normalUser->id]);
        $response = $this->json('put', $url, [
            'token' => $this->getToken($this->superAdmin),
            'role' => 4,
        ]);
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertEquals(4, $this->normalUser->fresh()->role_id);
    }

    /***********************************-----------
     *
     *              Functionality           =======================
     *
     * /**********************************/

    /** @test */
    public function cascade_on_delete_user()
    {
        /*===== 1- We create many records with a user =====*/
        $user = $this->goldUser;
        $user2 = $this->networkManager;
        $this->createJobEssentials();

        factory(Contact::class, 3)->create(['user_id' => $user->id, 'type' => 'user']);

        factory(Post::class, 3)->create(['user_id' => $user->id, 'timeline_id' => $user->timeline->id]);
        factory(Comment::class, 3)->create(['user_id' => $user->id, 'post_id' => 1]);
        factory(Group::class, 3)->create(['admin_id' => $user->id]);

        factory(Company::class)->create(['user_id' => $user->id]);
        factory(Job::class, 3)->create(['company_id' => 1]);
        factory(UserExperience::class, 3)->create(['user_id' => $user->id]);

        $user->request_recipients()->attach([$user2->id => ['status' => 'approved']]);
//        $user->groups()->attach([10 => ['status' => 'approved']]);


        $url = route('chat.session.create', ['user' => $user2->id]);
        $this->json('post', $url, ['token' => $this->getToken($user)]);
        $url = route('chat.send', [1]);
        $this->json('post', $url, ['token' => $this->getToken($this->goldUser), 'body' => 'test message']);


        /*===== Check if everything is in database =====*/
        $this->assertCount(8, User::all());
        $this->assertCount(3, Contact::all());
        $this->assertCount(3, Post::all());
        $this->assertCount(3, Comment::all());
        $this->assertCount(3, Group::all());
        $this->assertCount(1, Company::all());
        $this->assertCount(3, Job::all());
        $this->assertCount(3, UserExperience::all());
        $this->assertCount(1, Session::all());
        $this->assertCount(2, Chat::all());
        $this->assertCount(1, Message::all());


        /*===== 2- We soft delete the user with super admin =====*/
        $url = route('user.destroy', ['user' => $this->goldUser->id]);
        $response = $this->json('delete', $url, ['token' => $this->getToken($this->superAdmin)]);
        $response->assertStatus(Response::HTTP_ACCEPTED);


        /*===== 3- Assert that records are deleted =====*/
        $this->assertCount(7, User::all());

        $this->assertCount(0, Contact::all());

        $this->assertCount(0, Post::all());
        $this->assertCount(0, Comment::all());
        $this->assertCount(0, Group::all());

        $this->assertCount(0, Company::all());
        $this->assertCount(0, Job::all());
        $this->assertCount(0, UserExperience::all());

        $this->assertCount(0, Session::all());
        $this->assertCount(0, Chat::all());
        $this->assertCount(0, Message::all());

        /*===== 4- Assert that user still exist in database =====*/
        $this->assertCount(8, User::withTrashed()->get());

    }


    private function createJobEssentials()
    {
        factory(City::class, 2)->create();
        factory(Country::class, 2)->create();
        factory(Province::class, 2)->create();
        factory(JobTimeStatus::class, 2)->create();
        factory(JobCategory::class, 2)->create();
        factory(JobPayment::class, 2)->create();
        factory(Gender::class, 2)->create();
        factory(DutyStatus::class, 2)->create();
        factory(JobDegree::class, 2)->create();
        factory(WorkExperienceYears::class, 2)->create();
        factory(Religion::class, 2)->create();
    }

    private function createResumeEssentials()
    {
        factory(Country::class)->create();
        factory(City::class)->create();
        factory(WorkExperienceYears::class)->create();
        factory(JobCategory::class)->create();
        factory(JobDegree::class)->create();
        factory(Gender::class)->create();
        factory(DutyStatus::class)->create();
        factory(Religion::class)->create();
    }

}
