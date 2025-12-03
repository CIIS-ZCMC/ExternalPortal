<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired</title>
    <link rel="shortcut icon" href="https://portal.zcmc.online/assets/zcmc-DW37XhWu.png" type="image/x-icon">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f4f6fb;
            font-family: "Inter", sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: #fff;
            max-width: 420px;
            width: 90%;
            padding: 40px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0px 6px 20px rgba(0, 0, 0, .06);
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

        .btn {
            display: inline-block;
            padding: 12px 22px;
            text-decoration: none;
            background: #2563eb;
            color: #fff;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
            transition: 0.2s;
        }

        .btn:hover {
            background: #1e50c5;
        }

        .icon {
            font-size: 46px;
            color: #ef4444;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="icon">⏳</div>
        <h1>Session Expired</h1>
        <p>Your session has ended for security reasons or inactivity. Please log in again to continue.</p>

        <a href="portal/login" class="btn">Sign In Again</a>
    </div>
</body>

</html>
