<?php

namespace Tests\Feature;

use App\Models\BookCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BookCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => ':memory:']);
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'address' => 'Test Address',
            'status' => 'active',
            'is_admin' => true,
        ]);
    }

    public function test_index_shows_categories(): void
    {
        $this->actingAs($this->admin());
        BookCategory::create(['name' => 'Science']);

        $response = $this->get(route('categories.list'));

        $response->assertOk();
        $response->assertViewIs('categories.index');
        $response->assertViewHas('categories');
    }

    public function test_store_creates_category(): void
    {
        $this->actingAs($this->admin());

        $response = $this->post(route('categories.store'), [
            'name' => 'History',
        ]);

        $response->assertRedirect(route('categories.list'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('book_categories', ['name' => 'History']);
    }

    public function test_update_changes_category_name(): void
    {
        $this->actingAs($this->admin());
        $category = BookCategory::create(['name' => 'Old Name']);

        $response = $this->post(route('categories.update', $category->id), [
            'name' => 'New Name',
        ]);

        $response->assertRedirect(route('categories.list'));
        $this->assertDatabaseHas('book_categories', [
            'id' => $category->id,
            'name' => 'New Name',
        ]);
    }

    public function test_destroy_deletes_category(): void
    {
        $this->actingAs($this->admin());
        $category = BookCategory::create(['name' => 'Temporary']);

        $response = $this->post(route('categories.destroy', $category->id));

        $response->assertRedirect(route('categories.list'));
        $this->assertDatabaseMissing('book_categories', ['id' => $category->id]);
    }
}
