<x-main-layout>
    <style>
        .member-page {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .page-title {
            margin: 0;
            color: #1d4ed8;
            font-size: 28px;
            font-weight: 800;
        }

        .search-card,
        .table-card,
        .history-card {
            background: #ffffff;
            border: 1px solid #dbeafe;
            border-radius: 18px;
            padding: 20px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        }

        .search-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .form-select,
        .form-input {
            border: 1px solid #bfdbfe;
            background: #f8fafc;
            color: #1e293b;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
            outline: none;
        }

        .form-input {
            width: 100%;
            max-width: 360px;
        }

        .form-select:focus,
        .form-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px #dbeafe;
            background: #ffffff;
        }

        .btn-primary {
            border: none;
            border-radius: 10px;
            background: #2563eb;
            color: #ffffff;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            background: #ffffff;
            color: #2563eb;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }

        .member-table {
            width: 100%;
            border-collapse: collapse;
        }

        .member-table th,
        .member-table td {
            padding: 14px 12px;
            border-bottom: 1px solid #dbeafe;
            text-align: left;
            font-size: 14px;
        }

        .member-table th {
            color: #1d4ed8;
            background: #eff6ff;
            font-weight: 800;
        }

        .member-table td {
            color: #334155;
        }

        .member-name-link {
            color: #2563eb;
            font-weight: 700;
            text-decoration: none;
        }

        .member-name-link:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        .status-form {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .status-select {
            border: 1px solid #bfdbfe;
            background: #ffffff;
            color: #334155;
            border-radius: 10px;
            padding: 8px 10px;
            font-size: 14px;
            outline: none;
        }

        .btn-save {
            border: none;
            border-radius: 10px;
            background: #2563eb;
            color: #ffffff;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .success-message {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 14px;
            font-weight: 600;
        }

        .empty-message {
            background: #f8fafc;
            border: 1px solid #dbeafe;
            color: #64748b;
            border-radius: 14px;
            padding: 16px;
            font-size: 14px;
        }

        .history-title {
            margin: 0 0 16px;
            color: #1e293b;
            font-size: 22px;
            font-weight: 800;
        }

        .pagination-wrapper {
            margin-top: 20px;
        }

        @media (max-width: 760px) {
            .search-form {
                flex-direction: column;
                align-items: stretch;
            }

            .form-input {
                max-width: none;
            }

            .member-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .status-form {
                min-width: 260px;
            }
        }
    </style>

    <div class="member-page">
        <div class="page-header">
            <h1 class="page-title">Members</h1>
        </div>

        @if (session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <div class="search-card">
            <form method="GET" action="{{ route('admin.members.index') }}" class="search-form">
                <select name="search_by" class="form-select">
                    <option value="id" {{ request('search_by', $searchBy ?? 'name') === 'id' ? 'selected' : '' }}>
                        ID
                    </option>
                    <option value="name" {{ request('search_by', $searchBy ?? 'name') === 'name' ? 'selected' : '' }}>
                        Name
                    </option>
                </select>

                <input
                    type="text"
                    name="keyword"
                    value="{{ request('keyword', $keyword ?? '') }}"
                    class="form-input"
                    placeholder="Search member..."
                >

                <button type="submit" class="btn-primary">Search</button>

                <a href="{{ route('admin.members.index') }}" class="btn-secondary">Reset</a>
            </form>
        </div>

        <div class="table-card">
            @if ($members->count() > 0)
                <table class="member-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($members as $member)
                            <tr>
                                <td>{{ $member->id }}</td>
                                <td>
                                    <a href="{{ route('admin.members.show', $member->id) }}" class="member-name-link">
                                        {{ $member->name }}
                                    </a>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.members.update-status', $member->id) }}" class="status-form">
                                        @csrf

                                        <select name="status" class="status-select">
                                            <option value="active" {{ $member->status === 'active' ? 'selected' : '' }}>
                                                Active
                                            </option>
                                            <option value="suspended" {{ $member->status === 'suspended' ? 'selected' : '' }}>
                                                Suspended
                                            </option>
                                            <option value="inactive" {{ $member->status === 'inactive' ? 'selected' : '' }}>
                                                Inactive
                                            </option>
                                        </select>

                                        <button type="submit" class="btn-save">Save</button>
                                    </form>

                                    @error('status')
                                        <p style="margin: 6px 0 0; color: #dc2626; font-size: 13px;">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="pagination-wrapper">
                    {{ $members->links() }}
                </div>
            @else
                <div class="empty-message">
                    No members found.
                </div>
            @endif
        </div>

        @if ($selectedMember)
            <div class="history-card">
                <h2 class="history-title">
                    Borrowing History - {{ $selectedMember->name }}
                </h2>

                @if ($histories && $histories->count() > 0)
                    <table class="member-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Book</th>
                                <th>Borrowed Date</th>
                                <th>Due Date</th>
                                <th>Returned Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($histories as $history)
                                <tr>
                                    <td>{{ $history->id }}</td>
                                    <td>{{ $history->bookCopy->bookMetadata->title ?? 'Unknown Book' }}</td>
                                    <td>{{ $history->borrowed_date ?? '-' }}</td>
                                    <td>{{ $history->due_date ?? '-' }}</td>
                                    <td>{{ $history->returned_date ?? '-' }}</td>
                                    <td>{{ $history->status ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="pagination-wrapper">
                        {{ $histories->links() }}
                    </div>
                @else
                    <div class="empty-message">
                        This member has no borrowing history yet.
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-main-layout>