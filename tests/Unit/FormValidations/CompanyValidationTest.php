<?php

namespace Tests\Unit\Job;

use App\Http\Requests\Job\CompanyRequest;
use App\Models\Job\Company;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class CompanyValidationTest extends TestCase
{
    protected $rules;

    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->createUsers();

        $this->rules = (new CompanyRequest())->rules();
    }


    /***********************************-----------
     *
     *              Validation           =======================
     *
     * /**********************************/

    /** @test */
    public function valid_company_name()
    {
        $this->bad('name', '');
        $this->bad('name', 123);
        $this->bad('name', str_repeat('a', 51));
        /*===========*/
        $this->good('name', str_repeat('a', 50));
    }

    /** @test */
    public function valid_company_address()
    {
        $this->bad('address', '');
        $this->bad('address', 123);
        $this->bad('address', str_repeat('a', 4));
        $this->bad('address', str_repeat('a', 71));
        /*===========*/
    }

    /** @test */
    public function valid_company_description()
    {
        $this->bad('description', '');
        $this->bad('description', 123);
        $this->bad('description', str_repeat('a', 9));
        $this->bad('description', str_repeat('a', 501));
        /*===========*/
    }


    /** @test */
    public function valid_company_logo()
    {
        $this->bad('logo', UploadedFile::fake()->create('doc.doc')->size(200));
        $this->bad('logo', UploadedFile::fake()->create('video.mp4')->size(200));
        $this->bad('logo', UploadedFile::fake()->image('image.jpg', 200, 200)->size(401));
        $this->bad('logo', UploadedFile::fake()->image('image.jpg', 50, 50)->size(200));
        $this->bad('logo', UploadedFile::fake()->image('image.jpg', 600, 400)->size(200));
        $this->bad('logo', UploadedFile::fake()->image('image.jpg', 3500, 3500)->size(200));
        /*===========*/
        $this->good('logo', UploadedFile::fake()->image('image.jpg', 200, 200)->size(200));
        $this->good('logo', UploadedFile::fake()->image('image.jpg', 300, 300)->size(200));
    }


    /** @test */
    public function logo_is_required_for_storing_company()
    {
        $url = route('jobs.company.store');
        $response = $this->json('post', $url, [
            'token' => $this->getToken($this->goldUser),
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonStructure(['errors' => ['logo']]);
    }

    /** @test */
    public function logo_is_not_required_for_updating_company()
    {
        factory(Company::class)->create(['user_id' => $this->goldUser->id]);


        $url = route('jobs.company.update', [1]);
        $response = $this->json('put', $url, [
            'token' => $this->getToken($this->goldUser),
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertDontSee('logo');
    }


}
