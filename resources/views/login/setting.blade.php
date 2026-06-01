<x-main-layout>
    <style>
        .setting-wrapper {
            max-width: 560px;
            margin: 0 auto;
        }

        .setting-card {
            background: #ffffff;
            border: 1px solid #dbeafe;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        }

        .setting-title {
            margin: 0 0 6px;
            color: #1d4ed8;
            font-size: 28px;
            font-weight: 800;
        }

        .setting-subtitle {
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
    </style>

    <div class="setting-wrapper">
        <div class="setting-card">
            <h1 class="setting-title">Setting</h1>
            <p class="setting-subtitle">Change your account password.</p>

            @if (session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.update-password') }}">
                @csrf

                <div class="form-group">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input
                        type="password"
                        name="current_password"
                        id="current_password"
                        class="form-input"
                        placeholder="Enter current password"
                    >

                    @error('current_password')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">New Password</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-input"
                        placeholder="Enter new password"
                    >

                    @error('password')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="form-input"
                        placeholder="Confirm new password"
                    >
                </div>

                <div class="button-row">
                    <a href="{{ route('home') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</x-main-layout>