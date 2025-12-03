<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <link rel="shortcut icon" href="https://portal.zcmc.online/assets/zcmc-DW37XhWu.png" type="image/x-icon">
    <title>Check your email</title>
    <style>
        /* Mobile friendly styles */
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6fb;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }

        .card {
            background: #ffffff;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 6px 18px rgba(20, 28, 52, 0.08);
        }

        h1 {
            margin: 0 0 8px 0;
            font-size: 20px;
            color: #10203f;
        }

        p {
            margin: 0 0 16px 0;
            color: #4b5563;
            line-height: 1.5;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 8px;
            background: #2563eb;
            color: #ffffff;
            font-weight: 600;
        }

        .muted {
            color: #9aa3b2;
            font-size: 13px;
        }

        .preheader {
            display: none !important;
            visibility: hidden;
            opacity: 0;
            color: transparent;
            height: 0;
            width: 0;
        }

        @media (max-width:480px) {
            .card {
                padding: 20px;
                border-radius: 10px;
            }

            h1 {
                font-size: 18px;
            }
        }
    </style>
</head>

<body>
    <!-- Preheader text: shows in inbox preview but hidden in email body -->
    <div class="preheader">Please check your inbox — we sent important information to your email address.</div>

    <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
        style="background-color:#f4f6fb; padding:40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" class="container" cellpadding="0" cellspacing="0" width="600">
                    <tr>
                        <td style="text-align:center; padding-bottom:18px;">
                            <!-- logo placeholder -->

                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="card">
                                <h1>Check your email</h1>
                                <p>Hi {{ $user['first_name'] }},</p>
                                <p>We just sent an important message to <strong>{{ $user['email'] }}</strong>. Please
                                    open your inbox and follow the instructions there to complete the process.</p>

                                <p style="margin-top:6px; margin-bottom:18px;">If you don't see the email, check your
                                    spam or promotions folder — sometimes it lands there. If it's still missing, you can
                                    request a new message.</p>

                                <p style="text-align:center; margin:22px 0;">
                                    <!-- Primary action button -->
                                    <a class="btn" href="https://mail.google.com/mail/u/0/#inbox" rel="noopener">Open
                                        my
                                        email</a>
                                 
                                </p>


                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding-top:18px; text-align:center; color:#9aa3b2; font-size:13px;">
                            <div>© {{ date('Y') }} Zamboanga City Medical Center. All rights reserved.</div>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
