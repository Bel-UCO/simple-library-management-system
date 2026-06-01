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
            max-width: 520px;
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
            <h1 class="auth-title">Create Account</h1>
            <p class="auth-subtitle">Register as a library member</p>

            <form method="POST" action="{{ route('register.store') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="name">Name</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        class="form-input"
                        placeholder="Enter your name"
                    >
                    @error('name')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="form-input"
                        placeholder="Enter your email"
                    >
                    @error('email')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Phone</label>
                    <input
                        id="phone"
                        type="text"
                        name="phone"
                        value="{{ old('phone') }}"
                        class="form-input"
                        placeholder="Enter your phone number"
                    >
                    @error('phone')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="address">Address</label>
                    <input
                        id="address"
                        type="text"
                        name="address"
                        value="{{ old('address') }}"
                        class="form-input"
                        placeholder="Enter your address"
                    >
                    @error('address')
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

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        class="form-input"
                        placeholder="Confirm your password"
                    >
                </div>

                <button type="submit" class="btn-submit">Register</button>
            </form>

            <div class="auth-link">
                Already have an account?
                <a href="{{ route('login.index') }}">Login here</a>
            </div>
        </div>
    </div>
</x-main-layout>