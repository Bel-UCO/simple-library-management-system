<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Library App') }}</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }

        a {
            text-decoration: none;
        }

        .page-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: #f8fafc;
            border-bottom: 1px solid #dbeafe;
        }

        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }

        .logo {
            font-size: 20px;
            font-weight: 700;
            color: #1d4ed8;
            white-space: nowrap;
        }

        .search-form {
            flex: 1;
            display: flex;
            justify-content: center;
            gap: 8px;
        }

        .search-select,
        .search-input {
            border: 1px solid #bfdbfe;
            background: #ffffff;
            color: #334155;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 14px;
            outline: none;
        }

        .search-input {
            width: 100%;
            max-width: 380px;
        }

        .search-select:focus,
        .search-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px #dbeafe;
        }

        .btn-primary {
            border: none;
            border-radius: 8px;
            background: #2563eb;
            color: #ffffff;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .user-menu {
            position: relative;
            padding-bottom: 8px;
            margin-bottom: -8px;
        }

        .user-menu-button {
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            background: #ffffff;
            color: #334155;
            padding: 10px 14px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .user-menu-button:hover {
            background: #eff6ff;
            border-color: #93c5fd;
        }

        .dropdown {
            position: absolute;
            right: 0;
            top: 100%;
            z-index: 100;
            width: 220px;
            background: #ffffff;
            border: 1px solid #dbeafe;
            border-radius: 12px;
            padding: 8px;
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
            display: none;
        }

        .user-menu:hover .dropdown,
        .user-menu:focus-within .dropdown {
            display: block;
        }

        .dropdown a,
        .dropdown button {
            display: block;
            width: 100%;
            padding: 10px 12px;
            border: none;
            border-radius: 8px;
            background: transparent;
            color: #334155;
            text-align: left;
            font-size: 14px;
            cursor: pointer;
        }

        .dropdown a:hover,
        .dropdown button:hover {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .dropdown-divider {
            margin: 8px 0;
            border-top: 1px solid #dbeafe;
        }

        .main-content {
            flex: 1;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px 24px;
        }

        .footer {
            background: #f1f5f9;
            border-top: 1px solid #dbeafe;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            color: #64748b;
            font-size: 14px;
        }

        .footer-brand {
            color: #1d4ed8;
            font-weight: 600;
        }

        .mobile-search {
            display: none;
            padding: 0 24px 16px;
        }

        @media (max-width: 768px) {
            .navbar-container {
                gap: 12px;
            }

            .search-form.desktop {
                display: none;
            }

            .mobile-search {
                display: block;
            }

            .mobile-search .search-form {
                flex-direction: column;
            }

            .search-input {
                max-width: none;
            }

            .footer-container {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <nav class="navbar">
            <div class="navbar-container">
                <a href="{{ route('home') }}" class="logo">
                    Library App
                </a>

                <form method="GET" action="{{ route('home') }}" class="search-form desktop">
                    <select name="search_by" class="search-select">
                        <option value="title" {{ request('search_by') === 'title' ? 'selected' : '' }}>Title</option>
                        <option value="author" {{ request('search_by') === 'author' ? 'selected' : '' }}>Author</option>
                        <option value="publisher" {{ request('search_by') === 'publisher' ? 'selected' : '' }}>Publisher</option>
                    </select>

                    <input
                        type="text"
                        name="keyword"
                        value="{{ request('keyword') }}"
                        placeholder="Search books..."
                        class="search-input"
                    >

                    @if (request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif

                    <button type="submit" class="btn-primary">Search</button>
                </form>

                <div>
                    @auth
                        <div class="user-menu">
                            <button type="button" class="user-menu-button">
                                Hello, {{ auth()->user()->is_admin ? 'Admin' : auth()->user()->name }} ▾
                            </button>

                            <div class="dropdown">
                                @can('is-admin')
                                    <a href="{{ route('book.create') }}">Add New Book</a>
                                    <a href="{{ route('categories.list') }}">Manage Category</a>
                                    <a href="{{ route('admin.members.index') }}">Manage Member</a>
                                    <a href="{{ route('admin.borrowed-logs.create') }}">Issue Book</a>
                                    <a href="{{ route('admin.borrowed-logs.return') }}">Return Book</a>
                                @endcan

                                @can('is-member')
                                    <a href="{{ route('home', ['view' => 'history']) }}">Borrowed History</a>
                                    <a href="{{ route('login.setting') }}">Setting</a>
                                @endcan

                                <div class="dropdown-divider"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit">Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login.index') }}" class="btn-primary">Login</a>
                    @endauth
                </div>
            </div>

            <div class="mobile-search">
                <form method="GET" action="{{ route('home') }}" class="search-form">
                    <select name="search_by" class="search-select">
                        <option value="title" {{ request('search_by') === 'title' ? 'selected' : '' }}>Title</option>
                        <option value="author" {{ request('search_by') === 'author' ? 'selected' : '' }}>Author</option>
                        <option value="publisher" {{ request('search_by') === 'publisher' ? 'selected' : '' }}>Publisher</option>
                    </select>

                    <input
                        type="text"
                        name="keyword"
                        value="{{ request('keyword') }}"
                        placeholder="Search books..."
                        class="search-input"
                    >

                    @if (request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif

                    <button type="submit" class="btn-primary">Search</button>
                </form>
            </div>
        </nav>

        <main class="main-content">
            {{ $slot }}
        </main>

        <footer class="footer">
            <div class="footer-container">
                <span>© 2026 Library App. All rights reserved.</span>
                <span class="footer-brand">Simple Library Management System</span>
            </div>
        </footer>
    </div>
</body>
</html>