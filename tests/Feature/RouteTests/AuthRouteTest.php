<?php

namespace Tests\Feature;

use App\Mail\ForgotPassword;
use App\Notifications\VerifyEmail;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Mail;
use Notification;
use Password;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthRouteTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->createRoles();
        Mail::fake();
        Notification::fake();
    }





    /*
       |--------------------------------------------------------------------------
       | Auth
       |--------------------------------------------------------------------------
       |
       | Routes = 7
       |
       |
       |
       */

    /**
     * #1
     * @test
     */
    public function auth_register_customer()
    {
        $this->withoutExceptionHandling();
        $url = route('auth.register');
        $response = $this->json('post', $url, [
            'name' => 'test name',
            'email' => 'test@gmail.com',
            'phone' => '09191234567',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(1, User::all());
        Notification::assertSentTo(User::first(), VerifyEmail::class);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);
    }



    /**
     * #3
     * @test
     */
    public function auth_login()
    {
        $this->withoutExceptionHandling();
        $url = route('auth.register');
        $this->json('post', $url, [
            'name' => 'test name',
            'email' => 'test@gmail.com',
            'phone' => '09122899787',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $url = route('auth.login');
        $response = $this->json('post', $url, [
            'email' => 'test@gmail.com',
            'password' => 'password'
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);
    }


    /**
     * #4
     * @test
     */
    public function auth_me()
    {
        $url = route('auth.register');
        $this->json('post', $url, [
            'name' => 'test name',
            'email' => 'test@gmail.com',
            'phone' => '09158899878',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $url = route('auth.me');
        $response = $this->json('post', $url, [
            'token' => JWTAuth::fromuser(User::first()),
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure(['data']);
    }


    /**
     * #5
     * @test
     */
    public function auth_refresh()
    {
        $url = route('auth.register');
        $this->json('post', $url, [
            'name' => 'test name',
            'email' => 'test@gmail.com',
            'phone' => '09121234567',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $url = route('auth.refresh');
        $response = $this->json('post', $url, [
            'token' => JWTAuth::fromuser(User::first()),
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);
    }


    /**
     * #6
     * @test
     */
    public function auth_logout()
    {
        $url = route('auth.register');
        $this->json('post', $url, [
            'name' => 'test name',
            'email' => 'test@gmail.com',
            'phone' => '09121234567',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $url = route('auth.logout');
        $response = $this->json('post', $url, [
            'token' => JWTAuth::fromuser(User::first()),
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(["message" => "Successfully logged out"]);
    }


    /** @test #7 */
    public function auth_forgot_password_email()
    {
        $user = factory(User::class)->create();

        $url = route('auth.forgot.password.email');
        $response = $this->json('post', $url, [
            'email' => $user->email,
        ]);
        Mail::assertSent(ForgotPassword::class);
        $response->assertStatus(Response::HTTP_ACCEPTED);
    }


    /** @test #8 */
    public function auth_forgot_password_reset()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $url = route('auth.forgot.password.reset');
        $response = $this->json('post', $url, [
            'token' => Password::broker()->createToken($user),
            'email' => $user->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword'
        ]);
        $response->assertOk();

        /*===== Try to login with new password =====*/
        $url = route('auth.login');
        $response = $this->json('post', $url, [
            'email' => $user->email,
            'password' => 'newpassword'
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);
    }
}
