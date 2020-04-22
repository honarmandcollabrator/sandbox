<?php

namespace Tests\Feature\User;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Notification;
use Tests\TestCase;

class VerificationRouteTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->createUsers();
    }


    /*
       |--------------------------------------------------------------------------
       | Verification Routes
       |--------------------------------------------------------------------------
       |
       | 2 Routes
       |
       |
       |
       */

    /** @test */
    public function verification_resend()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);
        $url = route('verification.resend');
        $response = $this->json('post', $url, ['token' => $this->getToken($user)]);
        $response->assertOk();

    }

    /** @test */
    public function verification_verify()
    {
        //todo: design it
        $this->assertTrue(true);
    }

}
