<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => ':memory:']);
    }

    public function test_index_shows_register_page(): void
    {
        $response = $this->get(route('register.index'));

        $response->assertOk();
        $response->assertViewIs('register.index');
    }

    public function test_store_creates_active_member(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'New Member',
            'email' => 'newmember@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '081234567890',
            'address' => 'Test Address',
        ]);

        $response->assertRedirect(route('login.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'name' => 'New Member',
            'email' => 'newmember@example.com',
            'status' => 'active',
            'is_admin' => false,
        ]);
    }

    public function test_store_requires_unique_email(): void
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'same@example.com',
            'password' => 'password123',
            'phone' => '081234567890',
            'address' => 'Test Address',
            'status' => 'active',
            'is_admin' => false,
        ]);

        $response = $this->from(route('register.index'))->post(route('register.store'), [
            'name' => 'New Member',
            'email' => 'same@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '081234567890',
            'address' => 'Test Address',
        ]);

        $response->assertRedirect(route('register.index'));
        $response->assertSessionHasErrors('email');
    }
}
