<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZCMC External Portal Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="https://portal.zcmc.online/assets/zcmc-DW37XhWu.png" type="image/x-icon">

    <style>
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
            transition: opacity 0.5s ease;
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
            opacity: 0.4;
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
            margin-bottom: 15px;
            font-weight: 600;
            font-size: 22px;
            color: #1064a3;
        }

        p {
            text-align: center;
            font-size: 14px;
            color: #444;
            margin-bottom: 25px;
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
            transition: 0.3s;
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

        .footer-text {
            margin-top: 18px;
            text-align: center;
            font-size: 13px;
            color: #777;
        }

        .footer-text a {
            color: #1064a3;
            text-decoration: none;
            font-weight: 500;
        }

        .footer-text a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            color: #fff;
            font-size: 14px;
        }

        .alert-danger {
            background-color: #dc3545;
        }
    </style>
</head>

<body>

    <div class="login-wrapper" id="loginBox">
        <div class="logo">
            <img src="{{ asset('asset/zcmc.png') }}" alt="ZCMC Logo">
        </div>
        <h2>ZCMC External Portal</h2>
        <p>Employee DTR</p>

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="post" id="loginForm">
            @csrf
            <div class="form-group">
                <label for="empid">Username</label>
                <input name="username" required type="text" id="empid" placeholder="Enter Username">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input name="password" required type="password" id="password" placeholder="Enter Password">
            </div>

            <button type="submit" id="loginBtn">
                <span>Login</span>
                <div class="spinner"></div>
            </button>
        </form>

        <button style="background-color:white;color:#333"
            className="mt-4 text-primary shadow-lg h-15 w-full flex items-center justify-center gap-2 border border-gray-200 rounded-md p-2 bg-white hover:bg-gray-100"
            onClick="window.location.href = '/auth/google'">
            <img src="{{ asset('asset/googleLogin.png') }}" alt="Google login" style="width:17px;height:17px" />
            Login with Google
        </button>

        <div class="footer-text">
            <p><a href="/portal/register">Register</a></p>
        </div>
    </div>

    <script>
        const form = document.getElementById("loginForm");
        const button = document.getElementById("loginBtn");
        const wrapper = document.getElementById("loginBox");

        form.addEventListener("submit", () => {
            button.classList.add("loading");
            wrapper.classList.add("submitting");
            button.disabled = true;
        });
    </script>

</body>

</html>
