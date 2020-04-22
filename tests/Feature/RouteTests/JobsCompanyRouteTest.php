<?php

namespace Tests\Feature\Job;

use App\Models\Job\Company;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class JobsCompanyRouteTest extends TestCase
{
    /***********************************-----------
     *
     *              set Up                      =======================
     *
     * /**********************************/

    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->createUsers();
    }

    /***********************************-----------
     *
     *              Routes: 3                      =======================
     *
     * /**********************************/

    /**
     * #1
     * @test
     */
    public function jobs_company_store()
    {
        /** 1#
         * Creating a Company
         */
        $response = $this->createCompany();


        /** 2#
         * Asserting Created
         */
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment([
            'id' => Company::first()->id,
            'logo' => Storage::disk('public')->url(Company::first()->logo),
            'name' => 'test name',
            'address' => 'test address',
            'description' => 'this is company description',
        ]);
        Storage::disk('public')->assertExists(Company::first()->logo);
        $this->assertCount(1, Company::all());
    }

    /**
     * #2
     * @test
     */
    public function jobs_company_update()
    {

        /** 1#
         * Creating a Company
         */
        $this->createCompany();
        $oldFile = Company::first()->logo; //png


        /** 2#
         * Updating the Company
         */
        $url = route('jobs.company.update', [1]);
        $response = $this->json('put', $url, [
            'token' => $this->getToken($this->goldUser),
            'logo' => UploadedFile::fake()->image('new.jpg', 200, 200)->size(10),
            'name' => 'updated name',
            'address' => 'updated address',
            'description' => 'this is company updated description',
        ]);


        /** 3#
         * Asserting updated
         */
        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJsonFragment([
            'id' => Company::first()->id,
            'logo' => Storage::disk('public')->url(Company::first()->logo),
            'name' => 'updated name',
            'address' => 'updated address',
            'description' => 'this is company updated description',
        ]);
        $newFile = Company::first()->logo; //jpg
        Storage::disk('public')->assertExists($newFile);
        Storage::disk('public')->assertMissing($oldFile);
    }

    /**
     * #3
     * @test
     */
    public function jobs_company_show()
    {
        /** 1#
         * Creating a Company
         */
        $this->createCompany();

        /** 2#
         * Get the Company
         */
        $url = route('jobs.company.show', [1]);
        $response = $this->json('get', $url);


        /** 3#
         * Asserting Show
         */
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => Company::first()->id,
            'logo' => Storage::disk('public')->url(Company::first()->logo),
            'name' => 'test name',
            'address' => 'test address',
            'description' => 'this is company description',
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
    private function createCompany()
    {
        $url = route('jobs.company.store');
        return $this->json('post', $url, [
            'token' => $this->getToken($this->goldUser),
            'logo' => UploadedFile::fake()->image('logo.png', 150, 150)->size(100),
            'name' => 'test name',
            'address' => 'test address',
            'description' => 'this is company description',
        ]);
    }


}
