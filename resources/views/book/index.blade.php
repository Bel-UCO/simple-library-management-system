<x-main-layout>
    <style>
        .book-detail-wrapper {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .book-detail-card {
            background: #ffffff;
            border: 1px solid #dbeafe;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        }

        .book-detail-header {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 28px;
            align-items: start;
        }

        .book-image-box {
            width: 100%;
            aspect-ratio: 3 / 4;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2563eb;
            font-size: 18px;
            font-weight: 700;
        }

        .book-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .book-info-header {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: start;
            margin-bottom: 16px;
        }

        .book-title {
            margin: 0;
            color: #1e293b;
            font-size: 30px;
            font-weight: 800;
            line-height: 1.25;
        }

        .book-category {
            margin-top: 8px;
            display: inline-block;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 13px;
            font-weight: 700;
        }

        .edit-button {
            white-space: nowrap;
            border: none;
            border-radius: 10px;
            background: #2563eb;
            color: #ffffff;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }

        .edit-button:hover {
            background: #1d4ed8;
        }

        .metadata-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 18px;
        }

        .metadata-item {
            background: #f8fafc;
            border: 1px solid #dbeafe;
            border-radius: 12px;
            padding: 12px;
        }

        .metadata-label {
            margin: 0 0 4px;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
        }

        .metadata-value {
            margin: 0;
            color: #1e293b;
            font-size: 15px;
            font-weight: 700;
        }

        .available-box {
            margin-top: 18px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 14px;
            padding: 16px;
        }

        .available-label {
            margin: 0;
            color: #1d4ed8;
            font-size: 14px;
            font-weight: 700;
        }

        .available-value {
            margin: 6px 0 0;
            color: #1e293b;
            font-size: 28px;
            font-weight: 800;
        }

        .description-box {
            margin-top: 18px;
        }

        .description-title {
            margin: 0 0 8px;
            color: #1e293b;
            font-size: 18px;
            font-weight: 800;
        }

        .description-text {
            margin: 0;
            color: #475569;
            font-size: 15px;
            line-height: 1.7;
        }

        .admin-section {
            margin-top: 28px;
            padding-top: 22px;
            border-top: 1px solid #dbeafe;
        }

        .admin-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
        }

        .admin-title {
            margin: 0;
            color: #1e293b;
            font-size: 20px;
            font-weight: 800;
        }

        .plus-button {
            width: 42px;
            height: 42px;
            border: none;
            border-radius: 50%;
            background: #2563eb;
            color: #ffffff;
            font-size: 24px;
            line-height: 1;
            font-weight: 700;
            cursor: pointer;
        }

        .plus-button:hover {
            background: #1d4ed8;
        }

        .copy-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .copy-row {
            display: grid;
            grid-template-columns: 1fr 220px auto;
            gap: 12px;
            align-items: center;
            background: #f8fafc;
            border: 1px solid #dbeafe;
            border-radius: 14px;
            padding: 14px;
        }

        .copy-id {
            color: #1e293b;
            font-size: 14px;
            font-weight: 700;
        }

        .copy-status-form {
            display: contents;
        }

        .status-select {
            width: 100%;
            border: 1px solid #bfdbfe;
            background: #ffffff;
            color: #334155;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
            outline: none;
        }

        .status-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px #dbeafe;
        }

        .save-button {
            border: none;
            border-radius: 10px;
            background: #2563eb;
            color: #ffffff;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
        }

        .save-button:hover {
            background: #1d4ed8;
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

        @media (max-width: 900px) {
            .book-detail-header {
                grid-template-columns: 1fr;
            }

            .book-image-box {
                max-width: 260px;
            }

            .metadata-grid {
                grid-template-columns: 1fr;
            }

            .copy-row {
                grid-template-columns: 1fr;
            }

            .copy-status-form {
                display: grid;
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }
    </style>

    @php
        $availableCopies = $copies->where('status', 'available')->count();

        $statusOptions = [
            'available',
            'borrowed',
            'reserved',
            'lost',
            'damaged',
            'transferred',
        ];
    @endphp

    <div class="book-detail-wrapper">
        @if (session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <div class="book-detail-card">
            <div class="book-detail-header">
                <div class="book-image-box">
                    @if (!empty($book->image))
                        <img src="{{ asset('storage/' . $book->image) }}" alt="{{ $book->title }}" class="book-image">
                    @else
                        <span>No Image</span>
                    @endif
                </div>

                <div>
                    <div class="book-info-header">
                        <div>
                            <h1 class="book-title">{{ $book->title }}</h1>

                            <span class="book-category">
                                {{ $book->bookCategory->name ?? 'Uncategorized' }}
                            </span>
                        </div>

                        @can('is-admin')
                            <a href="{{ route('book.update-metadata', $book->id) }}" class="edit-button">
                                Edit
                            </a>
                        @endcan
                    </div>

                    <div class="metadata-grid">
                        <div class="metadata-item">
                            <p class="metadata-label">Author</p>
                            <p class="metadata-value">{{ $book->author }}</p>
                        </div>

                        <div class="metadata-item">
                            <p class="metadata-label">Publisher</p>
                            <p class="metadata-value">{{ $book->publisher }}</p>
                        </div>

                        <div class="metadata-item">
                            <p class="metadata-label">Year Published</p>
                            <p class="metadata-value">{{ $book->year_published }}</p>
                        </div>

                        <div class="metadata-item">
                            <p class="metadata-label">ISBN</p>
                            <p class="metadata-value">{{ $book->isbn ?? '-' }}</p>
                        </div>

                        <div class="metadata-item">
                            <p class="metadata-label">Language</p>
                            <p class="metadata-value">{{ $book->language }}</p>
                        </div>
                    </div>

                    @auth
                        <div class="available-box">
                            <p class="available-label">Available Book</p>
                            <p class="available-value">{{ $availableCopies }}</p>
                        </div>
                    @endauth

                    <div class="description-box">
                        <h2 class="description-title">Description</h2>
                        <p class="description-text">
                            {{ $book->description ?? 'No description available.' }}
                        </p>
                    </div>
                </div>
            </div>

            @can('is-admin')
                <div class="admin-section">
                    <div class="admin-section-header">
                        <h2 class="admin-title">Book Copies</h2>

                        <form method="POST" action="{{ route('book.copy.store', $book->id) }}">
                            @csrf
                            <button type="submit" class="plus-button" title="Add new copy">
                                +
                            </button>
                        </form>
                    </div>

                    <div class="copy-list">
                        @forelse ($copies as $copy)
                            <div class="copy-row">
                                <div class="copy-id">
                                    Copy ID: {{ $copy->id }}
                                </div>

                                <form method="POST" action="{{ route('book.copy.update', $copy->id) }}" class="copy-status-form">
                                    @csrf

                                    <select name="status" class="status-select">
                                        @foreach ($statusOptions as $status)
                                            <option value="{{ $status }}" {{ $copy->status === $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <button type="submit" class="save-button">
                                        Save
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="empty-message">
                                No copies available for this book.
                            </div>
                        @endforelse
                    </div>
                </div>
            @endcan
        </div>
    </div>
</x-main-layout>