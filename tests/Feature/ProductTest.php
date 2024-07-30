<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Product;
use App\Models\User;
use Tests\TestCase;

class ProductTest extends TestCase
{

    use RefreshDatabase;

    public function test_the_data_doesnt_has_product(): void
    {
        // Example of acting as auth user in some page
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/product');

        $response->assertStatus(200);

        $response->assertSee(__("Nothing in here"));

    }

    /**
     * A basic test example.
     */
    public function test_the_data_has_product(): void
    {

        $user = User::factory()->create();

        $product = Product::factory()->create([
            'name' => 'Shiro',
        ]);

        $response = $this->actingAs($user)->get('/product');

        $response->assertStatus(200);

        $response->assertDontSee("Nothing in here");
        $response->assertSee("Shiro");

        // If product exist in variable "datas"
        $response->assertViewHas('datas', function ($collection) use ($product) {
            return $collection->contains($product);
        });
    }

    public function test_the_data_in_paginate_doesnt_has_11th_data(){
        $user = User::factory()->create();

        $products = Product::factory(11)->create();
        // dd($products);
        $last_product = $products->last();
        // dd($last_product);

        $response = $this->actingAs($user)->get('/product');

        $response->assertStatus(200);

        // If product doesnt exist in variable "datas"
        $response->assertViewHas('datas', function ($collection) use ($last_product) {
            return !$collection->contains($last_product);
        });

    }

}
