<?php

namespace Tests\Feature\FunctionalityTests;

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

class NetworkTimelineRouteTest extends TestCase
{

    /***********************************-----------
     *
     *              Test setUp                      =======================
     *
     * /**********************************/


    use RefreshDatabase;
    use WithFaker;

    /**
     * @var string
     */
    private $apiUrl;

    private $dataWithPicture;
    private $dataWithVideo;


    public function setUp(): void
    {
        parent::setUp();

        $this->createUsers();

    }


    /*
       |--------------------------------------------------------------------------
       | Timeline
       |--------------------------------------------------------------------------
       |
       | Routes = 1
       |
       |
       |
       */


    /** @test */
    public function network_timeline()
    {
        /**
         * 1
         *
         * We create 3 post for normal user and 1 post for each silver and gold users,
         * Then we share two posts of the other users by normal user
         * So in timeline of normal user, We are going to have 3 posts, 3 of his own and not 2 shared.
         * Also we create 3 posts for admin and we don't do any share on them, just to make sure they don't get add up.
         */
        factory(Post::class, 3)->create(['user_id' => $this->normalUser->id, 'timeline_id' => $this->normalUser->timeline_id]);
        $silverPost = factory(Post::class)->create(['user_id' => $this->silverUser->id, 'timeline_id' => $this->silverUser->timeline_id]);
        $goldPost = factory(Post::class)->create(['user_id' => $this->goldUser->id, 'timeline_id' => $this->goldUser->timeline_id]);
        factory(Post::class, 3)->create(['user_id' => $this->goldUser->id, 'timeline_id' => $this->goldUser->timeline_id]);

        $this->normalUser->shares()->attach($silverPost->id);
        $this->normalUser->shares()->attach($goldPost->id);


        /** 2
         * Get the Timeline
         */
        $url = route('network.timeline', ['timeline' => $this->normalUser->timeline_id]);
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->silverUser)
        ]);


        /** 3
         * Assert
         */
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $this->normalUser->id,
            'username' => $this->normalUser->username,
            'name' => $this->normalUser->name,
            'avatar' => null
        ]);
        $response->assertJsonCount(3, 'data');
    }
}
