<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZCMC External Portal - Reset Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="https://portal.zcmc.online/assets/zcmc-DW37XhWu.png" type="image/x-icon">

    <style>
        /* CSS is identical to the Forgot Password page to keep branding consistent */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Inter", sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1064a3, #1cb572);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-wrapper {
            background: #ffffff;
            width: 400px;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .submitting {
            opacity: 0.5;
            pointer-events: none;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            width: 80px;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 22px;
            color: #1064a3;
        }

        p {
            text-align: center;
            font-size: 13px;
            color: #666;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            font-weight: 500;
            font-size: 14px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-top: 7px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
        }

        input:focus {
            border-color: #1cb572;
            outline: none;
            box-shadow: 0 0 6px rgba(28, 181, 114, 0.3);
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            background: #1064a3;
            border: none;
            color: #fff;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        button:hover {
            background: #0a4d80;
        }

        .spinner {
            width: 18px;
            height: 18px;
            border: 3px solid #fff;
            border-top: 3px solid transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            display: none;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .loading .spinner {
            display: inline-block;
        }

        .loading span {
            display: none;
        }

        .alert-danger {
            background-color: #dc3545;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 8px;
            font-size: 13px;
            color: #fff;
        }
    </style>
</head>

<body>

    <div class="login-wrapper" id="actionBox">
        <div class="logo">
            <img src="https://portal.zcmc.online/assets/zcmc-DW37XhWu.png" alt="ZCMC Logo">
        </div>
        <h2>Create New Password</h2>
        <p>Your identity is verified. Please set a strong new password for your account.</p>

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('portal.savePassword') }}" method="POST" id="mainForm">
            @csrf
            {{-- <input type="hidden" name="token" value="{{ $token }}"> --}}
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" name="password" id="password" placeholder="Enter new password" required
                    autofocus>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    placeholder="Repeat new password" required>
            </div>

            <button type="submit" id="submitBtn">
                <div class="spinner"></div>
                <span>Update Password</span>
            </button>
        </form>
    </div>

    <script>
        const form = document.getElementById('mainForm');
        const btn = document.getElementById('submitBtn');
        const box = document.getElementById('actionBox');

        const password = document.getElementById('password');
        const password_confirmation = document.getElementById('password_confirmation');

        form.onsubmit = function() {
            if (password.value !== password_confirmation.value) {
                alert('Passwords do not match');
                return false;
            }
            btn.classList.add('loading');
            box.classList.add('submitting');
        };
    </script>
</body>

</html>
