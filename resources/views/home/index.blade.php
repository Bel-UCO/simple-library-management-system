<x-main-layout>
    <style>
        .home-layout {
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 24px;
        }

        .sidebar {
            align-self: start;
            background: #ffffff;
            border: 1px solid #dbeafe;
            border-radius: 16px;
            padding: 20px;
        }

        .sidebar-title {
            margin: 0 0 16px;
            color: #1d4ed8;
            font-size: 18px;
            font-weight: 700;
        }

        .category-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .category-link {
            display: block;
            padding: 10px 12px;
            border-radius: 10px;
            color: #334155;
            background: #f8fafc;
            border: 1px solid transparent;
            font-size: 14px;
        }

        .category-link:hover,
        .category-link.active {
            color: #1d4ed8;
            background: #eff6ff;
            border-color: #bfdbfe;
        }

        .content {
            min-width: 0;
        }

        .section {
            margin-bottom: 40px;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .section-title {
            margin: 0;
            color: #1e293b;
            font-size: 26px;
            font-weight: 700;
        }

        .section-subtitle {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 14px;
        }

        .see-all {
            color: #2563eb;
            font-size: 14px;
            font-weight: 600;
        }

        .book-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .book-card {
            background: #ffffff;
            border: 1px solid #dbeafe;
            border-radius: 16px;
            padding: 16px;
            min-height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .book-title {
            margin: 0 0 10px;
            color: #1e293b;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.4;
        }

        .book-meta {
            margin: 5px 0;
            color: #64748b;
            font-size: 14px;
            line-height: 1.4;
        }

        .book-link {
            display: inline-block;
            margin-top: 12px;
            color: #2563eb;
            font-size: 14px;
            font-weight: 600;
        }

        .empty-box {
            background: #ffffff;
            border: 1px solid #dbeafe;
            border-radius: 16px;
            padding: 20px;
            color: #64748b;
            font-size: 14px;
        }

        .pagination-wrapper {
            margin-top: 24px;
        }

        @media (max-width: 900px) {
            .home-layout {
                grid-template-columns: 1fr;
            }

            .book-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 560px) {
            .book-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @php
        $currentCategory = $categories->firstWhere('id', $selectedCategory);
        $isHistoryMode = request('view') === 'history';
        $isResultMode = $keyword || $selectedCategory || $isHistoryMode;
    @endphp

    <div class="home-layout">
        <aside class="sidebar">
            <h2 class="sidebar-title">Categories</h2>

            <div class="category-list">
                <a
                    href="{{ route('home') }}"
                    class="category-link {{ !$selectedCategory ? 'active' : '' }}"
                >
                    All Categories
                </a>

                @foreach ($categories as $category)
                    <a
                        href="{{ route('home', ['category' => $category->id]) }}"
                        class="category-link {{ $selectedCategory == $category->id ? 'active' : '' }}"
                    >
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </aside>

        <div class="content">
            @if ($isHistoryMode)
                <section class="section">
                    <div class="section-header">
                        <div>
                            <h1 class="section-title">History</h1>
                            <p class="section-subtitle">Showing your borrowing history</p>
                        </div>
                    </div>

                    @if ($historyPage->count() > 0)
                        <div class="book-grid">
                            @foreach ($historyPage as $history)
                                <div class="book-card">
                                    <div>
                                        <h3 class="book-title">
                                            {{ $history->bookCopy->bookMetadata->title ?? 'Unknown Book' }}
                                        </h3>

                                        <p class="book-meta">
                                            Borrowed Date: {{ $history->date_borrowed ?? '-' }}
                                        </p>

                                        <p class="book-meta">
                                            Due Date: {{ $history->due_date ?? '-' }}
                                        </p>

                                        <p class="book-meta">
                                            Status: {{ $history->status ?? '-' }}
                                        </p>
                                    </div>

                                    @if ($history->bookCopy && $history->bookCopy->bookMetadata)
                                        <a
                                            href="{{ route('book.show', $history->bookCopy->bookMetadata->id) }}"
                                            class="book-link"
                                        >
                                            View Detail
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-box">
                            You have no borrowing history yet.
                        </div>
                    @endif

                    @if ($historyPage && $historyPage->hasPages())
                        <div class="pagination-wrapper">
                            {{ $historyPage->links() }}
                        </div>
                    @endif
                </section>
            @elseif ($isResultMode)
                <section class="section">
                    <div class="section-header">
                        <div>
                            @if ($keyword)
                                <h1 class="section-title">Search result for "{{ $keyword }}"</h1>
                                <p class="section-subtitle">Showing books by {{ ucfirst($searchBy) }}</p>
                            @elseif ($currentCategory)
                                <h1 class="section-title">{{ $currentCategory->name }}</h1>
                                <p class="section-subtitle">Showing books in this category</p>
                            @else
                                <h1 class="section-title">Books</h1>
                            @endif
                        </div>
                    </div>

                    @if ($books->count() > 0)
                        <div class="book-grid">
                            @foreach ($books as $book)
                                <div class="book-card">
                                    <div>
                                        <h3 class="book-title">{{ $book->title }}</h3>
                                        <p class="book-meta">Author: {{ $book->author }}</p>
                                        <p class="book-meta">Publisher: {{ $book->publisher ?? '-' }}</p>
                                        <p class="book-meta">Category: {{ $book->bookCategory->name ?? '-' }}</p>
                                    </div>

                                    <a href="{{ route('book.show', $book->id) }}" class="book-link">
                                        View Detail
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-box">
                            No books found.
                        </div>
                    @endif

                    @if ($books && $books->hasPages())
                        <div class="pagination-wrapper">
                            {{ $books->links() }}
                        </div>
                    @endif
                </section>
            @else
                @can('is-member')
                    @if (count($histories) > 0)
                        <section class="section">
                            <div class="section-header">
                                <h1 class="section-title">History</h1>
                                <a href="{{ route('home', ['view' => 'history']) }}" class="see-all">See All</a>
                            </div>

                            @php
                                $historyLast = $histories->take(4)
                            @endphp

                            <div class="book-grid">
                                @foreach ($historyLast as $history)
                                    <div class="book-card">
                                        <div>
                                            <h3 class="book-title">
                                                {{ $history->bookCopy->bookMetadata->title ?? 'Unknown Book' }}
                                            </h3>

                                            <p class="book-meta">
                                                Borrowed Date: {{ $history->date_borrowed ?? '-' }}
                                            </p>

                                            <p class="book-meta">
                                                Due Date: {{ $history->due_date ?? '-' }}
                                            </p>

                                            <p class="book-meta">
                                                Status: {{ $history->status ?? '-' }}
                                            </p>
                                        </div>

                                        @if ($history->bookCopy && $history->bookCopy->bookMetadata)
                                            <a
                                                href="{{ route('book.show', $history->bookCopy->bookMetadata->id) }}"
                                                class="book-link"
                                            >
                                                View Detail
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif
                @endcan

                <section class="section">
                    <div class="section-header">
                        <h1 class="section-title">Latest Arrivals</h1>
                    </div>

                    @if ($latestArrivals->count() > 0)
                        <div class="book-grid">
                            @foreach ($latestArrivals as $book)
                                <div class="book-card">
                                    <div>
                                        <h3 class="book-title">{{ $book->title }}</h3>
                                        <p class="book-meta">Author: {{ $book->author }}</p>
                                        <p class="book-meta">Publisher: {{ $book->publisher ?? '-' }}</p>
                                        <p class="book-meta">Category: {{ $book->bookCategory->name ?? '-' }}</p>
                                    </div>

                                    <a href="{{ route('book.show', $book->id) }}" class="book-link">
                                        View Detail
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-box">
                            No latest arrivals yet.
                        </div>
                    @endif
                </section>
            @endif
        </div>
    </div>
</x-main-layout>