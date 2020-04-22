<?php

namespace Tests\Unit\Chat;

use App\Models\Chat\Chat;
use App\Models\Chat\Message;
use App\User;
use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ChatSecurityTest extends TestCase
{

    /***********************************-----------
     *
     *              Test setUp                      =======================
     *
     * /**********************************/


    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->createUsers();
        /**
         * Silver user is friend with everybody
         */
        $this->silverUser->request_recipients()->attach([$this->normalUser->id => ['status' => 'approved']]);
        $this->silverUser->request_senders()->attach([$this->goldUser->id => ['status' => 'approved']]);
        $this->silverUser->request_senders()->attach([$this->superAdmin->id => ['status' => 'approved']]);

    }



    /***********************************-----------
     *
     *              Anybody                      =======================
     *
     * /**********************************/

    /** @test */
    public function cannot_create_more_than_500_messages_per_day()
    {
        $url = route('chat.session.create', [2]);
        $this->post($url, [
            'token' => $this->getToken($this->goldUser)
        ]);
        $url = route('chat.send', [1]);
        factory(Message::class, 1)->create(['session_id' => 1]);
        for($i = 1; $i <= 500; $i++) {
            DB::table('chats')->insert(
                [
                    'message_id' => 1,
                    'session_id' => 1,
                    'user_id' => $this->goldUser->id,
                    'type' => 0,
                    'created_at' => now(),
                ]
            );
        }
        $this->assertCount(500, Chat::all());
        $this->post($url, ['token' => $this->getToken($this->goldUser), 'body' => 'test message'])->assertForbidden();

    }


    /** @test */
    public function cannot_have_two_sessions_between_two_users()
    {
        $this->createASession($this->silverUser, $this->goldUser);
        $response = $this->createASession($this->silverUser, $this->goldUser);

        $response->assertForbidden();
        $response->assertJson([
            'error' => 'session already created'
        ]);
    }

    /** @test */
    public function cannot_have_two_sessions_between_two_users_the_reverse_direction()
    {
        $this->createASession($this->silverUser, $this->goldUser);
        $response = $this->createASession($this->goldUser, $this->silverUser);

        $response->assertForbidden();
        $response->assertJson([
            'error' => 'session already created'
        ]);
    }

    /** @test */
    public function cannot_create_chat_session_for_a_non_approved_friend()
    {
        /** we know that admin and gold user are not friends */
        $response = $this->createASession($this->superAdmin, $this->goldUser);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson([
            'error' => 'you cannot create a session with non approved friends'
        ]);
    }

    /** @test */
    public function cannot_create_chat_session_with_yourself()
    {
        $response = $this->createASession($this->silverUser, $this->silverUser);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson([
            'error' => 'you cannot create a session with yourself'
        ]);
    }

    /** @test */
    public function normal_user_is_not_appearing_in_chat_friends_of_others_when_he_is_friend_with_them()
    {
        /**
         * we know that silver user has 3 friends but one of them is a normal user
         * so instead of 3 friends we must get only 2 friends.
         */
        $url = route('chat.friends');
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->silverUser),
        ]);
        $response->assertJsonCount(2, 'data');
    }


    /** @test */
    public function cannot_create_session_with_a_normal_user()
    {
        $response = $this->createASession($this->silverUser, $this->normalUser);
        $response->assertForbidden();
        $response->assertJson([
            'error' => 'you cannot create a session with a normal user'
        ]);
    }


    /***********************************-----------
     *
     *              Owner Policy                      =======================
     *
     * /**********************************/

    /** @test */
    public function cannot_get_someone_else_chats()
    {
        $this->createASession($this->silverUser, $this->superAdmin);

        $url = route('chat.chats', [1]);
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->goldUser)
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function cannot_send_message_to_someone_else_session()
    {
        $this->createASession($this->silverUser, $this->goldUser);

        $url = route('chat.send', [1]);
        $response = $this->post($url, [
            'token' => $this->getToken($this->superAdmin),
            'body' => 'hi i am a admin in middle of your chat',
        ]);
        $response->assertForbidden();
    }

    /** @test */
    public function cannot_read_someone_else_session()
    {
        $this->createASession($this->silverUser, $this->goldUser);

        $url = route('chat.read', [1]);
        $response = $this->put($url, [
            'token' => $this->getToken($this->superAdmin),
        ]);
        $response->assertForbidden();
    }


    /** @test */
    public function super_admin_can_get_any_session_chats_that_not_belong_to_him()
    {
        $this->createASession($this->silverUser, $this->goldUser);

        $url = route('chat.chats', [1]);
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->superAdmin)
        ]);

        $response->assertOk();
    }

    /** @test */
    public function other_managers_are_forbidden_to_get_any_session_chats_that_not_belong_to_them()
    {
        $this->createASession($this->silverUser, $this->goldUser);

        $url = route('chat.chats', [1]);

        $this->json('get', $url, ['token' => $this->getToken($this->admin)])->assertForbidden();
        $this->json('get', $url, ['token' => $this->getToken($this->networkManager)])->assertForbidden();
        $this->json('get', $url, ['token' => $this->getToken($this->jobManager)])->assertForbidden();
        $this->json('get', $url, ['token' => $this->getToken($this->contactManager)])->assertForbidden();
    }


    /***********************************-----------
     *
     *              Guest                      =======================
     *
     * /**********************************/

    /** @test */
    public function guest_cannot_get_chat_friends()
    {
        $url = route('chat.friends');
        $response = $this->json('get', $url);

        $response->assertUnauthorized();
    }

    /** @test */
    public function guest_cannot_create_session()
    {
        $url = route('chat.session.create', ['user' => 1]);
        $response = $this->json('post', $url);

        $response->assertUnauthorized();
    }

    /** @test */
    public function guest_cannot_send_message()
    {
        /** creating a session between gold and silver */
        $this->createASession($this->silverUser, $this->goldUser);

        $url = route('chat.send', [1]);
        $response = $this->post($url, [
            'body' => 'hi i am a silver user'
        ]);

        $response->assertUnauthorized();
    }

    /** @test */
    public function guest_cannot_get_session_chats()
    {
        /** creating a session between gold and silver */
        $this->createASession($this->silverUser, $this->goldUser);

        $url = route('chat.chats', [1]);
        $response = $this->json('get', $url);

        $response->assertUnauthorized();
    }

    /** @test */
    public function guest_cannot_read_session_chats()
    {
        $this->createASession($this->silverUser, $this->goldUser);

        $url = route('chat.read', [1]);
        $response = $this->put($url);

        $response->assertUnauthorized();
    }




    /***********************************-----------
     *
     *              Normal User                      =======================
     *
     * /**********************************/


    /** @test */
    public function normal_user_cannot_get_chat_friends()
    {
        $url = route('chat.friends');
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->normalUser)
        ]);
        $this->forbiddenNormalUser($response);
    }

    /** @test */
    public function normal_user_cannot_create_session()
    {
        $response = $this->createASession($this->normalUser, $this->silverUser);

        $this->forbiddenNormalUser($response);
    }

    /** @test */
    public function normal_user_cannot_send_message()
    {
        $this->createASession($this->silverUser, $this->goldUser);
        $url = route('chat.send', [1]);
        $response = $this->post($url, [
            'token' => $this->getToken($this->normalUser),
            'body' => 'hi i am a silver user'
        ]);
        $this->forbiddenNormalUser($response);
    }

    /** @test */
    public function normal_user_cannot_get_session_chats()
    {
        $this->createASession($this->silverUser, $this->goldUser);

        $url = route('chat.chats', [1]);
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->normalUser)
        ]);
        $this->forbiddenNormalUser($response);
    }

    /** @test */
    public function normal_user_cannot_read_session_chats()
    {
        /** creating a session between gold and silver */
        $this->createASession($this->silverUser, $this->goldUser);
        $url = route('chat.read', [1]);
        $response = $this->put($url, [
            'token' => $this->getToken($this->normalUser)
        ]);
        $this->forbiddenNormalUser($response);
    }





    /***********************************-----------
     *
     *              Silver User                      =======================
     *
     * /**********************************/


    /** @test */
    public function silver_user_can_get_chat_friends()
    {
        $url = route('chat.friends');
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->silverUser)
        ]);

        $response->assertOk();
    }

    /** @test */
    public function silver_user_can_create_session()
    {
        $response = $this->createASession($this->silverUser, $this->goldUser);

        $response->assertCreated();
    }

    /** @test */
    public function silver_user_can_send_message()
    {
        $this->withoutExceptionHandling();
        $this->createASession($this->silverUser, $this->goldUser);
        $url = route('chat.send', [1]);
        $response = $this->post($url, [
            'token' => $this->getToken($this->silverUser),
            'body' => 'hi i am a silver user'
        ]);

        $response->assertCreated();
    }

    /** @test */
    public function silver_user_can_get_session_chats()
    {
        $this->createASession($this->silverUser, $this->goldUser);

        $url = route('chat.chats', [1]);
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->silverUser)
        ]);

        $response->assertOk();
    }

    /** @test */
    public function silver_user_can_read_session_chats()
    {
        /** creating a session between gold and silver */
        $this->createASession($this->silverUser, $this->goldUser);
        $url = route('chat.read', [1]);
        $response = $this->put($url, [
            'token' => $this->getToken($this->silverUser)
        ]);
        $response->assertStatus(Response::HTTP_ACCEPTED);
    }

    /***********************************-----------
     *
     *              Gold Users                      =======================
     *
     * /**********************************/


    /** @test */
    public function gold_user_can_get_chat_friends()
    {
        $url = route('chat.friends');
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->goldUser)
        ]);

        $response->assertOk();
    }

    /** @test */
    public function gold_user_can_create_session()
    {
        $response = $this->createASession($this->goldUser, $this->silverUser);

        $response->assertCreated();
    }

    /** @test */
    public function gold_user_can_send_message()
    {
        $this->withoutExceptionHandling();
        $this->createASession($this->goldUser, $this->silverUser);
        $url = route('chat.send', [1]);
        $response = $this->post($url, [
            'token' => $this->getToken($this->goldUser),
            'body' => 'hi i am a gold user'
        ]);

        $response->assertCreated();
    }

    /** @test */
    public function gold_user_can_get_session_chats()
    {
        $this->createASession($this->goldUser, $this->silverUser);

        $url = route('chat.chats', [1]);
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->goldUser)
        ]);

        $response->assertOk();
    }

    /** @test */
    public function gold_user_can_read_session_chats()
    {
        $this->createASession($this->goldUser, $this->silverUser);
        $url = route('chat.read', [1]);
        $response = $this->put($url, [
            'token' => $this->getToken($this->goldUser)
        ]);
        $response->assertStatus(Response::HTTP_ACCEPTED);
    }


    /***********************************-----------
     *
     *              Refactors                      =======================
     *
     * /**********************************/

    /**
     * @param $creator
     * @param $user
     * @return TestResponse
     */
    private function createASession($creator, $user)
    {
        $url = route('chat.session.create', ['user' => $user->id]);
        return $this->post($url, [
            'token' => JWTAuth::fromuser($creator),
        ]);
    }


    /**
     * @param TestResponse $response
     */
    private function forbiddenNormalUser(TestResponse $response): void
    {
        $response->assertForbidden();
        $response->assertJson([
            'error' => 'Permission Denied!'
        ]);
    }


}
