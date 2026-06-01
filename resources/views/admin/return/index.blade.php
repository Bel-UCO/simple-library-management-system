<x-main-layout>
    <style>
        .return-page {
            max-width: 980px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        .return-card,
        .history-card {
            background: #ffffff;
            border: 1px solid #dbeafe;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        }

        .page-title {
            margin: 0 0 6px;
            color: #1d4ed8;
            font-size: 28px;
            font-weight: 800;
        }

        .page-subtitle {
            margin: 0 0 24px;
            color: #64748b;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            margin-bottom: 7px;
            color: #334155;
            font-size: 14px;
            font-weight: 700;
        }

        .form-input,
        .form-select {
            width: 100%;
            border: 1px solid #bfdbfe;
            background: #f8fafc;
            color: #1e293b;
            border-radius: 10px;
            padding: 11px 12px;
            font-size: 14px;
            outline: none;
        }

        .form-input:focus,
        .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px #dbeafe;
            background: #ffffff;
        }

        .error-message {
            margin: 6px 0 0;
            color: #dc2626;
            font-size: 13px;
        }

        .success-message {
            margin-bottom: 18px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 14px;
            font-weight: 600;
        }

        .button-row {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 24px;
        }

        .btn-primary {
            border: none;
            border-radius: 10px;
            background: #2563eb;
            color: #ffffff;
            padding: 11px 18px;
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
            padding: 11px 18px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background: #eff6ff;
        }

        .history-title {
            margin: 0 0 18px;
            color: #1d4ed8;
            font-size: 24px;
            font-weight: 800;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
        }

        .history-table th,
        .history-table td {
            padding: 13px 12px;
            border-bottom: 1px solid #dbeafe;
            text-align: left;
            font-size: 14px;
        }

        .history-table th {
            color: #1d4ed8;
            background: #eff6ff;
            font-weight: 800;
        }

        .history-table td {
            color: #334155;
        }

        .empty-message {
            background: #f8fafc;
            border: 1px solid #dbeafe;
            color: #64748b;
            border-radius: 14px;
            padding: 16px;
            font-size: 14px;
        }

        @media (max-width: 760px) {
            .history-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .button-row {
                flex-direction: column;
            }

            .btn-primary,
            .btn-secondary {
                width: 100%;
                text-align: center;
            }
        }
    </style>

    <div class="return-page">
        <div class="return-card">
            <h1 class="page-title">Return Book</h1>
            <p class="page-subtitle">Record a book return transaction.</p>

            @if (session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
            @endif

            <form method="POST" action="{{ route('admin.borrowed-logs.return-store') }}">
                @csrf

                <div class="form-group">
                    <label for="borrowed_log_id" class="form-label">Borrowed Log ID</label>
                    <input
                        type="number"
                        name="borrowed_log_id"
                        id="borrowed_log_id"
                        value="{{ old('borrowed_log_id') }}"
                        class="form-input"
                        placeholder="Enter borrowed log ID">

                    @error('borrowed_log_id')
                    <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="returned_date" class="form-label">Returned Date</label>
                    <input
                        type="date"
                        name="returned_date"
                        id="returned_date"
                        value="{{ old('returned_date', date('Y-m-d')) }}"
                        class="form-input">

                    @error('returned_date')
                    <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="button-row">
                    <a href="{{ route('home') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Return Book</button>
                </div>
            </form>
        </div>

        <div class="history-card">
            <h2 class="history-title">Return History</h2>

            @if ($returnHistories->count() > 0)
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Member ID</th>
                        <th>Member Name</th>
                        <th>Book Copy ID</th>
                        <th>Book Name</th>
                        <th>Borrowed Date</th>
                        <th>Returned Date</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($returnHistories as $history)
                    <tr>
                        <td>{{ $history->user->id ?? '-' }}</td>
                        <td>{{ $history->user->name ?? 'Unknown Member' }}</td>
                        <td>{{ $history->bookCopy->id ?? '-' }}</td>
                        <td>{{ $history->bookCopy->bookMetadata->title ?? 'Unknown Book' }}</td>
                        <td>{{ $history->borrowed_date ?? '-' }}</td>
                        <td>{{ $history->returned_date ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-message">
                No return history yet.
            </div>
            @endif
        </div>
    </div>
</x-main-layout>