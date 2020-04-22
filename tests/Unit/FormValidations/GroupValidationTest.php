<?php

namespace Tests\Unit\Chat;

use App\Http\Requests\Network\GroupRequest;
use App\Models\Job\Company;
use App\Models\Network\Group;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class GroupValidationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;


    protected $rules;


    public function setUp(): void
    {
        parent::setUp();
        $this->createRoles();
        $this->rules = (new GroupRequest())->rules();
        factory(User::class)->create();
    }


    /***********************************-----------
     *
     *              Validation           =======================
     *
     * /**********************************/

    /** @test */
    public function valid_group_name()
    {
        $this->bad('name', '');
        $this->bad('name', 123);
        $this->bad('name', str_repeat('a', 51));
        $this->bad('name', str_repeat('a', 4));
        /*===========*/
    }


    /** @test */
    public function valid_username()
    {
        $user = factory(User::class)->create();
        $group = factory(Group::class)->create(['admin_id' => $user->id]);

        $this->good('username', 'username');
        $this->good('username', 'user1name');
        $this->good('username', 'username1');
        $this->good('username', 'user_name');

        /*===========*/

        $this->bad('username', '');
        $this->bad('username', 123456);
        $this->bad('username', $group->username); //not unique
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
    public function valid_group_about()
    {
        $this->bad('about', 123);
        $this->bad('about', str_repeat('a', 29));
        $this->bad('about', str_repeat('a', 251));
        /*===========*/
        $this->good('about', ''); //nullable
    }


    /** @test */
    public function valid_group_avatar()
    {
        $this->bad('avatar', UploadedFile::fake()->create('doc.doc')->size(200));
        $this->bad('avatar', UploadedFile::fake()->create('video.mp4')->size(200));
        $this->bad('avatar', UploadedFile::fake()->image('image.jpg', 200, 200)->size(401));
        $this->bad('avatar', UploadedFile::fake()->image('image.jpg', 50, 50)->size(200));
        $this->bad('avatar', UploadedFile::fake()->image('image.jpg', 600, 400)->size(200));
        $this->bad('avatar', UploadedFile::fake()->image('image.jpg', 3500, 3500)->size(200));
        /*===========*/
        $this->good('avatar', null);
        $this->good('avatar', UploadedFile::fake()->image('image.jpg', 200, 200)->size(200));
        $this->good('avatar', UploadedFile::fake()->image('image.jpg', 300, 300)->size(200));
    }

}
