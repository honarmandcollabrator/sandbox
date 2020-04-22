<?php

namespace Tests\Feature\Chat;

use App\Models\Chat\Chat;
use App\Models\Chat\Message;
use App\Models\Chat\Session;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ChatRouteTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->createUsers();
        /**
         * Creating two friends for normal user
         */
        $this->silverUser->request_recipients()->attach([$this->goldUser->id => ['status' => 'approved']]);
        $this->silverUser->request_senders()->attach([$this->superAdmin->id => ['status' => 'approved']]);
    }


    /***********************************-----------
     *
     *              Routes : 5                     =======================
     *
     * /**********************************/

    /**
     * #1
     * @test
     */
    public function chat_friends()
    {
        $this->withoutExceptionHandling();
        /** there is already two friends for user1 in set up */

        $url = route('chat.friends');
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->silverUser)
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(2, 'data');
    }

    /**
     * #2
     * @test
     */
    public function chat_session_create()
    {
        $url = route('chat.session.create', ['user' => 2]);
        $response = $this->post($url, [
            'token' => $this->getToken($this->goldUser)
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertCount(1, Session::all());
        $response->assertJson([
            'data' => [
                'id' => Session::first()->id,
                'users' => [
                    $this->goldUser->id,
                    $this->silverUser->id,
                ]
            ]
        ]);
    }

    /**
     * #3
     * @test
     */
    public function chat_send()
    {
        $url = route('chat.session.create', [2]);
        $this->post($url, [
            'token' => $this->getToken($this->goldUser)
        ]);
        $url = route('chat.send', [1]);
        $response = $this->post($url, [
            'token' => $this->getToken($this->goldUser),
            'body' => 'test message',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment([
            'id' => Message::first()->id,
            'message' => 'test message',
            'type' => 'sent',
            'read_at' => null,
            'send_at' => Message::first()->created_at->diffForHumans()
        ]);
        $this->assertCount(2, Chat::all());
    }


    /**
     * #4
     * @test
     */
    public function chat_chats()
    {
        $url = route('chat.session.create', [2]);
        $this->post($url, [
            'token' => $this->getToken($this->goldUser)
        ]);
        /** adding 4 messages to session*/
        $url = route('chat.send', [1]);
        $this->post($url, ['token' => $this->getToken($this->goldUser), 'body' => 'test message 1']);
        $this->post($url, ['token' => $this->getToken($this->goldUser), 'body' => 'test message 2']);
        $this->post($url, ['token' => $this->getToken($this->silverUser), 'body' => 'test message 3']);
        $this->post($url, ['token' => $this->getToken($this->silverUser), 'body' => 'test message 4']);
        $url = route('chat.chats', [1]);
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->goldUser)
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(4, 'data');

        $response->assertJsonFragment(['message' => 'test message 1']);
        $response->assertJsonFragment(['message' => 'test message 2']);
        $response->assertJsonFragment(['message' => 'test message 3']);
        $response->assertJsonFragment(['message' => 'test message 4']);
    }


    /**
     * #5
     * @test
     */
    public function chat_read()
    {
        $url = route('chat.session.create', [2]);
        $this->post($url, [
            'token' => $this->getToken($this->goldUser)
        ]);
        /** adding 4 messages to session*/
        $url = route('chat.send', [1]);
        $this->post($url, ['token' => $this->getToken($this->goldUser), 'body' => 'test message 1']);
        $this->post($url, ['token' => $this->getToken($this->goldUser), 'body' => 'test message 2']);
        $this->post($url, ['token' => $this->getToken($this->silverUser), 'body' => 'test message 3']);
        $this->post($url, ['token' => $this->getToken($this->silverUser), 'body' => 'test message 4']);
        $url = route('chat.chats', [1]);
        $response = $this->json('get', $url, [
            'token' => $this->getToken($this->goldUser)
        ]);

        $response->assertJsonFragment(['read_at' => null]);

        $url = route('chat.read', ['session' => 1]);
        $response = $this->put($url, [
            'token' => $this->getToken($this->silverUser)
        ]);

        $response->assertJsonMissing(['read_at' => null]);
    }

}
