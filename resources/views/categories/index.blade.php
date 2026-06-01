<x-main-layout>
    <style>
        .category-page {
            max-width: 900px;
            margin: 0 auto;
        }

        .page-title {
            margin: 0 0 24px;
            color: #1d4ed8;
            font-size: 28px;
            font-weight: 800;
        }

        .category-card {
            background: #ffffff;
            border: 1px solid #dbeafe;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
            margin-bottom: 24px;
        }

        .category-form {
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .form-input {
            width: 100%;
            border: 1px solid #bfdbfe;
            background: #f8fafc;
            color: #1e293b;
            border-radius: 10px;
            padding: 11px 12px;
            font-size: 14px;
            outline: none;
        }

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
            padding: 11px 18px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-edit {
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            background: #eff6ff;
            color: #1d4ed8;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-edit:hover {
            background: #dbeafe;
        }

        .btn-delete {
            border: none;
            border-radius: 10px;
            background: #ef4444;
            color: #ffffff;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-delete:hover {
            background: #dc2626;
        }

        .error-message {
            margin: 8px 0 0;
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

        .category-table {
            width: 100%;
            border-collapse: collapse;
        }

        .category-table th,
        .category-table td {
            padding: 14px 12px;
            border-bottom: 1px solid #dbeafe;
            text-align: left;
            font-size: 14px;
        }

        .category-table th {
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 800;
        }

        .category-table td {
            color: #334155;
        }

        .action-row {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            align-items: center;
        }

        .empty-message {
            background: #f8fafc;
            border: 1px solid #dbeafe;
            color: #64748b;
            border-radius: 14px;
            padding: 16px;
            font-size: 14px;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-card {
            width: 100%;
            max-width: 460px;
            background: #ffffff;
            border: 1px solid #dbeafe;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.18);
        }

        .modal-title {
            margin: 0 0 18px;
            color: #1d4ed8;
            font-size: 22px;
            font-weight: 800;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-cancel {
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            background: #ffffff;
            color: #2563eb;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-cancel:hover {
            background: #eff6ff;
        }

        @media (max-width: 640px) {
            .category-form {
                flex-direction: column;
            }

            .btn-primary {
                width: 100%;
            }

            .category-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>

    <div class="category-page">
        <h1 class="page-title">Categories</h1>

        @if (session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <div class="category-card">
            <form method="POST" action="{{ route('categories.store') }}" class="category-form">
                @csrf

                <div style="width: 100%;">
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        class="form-input"
                        placeholder="Enter category name"
                    >

                    @error('name')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-primary">Submit</button>
            </form>
        </div>

        <div class="category-card">
            @if ($categories->count() > 0)
                <table class="category-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category Name</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>{{ $category->name }}</td>
                                <td>
                                    <div class="action-row">
                                        <button
                                            type="button"
                                            class="btn-edit"
                                            onclick="openEditModal('{{ $category->id }}', '{{ e($category->name) }}')"
                                        >
                                            Edit
                                        </button>

                                        <form method="POST" action="{{ route('categories.destroy', $category->id) }}" onsubmit="return confirm('Delete this category?')">
                                            @csrf
                                            <button type="submit" class="btn-delete">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-message">
                    No categories found.
                </div>
            @endif
        </div>
    </div>

    <div class="modal-overlay" id="editCategoryModal">
        <div class="modal-card">
            <h2 class="modal-title">Edit Category</h2>

            <form method="POST" id="editCategoryForm">
                @csrf

                <input
                    type="text"
                    name="name"
                    id="editCategoryName"
                    class="form-input"
                    placeholder="Enter new category name"
                >

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">
                        Cancel
                    </button>

                    <button type="submit" class="btn-primary">
                        Change
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name) {
            const modal = document.getElementById('editCategoryModal');
            const form = document.getElementById('editCategoryForm');
            const input = document.getElementById('editCategoryName');

            form.action = "{{ route('categories.update', '__ID__') }}".replace('__ID__', id);
            input.value = name;

            modal.classList.add('active');
        }

        function closeEditModal() {
            const modal = document.getElementById('editCategoryModal');
            modal.classList.remove('active');
        }

        document.getElementById('editCategoryModal').addEventListener('click', function (event) {
            if (event.target === this) {
                closeEditModal();
            }
        });
    </script>
</x-main-layout>