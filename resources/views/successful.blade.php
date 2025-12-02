<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registration Successful</title>
    <style>
        body {
            margin: 0;
            background: #f9fafb;
            font-family: "Inter", sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        .card {
            background: #fff;
            padding: 40px 32px;
            border-radius: 20px;
            max-width: 420px;
            width: 100%;
            box-shadow: 0px 4px 30px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .icon {
            width: 70px;
            height: 70px;
            background: #e8fdef;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 20px;
        }

        .icon svg {
            width: 40px;
            height: 40px;
            stroke: #16a34a;
        }

        h1 {
            margin: 0;
            font-size: 24px;
            color: #111827;
            font-weight: 600;
        }

        p {
            font-size: 15px;
            color: #6b7280;
            margin-top: 6px;
        }

        .redirect {
            font-size: 14px;
            margin-top: 16px;
            color: #555;
        }

        .btn {
            display: inline-block;
            background: #111827;
            padding: 12px 18px;
            border-radius: 10px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            margin-top: 22px;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="icon">
            <!-- Check icon -->
            <svg fill="none" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
        </div>

        <h1>Registration Successful 🎉</h1>
        <p>Your account has been created. </p>


        <p style="margin-top: 6px; color:#374151;">
            Kindly proceed to the <strong>Human Resource Office ( HR )</strong> or Innovation and Innovation System Unit
            <strong> ( IISU )</strong>
            to complete your <strong>Biometric Registration</strong>.
        </p>

        <p style="margin-top: 6px; color:#374151;">
            Plaese provide them this ID for their convenience : <br><strong
                style="color: #2563eb;font-size:20px">{{ $biometric_id }}</strong>
        </p>
        {{-- 
        <div class="redirect">
            Redirecting to login in <span id="timer">5</span> seconds...
        </div> --}}

        <a href="portal/login" class="btn">Go to Login Now</a>
    </div>

    {{-- <script>
        let seconds = 5;
        const timerElement = document.getElementById("timer");

        const countdown = setInterval(() => {
            seconds--;
            timerElement.textContent = seconds;

            if (seconds <= 0) {
                clearInterval(countdown);
                window.location.href = "portal/login";
            }
        }, 1000);
    </script> --}}
</body>

</html>
