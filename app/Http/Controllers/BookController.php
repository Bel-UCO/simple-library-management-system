<?php

namespace App\Http\Controllers;

use App\Models\BookMetadata;
use App\Models\BookCopy;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    // 
    public function create()
    {
        // Get all categories for the dropdown
        $categories = BookCategory::orderBy('name')->get();

        return view('book.form', compact('categories'));
    }

    // 
    public function store(Request $request)
    {
        // Validate book input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'year_published' => 'nullable|integer|min:1000|max:' . date('Y'),
            'isbn' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'language' => 'nullable|string|max:255',
            'book_category_id' => 'required|exists:book_categories,id',
            'description' => 'nullable|string',
        ]);

        // Store uploaded image to storage/app/public/books
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('books', 'public');
        }

        // Create new book metadata
        $book = BookMetadata::create($validated);

        // Create the first available book copy
        BookCopy::create([
            'book_metadata_id' => $book->id,
            'status' => 'available',
        ]);

        return redirect()
            ->route('book.show', $book->id)
            ->with('success', 'Book created successfully.');
    }

    // 
    public function show($id)
    {
        // Logic to display details of a specific book

        // Fetch the book metadata along with its category and copies
        $book = BookMetadata::with('bookCategory', 'bookCopies')->findOrFail($id);

        $copies = BookCopy::where('book_metadata_id', $id)
            ->latest()
            ->get();

        return view('book.index', compact('book', 'copies'));
    }

    // 
    public function editMetadata($id)
    {
        // Fetch the book metadata
        $book = BookMetadata::findOrFail($id);

        // Get all categories for the dropdown
        $categories = BookCategory::orderBy('name')->get();

        return view('book.form', compact('book', 'categories'));
    }

    // 
    public function updateMetadata(Request $request, $id)
    {
        // Fetch the book metadata
        $book = BookMetadata::findOrFail($id);

        $oldCategoryId = $book->book_category_id;

        // Validate book input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'year_published' => 'nullable|integer|min:1000|max:' . date('Y'),
            'isbn' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'language' => 'nullable|string|max:255',
            'book_category_id' => 'required|exists:book_categories,id',
            'description' => 'nullable|string',
        ]);

        // Store uploaded image to storage/app/public/books
        if ($request->hasFile('image')) {
            if (!empty($book->image) && Storage::disk('public')->exists($book->image)) {
                Storage::disk('public')->delete($book->image);
            }

            $validated['image'] = $request->file('image')->store('books', 'public');
        }

        // Update book metadata
        $book->update($validated);

        if ($oldCategoryId != $book->book_category_id) {
            $categoryStillUsed = BookMetadata::where('book_category_id', $oldCategoryId)->exists();

            if (!$categoryStillUsed) {
                BookCategory::where('id', $oldCategoryId)->delete();
            }
        }

        return redirect()
            ->route('book.show', $book->id)
            ->with('success', 'Book updated successfully.');
    }

    // 
    public function createCopy($bookId)
    {
        // Fetch the book metadata
        $book = BookMetadata::findOrFail($bookId);

        // Create a new available book copy
        BookCopy::create([
            'book_metadata_id' => $book->id,
            'status' => 'available',
        ]);

        return redirect()
            ->route('book.show', $book->id)
            ->with('success', 'Book copy created successfully.');
    }

    // 
    public function updateCopy(Request $request, $id)
    {
        // Validate copy status input
        $validated = $request->validate([
            'status' => 'required|in:available,borrowed,reserved,lost,damaged,transferred',
        ]);

        // Fetch the book copy
        $copy = BookCopy::findOrFail($id);

        // Update book copy status
        $copy->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('book.show', $copy->book_metadata_id)
            ->with('success', 'Book copy status updated successfully.');
    }
}