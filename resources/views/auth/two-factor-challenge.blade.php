<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Two-Step Verification &bull; {{ config('app.name', 'ZCMC External Portal') }}</title>
    <link rel="shortcut icon" href="{{ asset('asset/zcmc.png') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        * {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .digit-box {
            width: 48px;
            height: 56px;
            text-align: center;
            font-size: 24px;
            font-family: monospace;
            font-weight: 700;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            outline: none;
            transition: all 0.15s ease;
            background-color: #f8fafc;
        }
        .digit-box:focus {
            background-color: #ffffff;
            border-color: #059669;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-emerald-700 via-emerald-600 to-teal-700 p-8 text-white text-center relative overflow-hidden">
        <!-- Background decorative circles -->
        <div class="absolute -top-12 -right-12 w-36 h-36 rounded-full bg-white/10 pointer-events-none"></div>
        <div class="absolute -bottom-12 -left-12 w-36 h-36 rounded-full bg-white/5 pointer-events-none"></div>

        <div class="relative z-10">
            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg p-2 ring-4 ring-white/20">
                <img src="{{ asset('asset/zcmc.png') }}" alt="ZCMC Official Seal" class="w-full h-full object-contain">
            </div>
            <h1 class="text-xl font-extrabold tracking-tight">Two-Step Verification</h1>
            <p class="text-xs text-emerald-100 mt-1 font-medium">
                Hello, {{ method_exists($user, 'fullName') ? $user->fullName() : ($user->name ?? $user->email ?? $user->username) }}
            </p>
        </div>
    </div>

    <!-- Verification Body -->
    <div class="p-8 space-y-6">
        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-2xl p-4 space-y-1">
                @foreach ($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="font-semibold">{{ $error }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('2fa.verify') }}" method="POST" id="challenge-form" class="space-y-6">
            @csrf
            <input type="hidden" name="code" id="final-code">
            <input type="hidden" name="is_recovery_code" id="is-recovery-input" value="0">

            <!-- TOTP 6-Box Input Container -->
            <div id="totp-container" class="space-y-4">
                <label class="block text-xs font-bold text-slate-700 text-center uppercase tracking-wider">
                    Enter 6-digit code from Google Authenticator
                </label>

                <!-- 6 Segmented Digit Inputs -->
                <div class="flex justify-center gap-2" id="digit-boxes">
                    <input type="text" inputmode="numeric" maxlength="1" class="digit-box" autofocus />
                    <input type="text" inputmode="numeric" maxlength="1" class="digit-box" />
                    <input type="text" inputmode="numeric" maxlength="1" class="digit-box" />
                    <input type="text" inputmode="numeric" maxlength="1" class="digit-box" />
                    <input type="text" inputmode="numeric" maxlength="1" class="digit-box" />
                    <input type="text" inputmode="numeric" maxlength="1" class="digit-box" />
                </div>

                <!-- 30-Second Live Timer Indicator -->
                <div class="flex items-center justify-center gap-2 text-[11px] text-slate-400">
                    <svg class="w-3.5 h-3.5 text-emerald-500 animate-spin" style="animation-duration: 3s;" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Rotating code updates every 30 seconds</span>
                </div>
            </div>

            <!-- Emergency Recovery Code Container (Initially Hidden) -->
            <div id="recovery-container" class="hidden space-y-3">
                <label class="block text-xs font-bold text-slate-700 text-center uppercase tracking-wider">
                    Enter 8-character Emergency Recovery Code
                </label>
                <input
                    type="text"
                    id="recovery-input"
                    placeholder="ABCD-1234"
                    autocomplete="off"
                    class="w-full text-center uppercase tracking-widest font-mono text-xl font-bold py-3.5 border-2 border-slate-300 rounded-2xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none"
                />
                <p class="text-[11px] text-slate-400 text-center">
                    Each emergency recovery code can only be used once.
                </p>
            </div>

            <button
                type="submit"
                id="submit-btn"
                class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl transition shadow-lg shadow-emerald-600/20 text-sm tracking-wide"
            >
                Verify & Sign In
            </button>
        </form>

        <!-- Toggle Recovery Code -->
        <div class="text-center pt-2 border-t border-slate-100">
            <button
                type="button"
                onclick="toggleRecovery()"
                id="toggle-recovery-btn"
                class="text-xs text-emerald-600 hover:text-emerald-700 font-bold hover:underline"
            >
                Don't have your phone? Use emergency recovery code
            </button>
        </div>

        <div class="text-center">
            <a href="{{ route('portal.login') }}" class="text-xs text-slate-400 hover:text-slate-600 font-medium">
                &larr; Cancel and return to login
            </a>
        </div>
    </div>
</div>

<script>
    const boxes = document.querySelectorAll('.digit-box');
    const finalCode = document.getElementById('final-code');
    const form = document.getElementById('challenge-form');
    let isRecovery = false;
    let isSubmitting = false;

    function doSubmit(code) {
        if (isSubmitting) return;
        if (!code || (!isRecovery && code.length < 6)) return;

        isSubmitting = true;
        finalCode.value = code;

        const btn = document.getElementById('submit-btn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="inline-flex items-center gap-2 justify-center"><svg class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Verifying...</span>';
        }

        boxes.forEach(b => b.readOnly = true);
        const recoveryInput = document.getElementById('recovery-input');
        if (recoveryInput) recoveryInput.readOnly = true;

        form.submit();
    }

    // Handle typing and auto-advancing
    boxes.forEach((box, index) => {
        box.addEventListener('input', (e) => {
            const val = e.target.value.replace(/\D/g, '');
            e.target.value = val ? val[0] : '';

            if (val && index < boxes.length - 1) {
                boxes[index + 1].focus();
            }

            checkAndAutoSubmit();
        });

        box.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !box.value && index > 0) {
                boxes[index - 1].focus();
            }
        });
    });

    // Handle pasting the full 6-digit code
    boxes[0].parentElement.addEventListener('paste', (e) => {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
        if (pasted.length >= 6) {
            boxes.forEach((box, i) => { box.value = pasted[i] || ''; });
            boxes[5].focus();
            checkAndAutoSubmit();
        }
    });

    function checkAndAutoSubmit() {
        const code = Array.from(boxes).map(b => b.value).join('');
        if (code.length === 6) {
            doSubmit(code);
        }
    }

    function toggleRecovery() {
        isRecovery = !isRecovery;
        document.getElementById('totp-container').classList.toggle('hidden', isRecovery);
        document.getElementById('recovery-container').classList.toggle('hidden', !isRecovery);
        document.getElementById('is-recovery-input').value = isRecovery ? "1" : "0";

        if (isRecovery) {
            document.getElementById('recovery-input').focus();
            document.getElementById('toggle-recovery-btn').innerText = "Use 6-digit Authenticator code instead";
        } else {
            boxes[0].focus();
            document.getElementById('toggle-recovery-btn').innerText = "Don't have your phone? Use emergency recovery code";
        }
    }

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const code = isRecovery 
            ? document.getElementById('recovery-input').value.trim() 
            : Array.from(boxes).map(b => b.value).join('');
        doSubmit(code);
    });
</script>
</body>
</html>
