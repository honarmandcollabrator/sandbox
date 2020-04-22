<?php

namespace Tests\Feature\User;

use App\Models\User\UserExperience;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserExperienceRouteTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->createUsers();
    }




    /*
       |--------------------------------------------------------------------------
       | UserExperience Routes
       |--------------------------------------------------------------------------
       |
       | 3 Routes
       |
       |
       |
       */

    /** #1
     * @test
     */
    public function user_experience_store_route()
    {
        $this->withoutExceptionHandling();
        $url = route('user.experiences.store', [1]);
        $response = $this->json('post', $url, [
            'token' => $this->getToken($this->normalUser),
            'work_place_name' => 'name test',
            'job_role' => 'test role',
            'started_at' => '1/2/2000',
            'finished_at' => '2/2/2003'
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertCount(1, UserExperience::all());
        $response->assertJsonFragment([
            'id' => UserExperience::first()->id,
            'work_place_name' => 'name test',
            'job_role' => 'test role',
            'started_at' => jdate(UserExperience::first()->started_at)->format('%d %B %Y'),
            'finished_at' => jdate(UserExperience::first()->finished_at)->format('%d %B %Y')
        ]);


    }

    /** #2
     * @test
     */
    public function user_experience_update_route()
    {
        factory(UserExperience::class)->create(['user_id' => $this->normalUser->id]);

        $url = route('user.experiences.update', [1, 1]);
        $response = $this->json('put', $url, [
            'token' => $this->getToken($this->normalUser),
            'work_place_name' => 'name test',
            'job_role' => 'test role',
            'started_at' => '1/2/2000',
            'finished_at' => '2/2/2003'
        ]);

        $response->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertInstanceOf(Carbon::class, UserExperience::first()->started_at);
        $this->assertInstanceOf(Carbon::class, UserExperience::first()->finished_at);

        $response->assertJsonFragment([
            'id' => UserExperience::first()->id,
            'work_place_name' => 'name test',
            'job_role' => 'test role',
            'started_at' => jdate(UserExperience::first()->started_at)->format('%d %B %Y'),
            'finished_at' => jdate(UserExperience::first()->finished_at)->format('%d %B %Y')
        ]);

    }

    /** #3
     * @test
     */
    public function user_experience_destroy_route()
    {
        factory(UserExperience::class)->create(['user_id' => $this->normalUser->id]);

        $url = route('user.experiences.destroy', [1, 1]);
        $response = $this->json('delete', $url, [
            'token' => $this->getToken($this->normalUser)
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertCount(0, UserExperience::all());
    }


    /***********************************-----------
     *
     *              Refactors           =======================
     *
     * /**********************************/


}
