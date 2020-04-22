<?php

namespace Tests\Feature\Blade;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewsRouteTest extends TestCase
{

    /*
       |--------------------------------------------------------------------------
       | Views Routes
       |--------------------------------------------------------------------------
       |
       | 4 Routes
       |
       |
       |
       */

    /** @test */
    public function home_page()
    {
        $url = route('home');
        $this->get($url)->assertOk();
    }

    /** @test */
    public function about_page()
    {
        $url = route('about');
        $this->get($url)->assertOk();
    }

    /** @test */
    public function contact_page()
    {
        $url = route('contact');
        $this->get($url)->assertOk();
    }

    /** @test */
    public function spa_page()
    {
        $url = route('spa');
        $this->get($url)->assertOk();
    }

}
