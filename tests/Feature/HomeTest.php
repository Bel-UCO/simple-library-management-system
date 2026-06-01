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

class HomeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => ':memory:']);
    }

    private function member(): User
    {
        return User::create([
            'name' => 'Member User',
            'email' => uniqid('member') . '@example.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'address' => 'Member Address',
            'status' => 'active',
            'is_admin' => false,
        ]);
    }

    private function book(string $title = 'Home Test Book', ?BookCategory $category = null): BookMetadata
    {
        $category ??= BookCategory::create(['name' => uniqid('Category ')]);

        return BookMetadata::create([
            'title' => $title,
            'author' => 'Test Author',
            'publisher' => 'Test Publisher',
            'year_published' => 2024,
            'isbn' => uniqid(),
            'image' => 'books/test.jpg',
            'language' => 'Indonesia',
            'book_category_id' => $category->id,
            'description' => 'Test description',
        ]);
    }

    public function test_home_shows_book_lists_for_guest(): void
    {
        $this->book();

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertViewIs('home.index');
        $response->assertViewHas(['latestArrivals', 'categories', 'histories', 'books']);
    }

    public function test_home_shows_logged_in_user_history(): void
    {
        $user = $this->member();
        $book = $this->book();
        $copy = BookCopy::create(['book_metadata_id' => $book->id, 'status' => 'borrowed']);
        BorrowedLog::create([
            'user_id' => $user->id,
            'book_copy_id' => $copy->id,
            'borrowed_date' => '2026-06-01',
            'due_date' => '2026-06-08',
            'returned_date' => null,
        ]);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk();
        $response->assertViewHas('histories');
    }

    public function test_home_filters_by_category(): void
    {
        $category = BookCategory::create(['name' => 'Filtered Category']);
        $this->book('Filtered Book', $category);

        $response = $this->get(route('home', ['category' => $category->id]));

        $response->assertOk();
        $response->assertViewHas('selectedCategory', (string) $category->id);
    }
}
