<?php

namespace Tests\Feature;

use App\Models\BookCategory;
use App\Models\BookCopy;
use App\Models\BookMetadata;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookDetailTest extends TestCase
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
            'email' => 'admin.book@example.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'address' => 'Test Address',
            'status' => 'active',
            'is_admin' => true,
        ]);
    }

    private function book(string $title = 'Test Book'): BookMetadata
    {
        $category = BookCategory::first() ?? BookCategory::create(['name' => 'Novel']);

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

    public function test_create_shows_book_form(): void
    {
        $this->actingAs($this->admin());
        BookCategory::create(['name' => 'Novel']);

        $response = $this->get(route('book.create'));

        $response->assertOk();
        $response->assertViewIs('book.form');
        $response->assertViewHas('categories');
    }

    public function test_store_creates_book_and_first_copy(): void
    {
        $this->actingAs($this->admin());
        Storage::fake('public');
        $category = BookCategory::create(['name' => 'Programming']);

        $response = $this->post(route('book.store'), [
            'title' => 'Laravel Basic',
            'author' => 'Test Author',
            'publisher' => 'Test Publisher',
            'year_published' => 2024,
            'isbn' => '9781234567890',
            'image' => UploadedFile::fake()->image('cover.jpg'),
            'language' => 'Indonesia',
            'book_category_id' => $category->id,
            'description' => 'Book description',
        ]);

        $book = BookMetadata::where('title', 'Laravel Basic')->firstOrFail();

        $response->assertRedirect(route('book.show', $book->id));
        $this->assertDatabaseHas('book_metadata', ['title' => 'Laravel Basic']);
        $this->assertDatabaseHas('book_copies', ['book_metadata_id' => $book->id, 'status' => 'available']);
        Storage::disk('public')->assertExists($book->image);
    }

    public function test_show_displays_book_detail(): void
    {
        $book = $this->book();
        BookCopy::create(['book_metadata_id' => $book->id, 'status' => 'available']);

        $response = $this->get(route('book.show', $book->id));

        $response->assertOk();
        $response->assertViewIs('book.index');
        $response->assertViewHas(['book', 'copies']);
    }

    public function test_edit_metadata_shows_book_form(): void
    {
        $this->actingAs($this->admin());
        $book = $this->book();

        $response = $this->get(route('book.update-metadata', $book->id));

        $response->assertOk();
        $response->assertViewIs('book.form');
        $response->assertViewHas(['book', 'categories']);
    }

    public function test_update_metadata_changes_book_and_deletes_unused_old_category(): void
    {
        $this->actingAs($this->admin());
        $oldCategory = BookCategory::create(['name' => 'Old Category']);
        $newCategory = BookCategory::create(['name' => 'New Category']);
        $book = BookMetadata::create([
            'title' => 'Before Update',
            'author' => 'Old Author',
            'publisher' => 'Old Publisher',
            'year_published' => 2020,
            'isbn' => '12345',
            'image' => 'books/old.jpg',
            'language' => 'Indonesia',
            'book_category_id' => $oldCategory->id,
            'description' => 'Old description',
        ]);

        $response = $this->post(route('book.update-metadata.save', $book->id), [
            'title' => 'After Update',
            'author' => 'New Author',
            'publisher' => 'New Publisher',
            'year_published' => 2024,
            'isbn' => '67890',
            'language' => 'English',
            'book_category_id' => $newCategory->id,
            'description' => 'New description',
        ]);

        $response->assertRedirect(route('book.show', $book->id));
        $this->assertDatabaseHas('book_metadata', [
            'id' => $book->id,
            'title' => 'After Update',
            'book_category_id' => $newCategory->id,
        ]);
        $this->assertDatabaseMissing('book_categories', ['id' => $oldCategory->id]);
    }

    public function test_create_copy_adds_available_copy(): void
    {
        $this->actingAs($this->admin());
        $book = $this->book();

        $response = $this->post(route('book.copy.store', $book->id));

        $response->assertRedirect(route('book.show', $book->id));
        $this->assertDatabaseHas('book_copies', [
            'book_metadata_id' => $book->id,
            'status' => 'available',
        ]);
    }

    public function test_update_copy_changes_status(): void
    {
        $this->actingAs($this->admin());
        $book = $this->book();
        $copy = BookCopy::create(['book_metadata_id' => $book->id, 'status' => 'available']);

        $response = $this->post(route('book.copy.update', $copy->id), [
            'status' => 'lost',
        ]);

        $response->assertRedirect(route('book.show', $book->id));
        $this->assertDatabaseHas('book_copies', [
            'id' => $copy->id,
            'status' => 'lost',
        ]);
    }
}
