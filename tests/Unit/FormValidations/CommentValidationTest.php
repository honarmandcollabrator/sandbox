<?php

namespace Tests\Unit\Network;

use App\Http\Requests\Job\ResumeRequest;
use App\Http\Requests\Network\CommentRequest;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class CommentValidationTest extends TestCase
{
    protected $rules;

    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->rules = (new CommentRequest())->rules();
    }
    /***********************************-----------
     *
     *              Validation           =======================
     *
     * /**********************************/

    /** @test */
    public function valid_comment_description()
    {
        $this->bad('description', '');
        $this->bad('description', 1234);
        $this->bad('description', str_repeat('a', 401));
        /*===========*/
        $this->good('description', 'این یک کامنت است.');
    }

}
