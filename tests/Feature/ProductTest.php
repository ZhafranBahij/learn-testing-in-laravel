<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Product;
use App\Models\User;
use Tests\TestCase;

class ProductTest extends TestCase
{

    use RefreshDatabase;

    private User $user;
    private User $admin;

    // Same as constructor in another language
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->admin = User::factory()->create([
            'is_admin' => true,
        ]);
    }

    public function test_the_data_doesnt_has_product(): void
    {
        $response = $this->actingAs($this->user)->get('/product');

        $response->assertStatus(200);

        $response->assertSee(__("Nothing in here"));
    }

    public function test_the_data_has_product(): void
    {

        $product = Product::factory()->create([
            'name' => 'Shiro',
        ]);

        $response = $this->actingAs($this->user)->get('/product');

        $response->assertStatus(200);

        $response->assertDontSee("Nothing in here");
        $response->assertSee("Shiro");

        // If product exist in variable "datas"
        $response->assertViewHas('datas', function ($collection) use ($product) {
            return $collection->contains($product);
        });
    }

    public function test_the_data_in_paginate_doesnt_has_11th_data(){
        $products = Product::factory(11)->create();
        $last_product = $products->last();

        $response = $this->actingAs($this->user)->get('/product');

        $response->assertStatus(200);

        // If product doesnt exist in variable "datas"
        $response->assertViewHas('datas', function ($collection) use ($last_product) {
            return !$collection->contains($last_product);
        });

    }

    public function test_admin_can_see_create_product_button(){
        $response = $this->actingAs($this->admin)->get('/product');
        $response->assertStatus(200);
        $response->assertSee('Add New Product');
    }

    public function test_normal_user_cannot_see_create_product_button(){
        $response = $this->actingAs($this->user)->get('/product');
        $response->assertStatus(200);
        $response->assertDontSee('Add New Product');
    }

    public function test_admin_can_access_create_product(){
        $response = $this->actingAs($this->admin)->get('/product/create');
        $response->assertStatus(200);
    }

    public function test_normal_user_cannot_access_create_product(){
        $response = $this->actingAs($this->user)->get('/product/create');
        $response->assertStatus(403);
    }

    public function test_admin_create_product(){
        $product = [
            'name' => 'Miniature 123',
            'price' => 12345,
        ];

        // Acting to create data
        $response = $this->actingAs($this->admin)->post('/product', $product);

        // Check if data redirect
        $response->assertStatus(302);
        $response->assertRedirect('/product');

        // Check if data in database
        $this->assertDatabaseHas('products', $product);
        $last_product = Product::latest()->first(['name', 'price'])->toArray();
        $this->assertEquals($product, $last_product);
    }

}
