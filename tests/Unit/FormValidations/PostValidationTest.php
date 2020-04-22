<?php

namespace Tests\Unit\Network;

use App\Http\Requests\Network\PostRequest;
use App\Models\Network\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Storage;
use Validator;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class PostValidationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;



    private $token;
    private $user;


    /**
     * @var array
     */
    private $data;
    private $dataWithPicture;
    private $dataWithVideo;


    /**
     * @var string
     */
    private $apiUrl;
    /**
     * @var array
     */
    protected $rules;
    private $validator;


    public function setUp(): void
    {
        parent::setUp();

        $this->createRoles();

        Storage::fake('public');

        $this->apiUrl = 'api/network/timeline/1/post/';

//        $this->withoutExceptionHandling();
        $this->user = factory(User::class)->state('super_admin')->create();
        $this->token = JWTAuth::fromUser($this->user);

        $this->data = [
            'token' => $this->token,
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->sentence(10),
        ];

        $this->rules = (new PostRequest())->rules();
    }


    /***********************************-----------
     *
     *             PostRequest Direct Tests                      =======================
     *
     * /**********************************/


    /** @test */
    public function valid_title()
    {
        $this->bad('title', '');
        $this->bad('title', 123456465);
        $this->bad('title', str_repeat('a', 4));
        $this->bad('title', str_repeat('a', 51));
        /*===========*/

    }

    /** @test */
    public function valid_description()
    {
        $this->bad('description', '');
        $this->bad('description', 123);
        $this->bad('description', str_repeat('a', 4));
        $this->bad('description', str_repeat('a', 401));
        /*===========*/
    }

    /** @test */
    public function valid_media()
    {
        $this->bad('media', UploadedFile::fake()->create('document.doc'));
        /*===========*/
    }



    /*****************co******************-----------
     *
     *              Validations in Controller           =======================
     *
     * /**********************************/

    /** @test */
    public function post_request_is_getting_used_in_store()
    {
        $response = $this->json('post', $this->apiUrl, [
            'token' => $this->token,
            'title' => '',
            'description' => '',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'errors' => [
                'title',
                'description',
            ]
        ]);
    }

    /** @test */
    public function post_request_is_getting_used_in_update()
    {
        $post = factory(Post::class)->create(['user_id' => $this->user->id, 'timeline_id' => $this->user->timeline_id]);
        $response = $this->json('put', $this->apiUrl . $post->id, [
            'token' => $this->token,
            'title' => '',
            'description' => '',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'errors' => [
                'title',
                'description',
            ]
        ]);
    }

    /** @test */
    public function media_is_required_in_storing()
    {
        $response = $this->json('post', $this->apiUrl, [
            'token' => $this->token,
            'title' => 'valid title',
            'description' => 'valid description',
        ]);

        $this->checkError($response, 'media', 'این مورد نمیتواند خالی باشد.');
    }


    /** @test */
    public function media_is_not_required_in_updating()
    {
//        $this->withoutExceptionHandling();
        $post = factory(Post::class)->create(['user_id' => $this->user->id, 'timeline_id' => $this->user->timeline_id]);
        $response = $this->json('put', $this->apiUrl . $post->id, $this->data);

        $response->assertStatus(Response::HTTP_ACCEPTED);
    }

    /** @test */
    public function media_max_size_for_image_is_1000()
    {
        $post = factory(Post::class)->create(['user_id' => $this->user->id, 'timeline_id' => $this->user->timeline_id]);
        $response = $this->json('put', $this->apiUrl . $post->id, array_merge($this->data, [
            'media' => UploadedFile::fake()->image('image.jpg')->size(1001),
        ]));

        $this->checkError($response, 'media', 'نباید بیشتر از  1000 کیلوبایت باشد.');
    }

    /** @test */
    public function media_max_size_for_video_is_10000()
    {
        $response = $this->json('post', $this->apiUrl, array_merge($this->data, [
            'media' => UploadedFile::fake()->create('image.mp4')->size(10001),
        ]));

        $this->checkError($response, 'media', 'نباید بیشتر از  10000 کیلوبایت باشد.');
    }














    /***********************************-----------
     *
     *              Refactors                      =======================
     *
     * /**********************************/

    /**
     * @param string $message
     * @param TestResponse $response
     * @param string $key
     */
    private function checkError(TestResponse $response, string $key, string $message): void
    {
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson([
            'errors' => [
                $key => [
                    $message
                ]
            ]
        ]);
    }

}
