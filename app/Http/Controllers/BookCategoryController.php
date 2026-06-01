<?php

namespace App\Http\Controllers;

use App\Models\BookCategory;
use Illuminate\Http\Request;

class BookCategoryController extends Controller
{
    public function index()
    {
        $categories = BookCategory::latest()->get();

        return view('categories.index', compact('categories'));
    }

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

    public function destroy($id)
    {
        $category = BookCategory::findOrFail($id);
        $category->delete();

        return redirect()
            ->route('categories.list')
            ->with('success', 'Category deleted successfully.');
    }
}