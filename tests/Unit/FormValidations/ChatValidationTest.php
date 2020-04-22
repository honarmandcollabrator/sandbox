<?php

namespace Tests\Unit\Chat;

use App\Http\Requests\Chat\MessageRequest;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Validator;

class ChatValidationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;


    protected $rules;


    public function setUp(): void
    {
        parent::setUp();
        $this->createRoles();
        $this->rules = (new MessageRequest())->rules();
        factory(User::class)->create();
    }

    /***********************************-----------
     *
     *              Request           =======================
     *
     * /**********************************/

    /** @test */
    public function valid_message_body()
    {
        $this->bad('body', 123);
        $this->bad('body', false);
        $this->bad('body', '');
        $this->bad('body', str_repeat('a', 2001));
        /*===========*/
        $this->good('body', str_repeat('a', 2000));
        $this->good('body', '123');
    }
}
