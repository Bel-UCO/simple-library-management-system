<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BookCopy;
use App\Models\BorrowedLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BorrowedLogController extends Controller
{
    public function create()
    {
        // Fetch borrowed histories with related user and book copy data, ordered by most recent
        $histories = BorrowedLog::with(['user', 'bookCopy.bookMetadata'])
            ->latest()
            ->take(20)
            ->get();

        return view('admin.issue.index', compact('histories'));
    }


    // Handle the borrowing of a book
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_copy_id' => 'required|exists:book_copies,id',
            'borrowed_date' => 'required|date',
        ]);

        // Check if the member is active and has not exceeded the borrowing limit

        $bookBorrowed = User::where('id', $validated['user_id'])
            ->where('is_admin', false)
            ->where('status', 'active')
            ->first();


        // Check if the member is active
        if (!$bookBorrowed) {
            return back()
                ->withErrors([
                    'user_id' => 'This member is not active.',
                ])
                ->withInput();
        }

        // Check if the member has already borrowed 3 books that are not yet returned
        $activeBorrowCount = $bookBorrowed->borrowedLogs()
            ->whereNull('returned_date')
            ->count();

        if ($activeBorrowCount >= 3) {
            return back()
                ->withErrors([
                    'user_id' => 'This member has already borrowed 3 books and cannot borrow more.',
                ])
                ->withInput();
        }


        // Check if the book copy is available
        $bookCopy = BookCopy::where('id', $validated['book_copy_id'])
            ->where('status', 'available')
            ->first();

        if (!$bookCopy) {
            return back()
                ->withErrors([
                    'book_copy_id' => 'This book copy is not available.',
                ])
                ->withInput();
        }

        // Use a transaction to ensure data integrity
        DB::transaction(function () use ($validated, $bookCopy) {
            BorrowedLog::create([
                'user_id' => $validated['user_id'],
                'book_copy_id' => $validated['book_copy_id'],
                'borrowed_date' => $validated['borrowed_date'],
                'due_date' => Carbon::parse($validated['borrowed_date'])->addDays(7)->toDateString(),
                'returned_date' => null,
            ]);

            $bookCopy->update([
                'status' => 'borrowed',
            ]);
        });

        return redirect()
            ->route('admin.borrowed-logs.create')
            ->with('success', 'Book borrowed successfully.');
    }

    //  Show the form to return a book
    public function returnForm()
    {
        // Fetch currently borrowed books with related user and book copy data, ordered by most recent
        $returnHistories = BorrowedLog::with(['user', 'bookCopy.bookMetadata'])
            ->whereNotNull('returned_date')
            ->latest()
            ->take(20)
            ->get();

        return view('admin.return.index', compact('returnHistories'));
    }

    // Handle the returning of a book
    public function returnBook(Request $request)
    {
        $validated = $request->validate([
            'borrowed_log_id' => 'required|exists:borrowed_logs,id',
            'returned_date' => 'required|date',
        ]);

        // Find the borrowed log entry and ensure it is valid for return
        $borrowedLog = BorrowedLog::with('bookCopy')
            ->whereNull('returned_date')
            ->whereHas('bookCopy', function ($query) {
                $query->where('status', 'borrowed');
            })
            ->where('id', $validated['borrowed_log_id'])
            ->first();

        if (!$borrowedLog) {
            return back()
                ->withErrors([
                    'borrowed_log_id' => 'Borrowed log not found or the book has already been returned.',
                ])
                ->withInput();
        }
        //  Use a transaction to ensure data integrity when updating the borrowed log and book copy status
        DB::transaction(function () use ($validated, $borrowedLog) {
            $borrowedLog->update([
                'returned_date' => $validated['returned_date'],
            ]);

            $borrowedLog->bookCopy->update([
                'status' => 'available',
            ]);
        });

        return redirect()
            ->route('admin.borrowed-logs.return')
            ->with('success', 'Book returned successfully.');
    }
}
