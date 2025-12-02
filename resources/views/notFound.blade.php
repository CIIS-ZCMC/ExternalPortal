<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Account Not Found — Register</title>
    <style>
        :root {
            --bg: #f7fafc;
            --card: #ffffff;
            --muted: #6b7280;
            --accent: #e11d48;
            --btn: #111827
        }

        * {
            box-sizing: border-box
        }

        html,
        body {
            height: 100%
        }

        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            background: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px
        }

        .card {
            width: 100%;
            max-width: 720px;
            background: var(--card);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.08);
            padding: 28px
        }

        .header {
            display: flex;
            gap: 16px;
            align-items: center
        }

        .icon-wrap {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            background: #fff6f7;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid rgba(225, 29, 72, 0.08)
        }

        .icon {
            width: 34px;
            height: 34px;
            display: block
        }

        h1 {
            margin: 0;
            font-size: 20px;
            color: #0f172a
        }

        p.lead {
            margin: 6px 0 0;
            color: var(--muted)
        }

        .body {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 20px;
            margin-top: 20px
        }

        @media (max-width:880px) {
            .body {
                grid-template-columns: 1fr;
            }

            .aside {
                order: 2
            }
        }

        .notice {
            background: #fff7f9;
            border: 1px solid rgba(225, 29, 72, 0.06);
            padding: 16px;
            border-radius: 10px;
            color: #6b2a2a
        }

        .muted {
            color: var(--muted);
            font-size: 14px
        }

        .actions {
            display: flex;
            flex-direction: column;
            gap: 12px
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 600;
            border: 0;
            cursor: pointer
        }

        .btn-primary {
            background: var(--btn);
            color: white
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #e6e7eb;
            color: #0f172a
        }

        .btn-link {
            background: transparent;
            color: var(--muted);
            text-decoration: underline;
            padding: 8px 0
        }

        .profile {
            display: flex;
            gap: 12px;
            align-items: center
        }

        .avatar {
            width: 72px;
            height: 72px;
            border-radius: 12px;
            background: linear-gradient(135deg, #eef2ff, #fce7f3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #0f172a
        }

        .email {
            font-weight: 600;
            color: #0f172a
        }

        .small {
            font-size: 13px;
            color: var(--muted)
        }

        .footer {
            margin-top: 18px;
            font-size: 13px;
            color: var(--muted)
        }

        .contact {
            color: #0f172a;
            font-weight: 600;
            text-decoration: none
        }
    </style>
</head>

<body>
    <div class="card" role="dialog" aria-labelledby="title" aria-describedby="desc">
        <div class="header">
            <div class="icon-wrap" aria-hidden>
                <!-- exclamation circle -->
                <svg class="icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden>
                    <path d="M12 9v4" stroke="#e11d48" stroke-width="1.6" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M12 17h.01" stroke="#e11d48" stroke-width="1.6" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M21 12A9 9 0 1112 3a9 9 0 019 9z" stroke="#e11d48" stroke-width="1.6"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div>
                <h1 id="title">Account not found</h1>
                <p class="lead" id="desc">We couldn't find an account associated with your Google sign-in. Please
                    register first to continue.</p>
            </div>
        </div>

        <div class="body">
            <div>
                <div class="notice" role="status">
                    <strong>Heads up:</strong>
                    <div style="margin-top:8px">It looks like the Google account you used to sign in (<span
                            id="user-email">{{ $email }}</span>) isn't registered in our system. Registering will
                        link your Google email to a new account so you can access the portal.</div>
                </div>




            </div>

            <aside class="aside">
                <div style="margin-top:12px;margin-bottom:10px">
                    <div class="profile">
                        <div class="avatar" aria-hidden>{{ substr($email, 0, 1) }}</div>
                        <div>
                            <div class="email" id="display-email">{{ $email }}</div>
                            <div class="small">Signed in with Google</div>
                        </div>
                    </div>


                </div>
                <div class="actions">
                    <button onclick="window.location.href='portal/register?email_address={{ $email }}'"
                        class="btn btn-primary" id="btn-register" type="button">Register with this Google
                        account</button>
                    <button class="btn btn-outline" id="btn-retry" type="button">Try signing in again</button>

                </div>
            </aside>
        </div>
    </div>

    <script>
        document.getElementById('btn-retry').addEventListener('click', () => {
            // Trigger your Google Sign-In flow again
            window.location.href = '/auth/google';
        });
    </script>
</body>

</html>
