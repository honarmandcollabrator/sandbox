<?php

namespace Tests\Feature\Contact;

use App\Models\Contact\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class ContactRouteTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createUsers();
    }


    /*
       |--------------------------------------------------------------------------
       | Contact Route
       |--------------------------------------------------------------------------
       |
       | 5 routes
       |
       |
       |
       */

    /** #1
     * @test
     */
    public function contact_with_manager()
    {
        /*===== 1- send contact to manager =====*/
        $url = route('contact.with.manager');
        $response = $this->json('post', $url, [
            'token' => $this->getToken($this->normalUser),
            'message' => 'hello manager i need help'
        ]);

        /*===== 2- Assert =====*/
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertEquals('user', Contact::first()->type);
        $this->assertCount(1, Contact::all());
        $response->assertJsonFragment([
            'name' => $this->normalUser->name,
            'message' => 'hello manager i need help'
        ]);
        $response->assertDontSee('type');
    }


    /** #2
     * @test
     */
    public function contact_with_users()
    {
        /*===== 1- Send contact to user with contact manager =====*/
        $url = route('contact.with.users');
        $response = $this->json('post', $url, [
            'token' => $this->getToken($this->contactManager),
            'message' => 'hi users here a link to new job'
        ]);

        /*===== 2- Assert =====*/
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertEquals('manager', Contact::first()->type);
        $this->assertCount(1, Contact::all());
        $response->assertJsonFragment([
            'name' => $this->contactManager->name,
            'message' => 'hi users here a link to new job'
        ]);
        $response->assertDontSee('type');
    }

    /** #3
     * @test
     */
    public function contact_destroy()
    {
        /*===== 1- We need to store contact in order to update it =====*/
        $this->storeContactWithUsers();

        /*===== 2- Try to delete the first contact =====*/
        $url = route('contact.destroy', [1]);
        $response = $this->json('delete', $url, [
            'token' => $this->getToken($this->superAdmin),
        ]);

        /*===== 3- Assert =====*/
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertCount(0, Contact::all());
    }




    /** #5
     * @test
     */
    public function contact_users_messages()
    {
        /*===== 1- We create two contacts to manager =====*/
        $this->storeContactWithManager();
        $this->storeContactWithManager();

        /*===== 2- Get contact messages with a manager =====*/
        $url = route('contact.users.messages');
        $response = $this->json('get', $url, ['token' => $this->getToken($this->contactManager)]);

        /*===== 3- Assert =====*/
        $this->assertCount(2, Contact::all());
        $response->assertJsonCount(2, 'data');
        $response->assertOk();
    }


    /** #4
     * @test
     */
    public function contact_manager_messages()
    {
        $this->withoutExceptionHandling();
        /*===== 1- We create two contacts to users =====*/
        $this->storeContactWithUsers();
        $this->storeContactWithUsers();

        /*===== 2- We get these admin contacts with a normal user =====*/
        $url = route('contact.manager.messages');
        $response = $this->json('get', $url, ['token' => $this->getToken($this->normalUser)]);

        /*===== 3- Assert =====*/
        $this->assertCount(2, Contact::all());
        $response->assertJsonCount(2, 'data');
        $response->assertOk();
    }




    /***********************************-----------
     *
     *              Refactors           =======================
     *
     * /**********************************/


    /**
     * @return string
     */
    private function storeContactWithUsers()
    {
        $url = route('contact.with.users');
        $this->json('post', $url, [
            'token' => $this->getToken($this->contactManager),
            'message' => 'hi users here a link to new job'
        ]);
    }

    /**
     * @return string
     */
    private function storeContactWithManager()
    {
        $url = route('contact.with.manager');
        $this->json('post', $url, [
            'token' => $this->getToken($this->normalUser),
            'message' => 'hello manager i need help'
        ]);
    }


}
