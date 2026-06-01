<x-main-layout>
    <style>
        .auth-wrapper {
            min-height: 65vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-card {
            width: 100%;
            max-width: 460px;
            background: #ffffff;
            border: 1px solid #dbeafe;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        }

        .auth-title {
            margin: 0;
            color: #1d4ed8;
            font-size: 28px;
            font-weight: 700;
            text-align: center;
        }

        .auth-subtitle {
            margin: 8px 0 24px;
            color: #64748b;
            font-size: 14px;
            text-align: center;
        }

        .success-message {
            margin-bottom: 16px;
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            color: #1d4ed8;
            border-radius: 10px;
            padding: 12px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            color: #334155;
            font-size: 14px;
            font-weight: 600;
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

        .error-message {
            margin: 6px 0 0;
            color: #dc2626;
            font-size: 13px;
        }

        .btn-submit {
            width: 100%;
            border: none;
            border-radius: 10px;
            background: #2563eb;
            color: #ffffff;
            padding: 12px 16px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 8px;
        }

        .btn-submit:hover {
            background: #1d4ed8;
        }

        .auth-link {
            margin-top: 18px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }

        .auth-link a {
            color: #2563eb;
            font-weight: 700;
        }

        .auth-link a:hover {
            color: #1d4ed8;
        }
    </style>

    <div class="auth-wrapper">
        <div class="auth-card">
            <h1 class="auth-title">Login</h1>
            <p class="auth-subtitle">Welcome back to Library App</p>

            @if (session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.authenticate') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email', session('registered_email')) }}"
                        class="form-input"
                        placeholder="Enter your email"
                    >
                    @error('email')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="form-input"
                        placeholder="Enter your password"
                    >
                    @error('password')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">Login</button>
            </form>

            <div class="auth-link">
                Don't have an account?
                <a href="{{ route('register.index') }}">Register here</a>
            </div>
        </div>
    </div>
</x-main-layout>