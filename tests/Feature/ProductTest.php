<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Product;
use Tests\TestCase;

class ProductTest extends TestCase
{

    public function test_the_data_doesnt_has_product(): void
    {
        $response = $this->get('/product');

        $response->assertStatus(200);

        $response->assertSee(__("Nothing in here"));

    }

    /**
     * A basic test example.
     */
    public function test_the_data_has_product(): void
    {

        Product::factory()->create();

        $response = $this->get('/product');

        $response->assertStatus(200);

        $response->assertDontSee("Nothing in here");

    }
}
