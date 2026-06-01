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

class MemberTest extends TestCase
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
            'email' => 'admin.member@example.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'address' => 'Test Address',
            'status' => 'active',
            'is_admin' => true,
        ]);
    }

    private function member(): User
    {
        return User::create([
            'name' => 'Member User',
            'email' => uniqid('member') . '@example.com',
            'password' => Hash::make('password'),
            'phone' => '081234567891',
            'address' => 'Member Address',
            'status' => 'active',
            'is_admin' => false,
        ]);
    }

    private function copy(): BookCopy
    {
        $category = BookCategory::create([
            'name' => uniqid('Member Category '),
        ]);

        $book = BookMetadata::create([
            'title' => 'Member Book',
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
            'status' => 'borrowed',
        ]);
    }

    public function test_index_shows_members(): void
    {
        $this->actingAs($this->admin());
        $this->member();

        $response = $this->get(route('admin.members.index'));

        $response->assertOk();
        $response->assertViewIs('admin.member.index');
        $response->assertViewHas(['members', 'selectedMember', 'histories', 'searchBy', 'keyword']);
    }

    public function test_show_displays_selected_member_history(): void
    {
        $this->actingAs($this->admin());

        $member = $this->member();
        $copy = $this->copy();

        BorrowedLog::create([
            'user_id' => $member->id,
            'book_copy_id' => $copy->id,
            'borrowed_date' => '2026-06-01',
            'due_date' => '2026-06-08',
            'returned_date' => null,
        ]);

        $response = $this->get(route('admin.members.show', $member->id));

        $response->assertOk();
        $response->assertViewIs('admin.member.index');
        $response->assertViewHas('selectedMember');
        $response->assertViewHas('histories');
    }

    public function test_update_status_changes_member_status(): void
    {
        $this->actingAs($this->admin());
        $member = $this->member();

        $response = $this->from(route('admin.members.index'))->post(route('admin.members.update-status', $member->id), [
            'status' => 'suspended',
        ]);

        $response->assertRedirect(route('admin.members.index'));
        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'status' => 'suspended',
        ]);
    }
}