<?php

namespace App\Http\Controllers;

use App\Models\BookMetadata;
use Illuminate\Http\Request;
use App\Models\BookCategory;
use App\Models\BorrowedLog;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Get the borrowing history of the authenticated user (if logged in)
        $histories = [];

        if (Auth::check()) {
            $histories = BorrowedLog::with('bookCopy.bookMetadata')
                ->where('user_id', Auth::id())
                ->latest()
                ->take(4)
                ->get();
        }

        // Get the full borrowing history for the "View History" page (if requested)
        $historyPage = null;

        if (Auth::check() && $request->input('view') === 'history') {
            $historyPage = BorrowedLog::with('bookCopy.bookMetadata')
                ->where('user_id', Auth::id())
                ->latest()
                ->paginate(20)
                ->withQueryString();
        }

        // Get the latest 20 book arrivals
        $latestArrivals = BookMetadata::with('bookCategory')
            ->latest()
            ->take(20)
            ->get();

        // Get all book categories for the filter dropdown
        $categories = BookCategory::orderBy('name')->get();

        // Get the selected category from the request (if any)
        $selectedCategory = $request->input('category');

        // Handle search and category filter
        $allowedSearchColumns = ['title', 'author', 'publisher'];

        $searchBy = $request->input('search_by', 'title');
        $keyword = $request->input('keyword');

        if (!in_array($searchBy, $allowedSearchColumns)) {
            $searchBy = 'title';
        }

        $books = BookMetadata::with('bookCategory')
            ->when($request->category, function ($query, $selectedCategory) {
                $query->where('book_category_id', $selectedCategory);
            })
            ->when($keyword, function ($query) use ($searchBy, $keyword) {
                $query->where($searchBy, 'like', '%' . $keyword . '%');
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('home.index', compact('latestArrivals', 'categories', 'histories', 'historyPage', 'books', 'selectedCategory', 'searchBy', 'keyword'));
    }
}
