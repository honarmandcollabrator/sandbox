<?php

namespace Tests\Feature\FunctionalityTests;

use App\Models\Network\Group;
use App\Models\Network\Timeline;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class NetworkGroupRouteTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->createUsers();
    }


    /*
       |--------------------------------------------------------------------------
       | Group
       |--------------------------------------------------------------------------
       |
       | Routes = 5
       |
       |
       |
       */


    /** #1
     * @test
     */
    public function network_group_store()
    {
        $response = $this->storeAGroup();
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertCount(1, Group::all());
        $this->assertCount(9, Timeline::all());
        Storage::disk('public')->assertExists(Group::first()->avatar);
        $response->assertJsonFragment([
            'name' => 'test group',
            'username' => 'username',
            'avatar' => Storage::disk('public')->url(Group::first()->avatar),
            'about' => 'here is a description about the group',
            'admin_id' => $this->goldUser->id,
        ]);
        /*===== Automatically joined to stored group =====*/
        $this->assertCount(1, $this->goldUser->groups);
    }


    /** #2
     * @test
     */

    public function network_group_show()
    {
        $this->storeAGroup();
        $this->withoutExceptionHandling();

        $url = route('network.group.show', [1]);
        $response = $this->json('get', $url, ['token' => $this->getToken($this->normalUser)]);

        $response->assertOk();
        $response->assertJsonFragment([
            'name' => 'test group',
            'username' => 'username',
            'avatar' => Storage::disk('public')->url(Group::first()->avatar),
            'about' => 'here is a description about the group',
            'admin_id' => $this->goldUser->id,
        ]);
    }


    /** #3
     * @test
     */
    public function network_group_update()
    {
        $this->storeAGroup();
        $oldFile = Group::first()->avatar;

        $url = route('network.group.update', [1]);
        $response = $this->json('put', $url, [
            'token' => $this->getToken($this->goldUser),
            'name' => 'update group',
            'username' => 'aUsername',
            'avatar' => UploadedFile::fake()->image('image.jpeg', 200, 200)->size(15),
            'about' => 'here is an updated description',
        ]);


        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJsonFragment([
            'name' => 'update group',
            'avatar' => Storage::disk('public')->url(Group::first()->avatar),
            'about' => 'here is an updated description',
            'admin_id' => $this->goldUser->id,
        ]);
        Storage::disk('public')->assertExists(Group::first()->avatar);
        Storage::disk('public')->assertMissing($oldFile);
    }


    /** #4
     * @test
     */
    public function network_group_destroy()
    {
        $this->storeAGroup();
        $oldFile = Group::first()->avatar;


        $url = route('network.group.destroy', [1]);
        $response = $this->json('delete', $url, [
            'token' => $this->getToken($this->goldUser),
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertCount(0, Group::all());
        $this->assertCount(8, Timeline::all());
        Storage::disk('public')->assertMissing($oldFile);
    }


    /** @test 5*/
    public function network_group_index()
    {
        $url = route('network.group.index');
        $response = $this->json('get', $url, ['token' => $this->getToken($this->superAdmin)]);
        $response->assertOk();
        $response->assertJsonStructure([
            'data', 'links', 'meta'
        ]);
    }





    /***********************************-----------
     *
     *              Refactors                      =======================
     *
     * /**********************************/


    /**
     * @return TestResponse
     */
    private function storeAGroup(): TestResponse
    {
        $url = route('network.group.store');
        return $this->json('post', $url, [
            'token' => $this->getToken($this->goldUser),
            'name' => 'test group',
            'username' => 'username',
            'avatar' => UploadedFile::fake()->image('image.png', 100, 100)->size(10),
            'about' => 'here is a description about the group',
        ]);
    }

}
