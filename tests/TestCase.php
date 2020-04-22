<?php

namespace Tests;

use App\Models\Network\Group;
use App\User;
use App\User\Role;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $normalUser;
    protected $silverUser;
    protected $goldUser;

    protected $superAdmin;
    protected $admin;
    protected $networkManager;
    protected $jobManager;
    protected $contactManager;

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
        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'network_manager']);
        Role::create(['name' => 'job_manager']);
        Role::create(['name' => 'contact_manager']);

        Role::create(['name' => 'gold']);
        Role::create(['name' => 'silver']);
        Role::create(['name' => 'normal']);
    }

    protected function createUsers()
    {
        $this->createRoles();

        $this->normalUser = factory(User::class)->create();
        $this->silverUser = factory(User::class)->state('silver')->create();
        $this->goldUser = factory(User::class)->state('gold')->create();

        $this->superAdmin = factory(User::class)->state('super_admin')->create();
        $this->admin = factory(User::class)->state('admin')->create();
        $this->networkManager = factory(User::class)->state('network_manager')->create();
        $this->jobManager = factory(User::class)->state('job_manager')->create();
        $this->contactManager = factory(User::class)->state('contact_manager')->create();
    }


    protected function storeGroup($user)
    {
        $url = route('network.group.store');
        return $this->json('post', $url, [
            'token' => $this->getToken($user),
            'username' => 'group_username',
            'name' => 'test group',
            'about' => 'here is a description about the group',
        ]);
    }

    protected function banUser($user)
    {
        $url = route('user.ban', ['user' => $user->id]);
        $this->json('put', $url, ['token' => $this->getToken($this->superAdmin)]);
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
