<?php

namespace Tests\Feature;

use App\Models\BookCategory;
use App\Models\BookCopy;
use App\Models\BookMetadata;
use App\Models\BorrowedLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => ':memory:']);
    }

    private function user(array $attributes = []): User
    {
        return User::create(array_merge([
            'name' => 'Member User',
            'email' => uniqid('user') . '@example.com',
            'password' => Hash::make('password123'),
            'phone' => '081234567890',
            'address' => 'Member Address',
            'status' => 'active',
            'is_admin' => false,
        ], $attributes));
    }

    private function bookCopy(string $status = 'borrowed'): BookCopy
    {
        $category = BookCategory::create(['name' => 'Login Category']);
        $book = BookMetadata::create([
            'title' => 'Overdue Book',
            'author' => 'Test Author',
            'publisher' => 'Test Publisher',
            'year_published' => 2024,
            'isbn' => uniqid(),
            'image' => 'books/test.jpg',
            'language' => 'Indonesia',
            'book_category_id' => $category->id,
            'description' => 'Test description',
        ]);

        return BookCopy::create([
            'book_metadata_id' => $book->id,
            'status' => $status,
        ]);
    }

    public function test_index_shows_login_page(): void
    {
        $response = $this->get(route('login.index'));

        $response->assertOk();
        $response->assertViewIs('login.index');
    }

    public function test_active_user_can_login(): void
    {
        $user = $this->user(['email' => 'active@example.com']);

        $response = $this->post(route('login.authenticate'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = $this->user([
            'email' => 'inactive@example.com',
            'status' => 'inactive',
        ]);

        $response = $this->from(route('login.index'))->post(route('login.authenticate'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('login.index'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_with_overdue_book_is_suspended_on_login(): void
    {
        $user = $this->user(['email' => 'overdue@example.com']);
        $copy = $this->bookCopy();
        BorrowedLog::create([
            'user_id' => $user->id,
            'book_copy_id' => $copy->id,
            'borrowed_date' => '2026-05-01',
            'due_date' => '2026-05-08',
            'returned_date' => null,
        ]);

        $response = $this->post(route('login.authenticate'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('login.index'));
        $response->assertSessionHasErrors('email');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'suspended']);
        $this->assertGuest();
    }

    public function test_setting_shows_password_setting_page(): void
    {
        $user = $this->user();

        $response = $this->actingAs($user)->get(route('login.setting'));

        $response->assertOk();
        $response->assertViewIs('login.setting');
    }

    public function test_update_password_changes_password(): void
    {
        $user = $this->user(['email' => 'password@example.com']);

        $response = $this->actingAs($user)->post(route('login.update-password'), [
            'current_password' => 'password123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('login.setting'));
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_logout_logs_user_out(): void
    {
        $user = $this->user();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login.index'));
        $this->assertGuest();
    }
}
