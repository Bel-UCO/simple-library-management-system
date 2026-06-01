<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BorrowedLog;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    // Display a list of members with search functionality
    public function index(Request $request)
    {
        $searchBy = $request->input('search_by', 'name');
        $keyword = $request->input('keyword');

        if (!in_array($searchBy, ['id', 'name'])) {
            $searchBy = 'name';
        }

        // Fetch members for the sidebar with search functionality
        $members = User::where('is_admin', false)
            ->when($keyword, function ($query) use ($searchBy, $keyword) {
                if ($searchBy === 'id') {
                    $query->where('id', $keyword);
                } else {
                    $query->where('name', 'like', '%' . $keyword . '%');
                }
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $selectedMember = null;
        $histories = null;

        return view('admin.member.index', compact('members', 'selectedMember', 'histories', 'searchBy', 'keyword'));
    }

    // Display the details of a specific member along with their borrowing history
    public function show(Request $request, $id)
    {
        $searchBy = $request->input('search_by', 'name');
        $keyword = $request->input('keyword');

        if (!in_array($searchBy, ['id', 'name'])) {
            $searchBy = 'name';
        }

        // Fetch members for the sidebar with search functionality
        $members = User::where('is_admin', false)
            ->when($keyword, function ($query) use ($searchBy, $keyword) {
                if ($searchBy === 'id') {
                    $query->where('id', $keyword);
                } else {
                    $query->where('name', 'like', '%' . $keyword . '%');
                }
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $selectedMember = User::where('is_admin', false)->findOrFail($id);

        $histories = BorrowedLog::with('bookCopy.bookMetadata')
            ->where('user_id', $selectedMember->id)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.member.index', compact('members', 'selectedMember', 'histories', 'searchBy', 'keyword'));
    }

    // Update the status of a member (active, suspended, inactive)
    public function updateStatus(Request $request, $id)
    {
        $member = User::where('is_admin', false)->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:active,suspended,inactive',
        ]);

        $member->update([
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Member status updated successfully.');
    }
}