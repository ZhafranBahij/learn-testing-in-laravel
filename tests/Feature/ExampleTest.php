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

    public function test_the_application_doesnt_has_symfony_text(): void
    {
        $response = $this->get('/');

        $response->assertDontSeeText("Symfony");

        $response->assertStatus(200);
    }

    public function test_the_application_has_laravel_text(): void
    {
        $response = $this->get('/');

        $response->assertSeeText("Laravel");

        $response->assertStatus(200);
    }
}
