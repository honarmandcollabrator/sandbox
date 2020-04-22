<?php

namespace Tests\Feature\Pay;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentRouteTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->createUsers();
    }

    /*
       |--------------------------------------------------------------------------
       | Payment Routes
       |--------------------------------------------------------------------------
       |
       | 2 Routes
       |
       |
       |
       */


    /** @test */
    public function pay()
    {
        //todo: design it
        $this->assertTrue(true);
    }


    /** @test */
    public function pay_callback()
    {
        //todo: design it
        $this->assertTrue(true);
    }
}
