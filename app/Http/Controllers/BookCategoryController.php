<?php

namespace App\Http\Controllers;

use App\Models\BookCategory;
use Illuminate\Http\Request;

class BookCategoryController extends Controller
{
    // Display a list of all book categories
    public function index()
    {
        $categories = BookCategory::latest()->get();

        return view('categories.index', compact('categories'));
    }

    // Show the form to create a new book category
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:book_categories,name',
        ]);

        BookCategory::create($validated);

        return redirect()
            ->route('categories.list')
            ->with('success', 'Category created successfully.');
    }

    // Show the form to edit an existing book category

    public function update(Request $request, $id)
    {
        $category = BookCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:book_categories,name,' . $category->id,
        ]);

        $category->update($validated);

        return redirect()
            ->route('categories.list')
            ->with('success', 'Category updated successfully.');
    }

    // Delete a book category
    public function destroy($id)
    {
        $category = BookCategory::findOrFail($id);
        $category->delete();

        return redirect()
            ->route('categories.list')
            ->with('success', 'Category deleted successfully.');
    }
}