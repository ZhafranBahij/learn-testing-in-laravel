<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_redirect_to_dashboard(){
        $user = User::factory()->create([
            'email' => 'wedhus@mail.com',
            'password' => 'wedhus123',
            'name' => 'Wedhus',
        ]);

        $response = $this->post('/login', [
            'email' => 'wedhus@mail.com',
            'password' => 'wedhus123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
    }

    public function test_guest_cannot_access_product(){
        $response = $this->get('/product');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
}
