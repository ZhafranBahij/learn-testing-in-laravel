<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_the_application_dont_have_symfony_text(): void
    {
        $response = $this->get('/');

        $response->assertDontSeeText("Symfony");

        $response->assertStatus(200);
    }
}
