<?php

namespace Tests\Feature\RouteTests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Storage;
use Tests\TestCase;

class OrderRouteTests extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->createUsers();

        Storage::fake('public');
    }

}
