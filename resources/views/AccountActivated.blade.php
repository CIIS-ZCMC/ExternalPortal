<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="https://portal.zcmc.online/assets/zcmc-DW37XhWu.png" type="image/x-icon">
    <title>Account Activated</title>

    <!-- Auto-redirect (change URL if needed) -->
    {{-- <meta http-equiv="refresh" content="5; url=/portal"> --}}

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f4f6fb;
            font-family: "Inter", system-ui, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: #ffffff;
            width: 90%;
            max-width: 450px;
            padding: 40px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
            animation: fadeIn 0.6s ease-out;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 8px;
            color: #1f2937;
        }

        p {
            font-size: 15px;
            color: #6b7280;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .checkmark {
            font-size: 56px;
            color: #16a34a;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 12px 22px;
            text-decoration: none;
            background: #2563eb;
            color: #fff;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
            transition: .2s;
        }

        .btn:hover {
            background: #1d4ed8;
        }

        .timer {
            margin-top: 12px;
            color: #9ca3af;
            font-size: 14px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="checkmark">✔️</div>
        <h1>Account Activated</h1>
        <p>Your account has been successfully verified and activated.
            You now have access to your DTR.</p>

        <p style="margin-top: 6px; color:#374151;">
            Kindly proceed to the <strong>Human Resource Office ( HR )</strong> or Innovations and Information Systems Unit
            <strong> ( IISU )</strong>
            to complete your <strong>Biometric Registration</strong>.
        </p>

        <p style="margin-top: 6px; color:#374151;">
            Please provide them this ID for their convenience : <br><strong
                style="color: #2563eb;font-size:20px">{{ $biometric_id }}</strong>
        </p>

        <a href="/portal/login" class="btn">Proceed to Login</a>

        {{-- <div class="timer">Redirecting in <span id="seconds">5</span> seconds...</div> --}}
    </div>


    <script>
        let timer = 5;
        const display = document.getElementById("seconds");
        const interval = setInterval(() => {
            timer--;
            display.textContent = timer;
            if (timer <= 0) clearInterval(interval);
        }, 1000);
    </script>

</body>

</html>
