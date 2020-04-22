<?php

namespace Tests\Feature\User;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class AuthSecurityTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createRoles();
    }

    /** @test */
    public function user_with_wrong_credentials_cannot_login()
    {
        $this->withoutExceptionHandling();
        $url = route('auth.login');
        $response = $this->json('post', $url, [
            'email' => 'not_legit_email@gmail.com',
            'password' => 'notlegitpassword'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson([
            'errors' => [
                'email' => 'ایمیل یا پسورد اشتباه است',
            ],
        ]);
    }


    /** @test */
    public function banned_user_cannot_login()
    {
        $user = factory(User::class)->create(['is_ban' => 1]);

        $url = route('auth.login');
        $response = $this->json('post', $url, [
            'email' => $user->email,
            'password' => 'password'
        ]);
        $response->assertForbidden()->assertSee('you are banned');
    }


    /** @test */
    public function banned_user_cannot_access_any_route_with_old_token()
    {
        $user = factory(User::class)->create();
        $oldToken = $this->getToken($user);

        $user->update(['is_ban' => 1]);


        /*===== The user try a random route that needs jwt for first time after ban with old token =====*/
        $url = route('jobs.company.store');
        $response = $this->json('post', $url, [
            'token' => $oldToken,
            'name' => 'bla bla bla'
        ]);
        $response->assertForbidden();

        /*===== the user try another second route that need jwt with old token =====*/
        $url = route('auth.me');
        $response = $this->json('post', $url, [
            'token' => $oldToken,
        ]);
        $response->assertUnauthorized();
    }


}
