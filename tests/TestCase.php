<?php

namespace Tests;

use App\Role;
use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $admin;
    protected $translator;
    protected $customer;

    /**
     * @param $user
     * @return mixed
     */
    protected function getToken($user)
    {
        if ($user === null) {
            return null;
        }
        return JWTAuth::fromuser($user);
    }

    protected function createRoles()
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'translator']);
        Role::create(['name' => 'customer']);
    }

    protected function createUsers()
    {
        $this->createRoles();

        $this->admin = factory(User::class)->state('admin')->create();
        $this->translator = factory(User::class)->state('translator')->create();
        $this->customer = factory(User::class)->create();
    }

    protected function banUser($user)
    {
        $url = route('user.ban', ['user' => $user->id]);
        $this->json('put', $url, ['token' => $this->getToken($this->admin)]);
    }




    /***********************************-----------
     *
     *              Validation Global                      =======================
     *
     * /**********************************/


    /**
     * @param $field
     * @param $value
     * @return bool
     */
    protected function validateField($field, $value)
    {
        return Validator::make(
            [$field => $value],
            [$field => $this->rules[$field]]
        )->passes();
    }

    protected function good($field, $value)
    {
        $this->assertTrue($this->validateField($field, $value));
    }

    protected function bad($field, $value)
    {
        $this->assertNotTrue($this->validateField($field, $value));
    }

}
