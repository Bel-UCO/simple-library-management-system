<x-main-layout>
    <style>
        .form-wrapper {
            max-width: 760px;
            margin: 0 auto;
        }

        .form-card {
            background: #ffffff;
            border: 1px solid #dbeafe;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        }

        .form-title {
            margin: 0 0 6px;
            color: #1d4ed8;
            font-size: 28px;
            font-weight: 800;
        }

        .form-subtitle {
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
        .form-select,
        .form-textarea {
            width: 100%;
            border: 1px solid #bfdbfe;
            background: #f8fafc;
            color: #1e293b;
            border-radius: 10px;
            padding: 11px 12px;
            font-size: 14px;
            outline: none;
        }

        .form-textarea {
            min-height: 130px;
            resize: vertical;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px #dbeafe;
            background: #ffffff;
        }

        .current-image-box {
            width: 160px;
            aspect-ratio: 3 / 4;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 14px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2563eb;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .current-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .help-text {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 13px;
        }

        .error-message {
            margin: 6px 0 0;
            color: #dc2626;
            font-size: 13px;
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

        @media (max-width: 640px) {
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

    @php
        $isEdit = isset($book);
    @endphp

    <div class="form-wrapper">
        <div class="form-card">
            <h1 class="form-title">{{ $isEdit ? 'Edit Book' : 'Add New Book' }}</h1>
            <p class="form-subtitle">
                {{ $isEdit ? 'Update book metadata and book image.' : 'Create new book metadata and the first available book copy.' }}
            </p>

            <form method="POST" action="{{ $isEdit ? route('book.update-metadata.save', $book->id) : route('book.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="title" class="form-label">Title</label>
                    <input
                        type="text"
                        name="title"
                        id="title"
                        value="{{ old('title', $isEdit ? $book->title : '') }}"
                        class="form-input"
                        placeholder="Enter book title"
                    >
                    @error('title')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="author" class="form-label">Author</label>
                    <input
                        type="text"
                        name="author"
                        id="author"
                        value="{{ old('author', $isEdit ? $book->author : '') }}"
                        class="form-input"
                        placeholder="Enter author name"
                    >
                    @error('author')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="publisher" class="form-label">Publisher</label>
                    <input
                        type="text"
                        name="publisher"
                        id="publisher"
                        value="{{ old('publisher', $isEdit ? $book->publisher : '') }}"
                        class="form-input"
                        placeholder="Enter publisher name"
                    >
                    @error('publisher')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="year_published" class="form-label">Year Published</label>
                    <input
                        type="number"
                        name="year_published"
                        id="year_published"
                        value="{{ old('year_published', $isEdit ? $book->year_published : '') }}"
                        class="form-input"
                        placeholder="Example: 2024"
                    >
                    @error('year_published')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="isbn" class="form-label">ISBN</label>
                    <input
                        type="text"
                        name="isbn"
                        id="isbn"
                        value="{{ old('isbn', $isEdit ? $book->isbn : '') }}"
                        class="form-input"
                        placeholder="Optional"
                    >
                    @error('isbn')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="image" class="form-label">Book Image</label>

                    @if ($isEdit)
                        <div class="current-image-box">
                            @if (!empty($book->image))
                                <img src="{{ asset('storage/' . $book->image) }}" alt="{{ $book->title }}" class="current-image">
                            @else
                                <span>No Image</span>
                            @endif
                        </div>
                    @endif

                    <input
                        type="file"
                        name="image"
                        id="image"
                        class="form-input"
                        accept="image/*"
                    >

                    @if ($isEdit)
                        <p class="help-text">Leave empty if you do not want to change the current image.</p>
                    @endif

                    @error('image')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="language" class="form-label">Language</label>
                    <input
                        type="text"
                        name="language"
                        id="language"
                        value="{{ old('language', $isEdit ? $book->language : '') }}"
                        class="form-input"
                        placeholder="Example: Indonesian"
                    >
                    @error('language')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="book_category_id" class="form-label">Category</label>
                    <select name="book_category_id" id="book_category_id" class="form-select">
                        <option value="">Select category</option>

                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('book_category_id', $isEdit ? $book->book_category_id : '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('book_category_id')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea
                        name="description"
                        id="description"
                        class="form-textarea"
                        placeholder="Enter book description"
                    >{{ old('description', $isEdit ? $book->description : '') }}</textarea>
                    @error('description')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="button-row">
                    <a href="{{ $isEdit ? route('book.show', $book->id) : route('home') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">
                        {{ $isEdit ? 'Update Book' : 'Save Book' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-main-layout>