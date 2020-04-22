<?php

namespace Tests\Feature\Contact;

use App\Models\Contact\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class ContactSecurityTest extends TestCase
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
       | JWT
       |--------------------------------------------------------------------------
       |
       | Testing JWT
       |
       |
       |
       */

    /** @test */
    public function contact_security_jwt()
    {
        factory(Contact::class)->create(['user_id' => $this->normalUser->id]);

        $this->tryContactWithManager(null)->assertUnauthorized();
        $this->tryContactWithUsers(null)->assertUnauthorized();
        $this->getManagerMessages(null)->assertUnauthorized();
        $this->getUsersMessages(null)->assertUnauthorized();
        $this->destroyContact(null)->assertUnauthorized();
    }

    /*
       |--------------------------------------------------------------------------
       | Roles
       |--------------------------------------------------------------------------
       |
       | CanManageContacts
       |
       |
       |
       */

    /** @test */
    public function correct_user_roles_can_manage_contacts()
    {
        factory(Contact::class)->create(['user_id' => $this->normalUser->id]);

        $this->tryContactWithUsers($this->normalUser)->assertForbidden();
        $this->getUsersMessages($this->normalUser)->assertForbidden();
        $this->getUsersMessages($this->jobManager)->assertForbidden();
        $this->destroyContact($this->normalUser)->assertForbidden();
        $this->destroyContact($this->networkManager)->assertForbidden();


        $this->getUsersMessages($this->superAdmin)->assertOk();
        $this->tryContactWithManager($this->normalUser)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->getManagerMessages($this->normalUser)->assertOk();
        $this->destroyContact($this->contactManager)->assertStatus(Response::HTTP_NO_CONTENT);
    }


    /***********************************-----------
     *
     *              Refactors           =======================
     *
     * /**********************************/

    private function tryContactWithManager($user)
    {
        $url = route('contact.with.manager');
        return $this->json('post', $url, ['token' => $this->getToken($user)]);
    }

    private function tryContactWithUsers($user)
    {
        $url = route('contact.with.users');
        return $this->json('post', $url, ['token' => $this->getToken($user)]);
    }

    private function destroyContact($user)
    {
        $url = route('contact.destroy', [1]);
        return $this->json('delete', $url, ['token' => $this->getToken($user)]);
    }

    private function getUsersMessages($user)
    {
        $url = route('contact.users.messages');
        return $this->json('get', $url, ['token' => $this->getToken($user)]);
    }

    private function getManagerMessages($user)
    {
        $url = route('contact.manager.messages');
        return $this->json('get', $url, ['token' => $this->getToken($user)]);
    }


}
