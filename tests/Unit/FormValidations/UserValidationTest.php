<?php

namespace Tests\Unit\User;

use App\Http\Requests\User\UserRequest;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class UserValidationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    protected $rules;

    public function setUp(): void
    {
        parent::setUp();

        $this->createRoles();

        $this->rules = (new UserRequest())->rules();
    }

    /***********************************-----------
     *
     *              UserRequest Direct Tests                      =======================
     *
     * /**********************************/


    /** @test */
    public function valid_username()
    {
        $user = factory(User::class)->create();

        $this->good('username', 'username');
        $this->good('username', 'user1name');
        $this->good('username', 'username1');
        $this->good('username', 'user_name');

        /*===========*/

        $this->bad('username', '');
        $this->bad('username', 123456);
        $this->bad('username', $user->username); //not unique
        $this->bad('username', '123456');
        $this->bad('username', 'user@name');
        $this->bad('username', 'user#name');
        $this->bad('username', 'user*name');
        $this->bad('username', '1username');
        $this->bad('username', 'poo ria');
        $this->bad('username', '_username');
        $this->bad('username', '-username');
        $this->bad('username', 'username-');
        $this->bad('username', 'username_');
        $this->bad('username', 'user-name');
        $this->bad('username', 'user--name');
        $this->bad('username', 'user__name');
    }

    /** @test */
    public function valid_name()
    {
        $this->bad('name', '');
        $this->bad('name', 123);
        $this->bad('name', str_repeat('a', 4));
        $this->bad('name', str_repeat('a', 41));
        /*===========*/
        $this->good('name', '123456');
    }


    /** @test */
    public function valid_about()
    {
        $this->bad('about', 123);
        $this->bad('about', str_repeat('a', 251));
        $this->bad('about', str_repeat('a', 29));
        /*===========*/
    }

    /** @test */
    public function valid_avatar()
    {
        $this->good('avatar', UploadedFile::fake()->image('pic.png', 100, 100)->size(400));
        /*===========*/
        $this->bad('avatar', UploadedFile::fake()->create('document.pdf')->size(200));
        $this->bad('avatar', UploadedFile::fake()->image('avatar.png', 99, 99)->size(400));
        $this->bad('avatar', UploadedFile::fake()->image('avatar.png', 1501, 1501)->size(400));
        $this->bad('avatar', UploadedFile::fake()->image('avatar.png', 200, 200)->size(401));
        $this->bad('avatar', UploadedFile::fake()->image('avatar.png', 200, 300)->size(100));
    }

    /** @test */
    public function valid_province_id()
    {
        $this->bad('province_id', 1);
        /*===========*/
    }



}
