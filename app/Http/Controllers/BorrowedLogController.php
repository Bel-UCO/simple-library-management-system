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
        // Fetch active members and available book copies for the form
        $members = User::where('is_admin', false)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $availableCopies = BookCopy::with('bookMetadata')
            ->where('status', 'available')
            ->latest()
            ->get();

        $histories = BorrowedLog::with(['user', 'bookCopy.bookMetadata'])
            ->latest()
            ->take(20)
            ->get();

        return view('admin.issue.index', compact('members', 'availableCopies', 'histories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_copy_id' => 'required|exists:book_copies,id',
            'date_borrowed' => 'required|date',
        ]);

        $bookBorrowed = User::where('id', $validated['user_id'])
            ->where('is_admin', false)
            ->where('status', 'active')
            ->first();

        if (!$bookBorrowed) {
            return back()
                ->withErrors([
                    'user_id' => 'This member is not active.',
                ])
                ->withInput();
        }

        $activeBorrowCount = $bookBorrowed->borrowedLogs()
            ->whereNull('date_returned')
            ->count();

        if ($activeBorrowCount >= 3) {
            return back()
                ->withErrors([
                    'user_id' => 'This member has already borrowed 3 books and cannot borrow more.',
                ])
                ->withInput();
        }

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

        DB::transaction(function () use ($validated, $bookCopy) {
            BorrowedLog::create([
                'user_id' => $validated['user_id'],
                'book_copy_id' => $validated['book_copy_id'],
                'date_borrowed' => $validated['date_borrowed'],
                'due_date' => Carbon::parse($validated['date_borrowed'])->addDays(7)->toDateString(),
                'date_returned' => null,
            ]);

            $bookCopy->update([
                'status' => 'borrowed',
            ]);
        });

        return redirect()
            ->route('admin.borrowed-logs.create')
            ->with('success', 'Book borrowed successfully.');
    }

    public function returnForm()
    {
        $borrowedLogs = BorrowedLog::with(['user', 'bookCopy.bookMetadata'])
            ->whereNull('date_returned')
            ->whereHas('bookCopy', function ($query) {
                $query->where('status', 'borrowed');
            })
            ->latest()
            ->get();

        $returnHistories = BorrowedLog::with(['user', 'bookCopy.bookMetadata'])
            ->whereNotNull('date_returned')
            ->latest()
            ->take(20)
            ->get();

        return view('admin.return.index', compact('borrowedLogs', 'returnHistories'));
    }

    public function returnBook(Request $request)
    {
        $validated = $request->validate([
            'borrowed_log_id' => 'required|exists:borrowed_logs,id',
            'date_returned' => 'required|date',
        ]);

        $borrowedLog = BorrowedLog::with('bookCopy')
            ->whereNull('date_returned')
            ->whereHas('bookCopy', function ($query) {
                $query->where('status', 'borrowed');
            })
            ->findOrFail($validated['borrowed_log_id']);

        DB::transaction(function () use ($validated, $borrowedLog) {
            $borrowedLog->update([
                'date_returned' => $validated['date_returned'],
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