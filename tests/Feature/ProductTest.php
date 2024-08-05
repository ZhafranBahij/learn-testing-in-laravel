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

    public function test_admin_edit_product_page(){
        $product = Product::factory()->create();

        // Acting as admin in Edit product
        $response = $this->actingAs($this->admin)->get('/product'.'/'.$product->id.'/edit');

        // Check if admin can access
        $response->assertStatus(200);

        // Check if data in the page
        $response->assertViewHas('data', $product);

        // Check if product data in input
        $response->assertSee('value="'.$product->name.'"', false);
        $response->assertSee('value="'.$product->price.'"', false);
    }

    public function test_admin_update_product_but_failed_in_validation()
    {
        $product = Product::factory()->create();

        $update_product = [
            'name' => '',
            'price' => '',
        ];

        // Acting as admin in update product
        $response = $this->actingAs($this->admin)->put('/product'.'/'.$product->id, $update_product);

        // Check if redirect
        $response->assertStatus(302);

        // Check if any input failed
        $response->assertInvalid(['name', 'price']);
    }

    public function test_admin_update_product_but_success_in_validation()
    {
        $product = Product::factory()->create();

        $update_product = [
            'name' => 'Rabi-Rabi',
            'price' => 30000,
        ];

        // Acting as admin in update product
        $response = $this->actingAs($this->admin)->put('/product'.'/'.$product->id, $update_product);

        // Check if redirect
        $response->assertStatus(302);

        // Check if data in database
        $this->assertDatabaseHas('products', $update_product);
        $selected_product = Product::find($product->id);

        // Check if data same
        $this->assertEquals($update_product['name'], $selected_product->name);
        $this->assertEquals($update_product['price'], $selected_product->price);
    }

    public function test_admin_delete_product_successfull()
    {
        $product = Product::factory()->create();

        // Acting as admin in delete product
        $response = $this->actingAs($this->admin)->delete('/product'.'/'.$product->id);

        // Redirect to product index
        $response->assertStatus(302);
        $response->assertRedirectToRoute('products.index');

        // Check data in database, if data is lost
        $this->assertDatabaseMissing('products', $product->toArray());
        $this->assertDatabaseCount('products', 0);

    }

    // TEST API
    public function test_api_get_all_product_successfull()
    {
        // Create and get all product
        Product::factory()->create();
        $all_product = Product::all();

        $response = $this->getJson('api/product');

        // Checking if all product in that page
        $response->assertJson($all_product->toArray());
    }

    public function test_api_post_product_successfull()
    {

        // Setting semi data product
        $product = [
            'name' => 'PS Portal',
            'price' => 123500,
        ];

        $response = $this->postJson('api/product', $product);

        // Success create will get 201 code
        $response->assertStatus(201);
        $response->assertJson($product);
    }

    public function test_api_post_product_invalid_validation()
    {
        // Setting semi data product
        $product = [
            'name' => '',
            'price' => 123500,
        ];

        $response = $this->postJson('api/product', $product);

        $response->assertStatus(422);
    }

}
