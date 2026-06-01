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

class AdminBorrowedLogTest extends TestCase
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
            'email' => 'admin.borrow@example.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'address' => 'Test Address',
            'status' => 'active',
            'is_admin' => true,
        ]);
    }

    private function member(string $status = 'active'): User
    {
        return User::create([
            'name' => 'Member User',
            'email' => uniqid('member') . '@example.com',
            'password' => Hash::make('password'),
            'phone' => '081234567891',
            'address' => 'Member Address',
            'status' => $status,
            'is_admin' => false,
        ]);
    }

    private function copy(string $status = 'available'): BookCopy
    {
        $category = BookCategory::create(['name' => uniqid('Category ')]);
        $book = BookMetadata::create([
            'title' => 'Borrow Test Book',
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

    public function test_create_shows_issue_form_data(): void
    {
        $this->actingAs($this->admin());
        $this->member();
        $this->copy();

        $response = $this->get(route('admin.borrowed-logs.create'));

        $response->assertOk();
        $response->assertViewIs('admin.issue.index');
        $response->assertViewHas(['members', 'availableCopies', 'histories']);
    }

    public function test_store_creates_borrowed_log_and_marks_copy_as_borrowed(): void
    {
        $this->actingAs($this->admin());
        $member = $this->member();
        $copy = $this->copy();

        $response = $this->post(route('admin.borrowed-logs.store'), [
            'user_id' => $member->id,
            'book_copy_id' => $copy->id,
            'borrowed_date' => '2026-06-01',
        ]);

        $response->assertRedirect(route('admin.borrowed-logs.create'));
        $this->assertDatabaseHas('borrowed_logs', [
            'user_id' => $member->id,
            'book_copy_id' => $copy->id,
            'borrowed_date' => '2026-06-01',
            'due_date' => '2026-06-08',
        ]);
        $this->assertDatabaseHas('book_copies', ['id' => $copy->id, 'status' => 'borrowed']);
    }

    public function test_store_rejects_inactive_member(): void
    {
        $this->actingAs($this->admin());
        $member = $this->member('inactive');
        $copy = $this->copy();

        $response = $this->from(route('admin.borrowed-logs.create'))->post(route('admin.borrowed-logs.store'), [
            'user_id' => $member->id,
            'book_copy_id' => $copy->id,
            'borrowed_date' => '2026-06-01',
        ]);

        $response->assertRedirect(route('admin.borrowed-logs.create'));
        $response->assertSessionHasErrors('user_id');
        $this->assertDatabaseMissing('borrowed_logs', ['user_id' => $member->id]);
    }

    public function test_return_form_shows_borrowed_books(): void
    {
        $this->actingAs($this->admin());
        $member = $this->member();
        $copy = $this->copy('borrowed');
        BorrowedLog::create([
            'user_id' => $member->id,
            'book_copy_id' => $copy->id,
            'borrowed_date' => '2026-06-01',
            'due_date' => '2026-06-08',
            'returned_date' => null,
        ]);

        $response = $this->get(route('admin.borrowed-logs.return'));

        $response->assertOk();
        $response->assertViewIs('admin.return.index');
        $response->assertViewHas(['borrowedLogs', 'returnHistories']);
    }

    public function test_return_book_sets_return_date_and_makes_copy_available(): void
    {
        $this->actingAs($this->admin());
        $member = $this->member();
        $copy = $this->copy('borrowed');
        $borrowedLog = BorrowedLog::create([
            'user_id' => $member->id,
            'book_copy_id' => $copy->id,
            'borrowed_date' => '2026-06-01',
            'due_date' => '2026-06-08',
            'returned_date' => null,
        ]);

        $response = $this->post(route('admin.borrowed-logs.return-store'), [
            'borrowed_log_id' => $borrowedLog->id,
            'returned_date' => '2026-06-05',
        ]);

        $response->assertRedirect(route('admin.borrowed-logs.return'));
        $this->assertDatabaseHas('borrowed_logs', ['id' => $borrowedLog->id, 'returned_date' => '2026-06-05']);
        $this->assertDatabaseHas('book_copies', ['id' => $copy->id, 'status' => 'available']);
    }
}
