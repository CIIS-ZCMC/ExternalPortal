<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Set New Password &bull; ZCMC External Portal</title>
    <link rel="shortcut icon" href="https://portal.zcmc.online/assets/zcmc-DW37XhWu.png" type="image/x-icon">

    <!-- Google Fonts -->
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
                    colors: {
                        brand: {
                            50: '#f0f7fc',
                            100: '#ddedf8',
                            500: '#1064a3',
                            600: '#0c5287',
                            700: '#0a426e',
                            800: '#09375b',
                            900: '#072b47',
                        },
                        emeraldBrand: {
                            50: '#ecfdf5',
                            500: '#1cb572',
                            600: '#15965d',
                            700: '#0f7649',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        * {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .bg-gradient-mesh {
            background-color: #072b47;
            background-image: 
                radial-gradient(at 0% 0%, rgba(28, 181, 114, 0.25) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(16, 100, 163, 0.45) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(15, 118, 110, 0.2) 0px, transparent 60%);
        }

        .input-focus-ring:focus-within {
            border-color: #1064a3;
            box-shadow: 0 0 0 3px rgba(16, 100, 163, 0.15);
        }

        .btn-gradient {
            background: linear-gradient(135deg, #1064a3 0%, #0d7cb8 50%, #15965d 100%);
            background-size: 200% 200%;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-gradient:hover {
            background-position: right center;
            box-shadow: 0 10px 25px -5px rgba(16, 100, 163, 0.4);
            transform: translateY(-1px);
        }

        .btn-gradient:active {
            transform: translateY(0);
        }

        .spinner {
            width: 1.25rem;
            height: 1.25rem;
            border: 2.5px solid rgba(255, 255, 255, 0.3);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .submitting-state {
            opacity: 0.7;
            pointer-events: none;
        }
    </style>
</head>

<body class="bg-slate-100/80 min-h-screen text-slate-800 antialiased flex flex-col justify-between p-4 sm:p-6 lg:p-8">

    <!-- Top Navigation -->
    <div class="max-w-md w-full mx-auto mb-4">
        <a href="{{ route('portal.login') }}" class="inline-flex items-center text-xs font-semibold text-slate-600 hover:text-brand-600 transition space-x-1.5 bg-white/80 backdrop-blur px-3.5 py-2 rounded-xl border border-slate-200 shadow-sm hover:shadow">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Back to Portal Login</span>
        </a>
    </div>

    <!-- Main Card Container -->
    <div class="max-w-md w-full mx-auto bg-white rounded-3xl shadow-xl shadow-slate-200/70 border border-slate-200/80 overflow-hidden" id="actionBox">
        
        <!-- Header Banner -->
        <div class="bg-gradient-mesh text-white p-6 sm:p-8 relative overflow-hidden text-center">
            <!-- Decorative rings -->
            <div class="absolute -right-12 -top-12 w-40 h-40 rounded-full border border-white/10 pointer-events-none"></div>
            <div class="absolute -left-12 -bottom-12 w-40 h-40 rounded-full border border-emerald-400/10 pointer-events-none"></div>

            <div class="relative z-10 flex flex-col items-center">
                <div class="w-16 h-16 rounded-2xl bg-white p-2.5 shadow-lg flex-shrink-0 ring-4 ring-white/10 mb-4">
                    <img src="{{ asset('asset/zcmc.png') }}" alt="ZCMC Official Seal" class="w-full h-full object-contain">
                </div>
                
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 mb-1.5">
                    Security Verification
                </span>
                
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-white">
                    Create New Password
                </h1>
                
                <p class="text-slate-300 text-xs sm:text-sm mt-1.5 max-w-xs leading-relaxed">
                    Identity verified for <strong class="text-emerald-300">{{ $email }}</strong>. Please establish a new secure password.
                </p>
            </div>
        </div>

        <!-- Form Body -->
        <div class="p-6 sm:p-8">
            
            <!-- Validation Errors Alert -->
            @if (isset($errors) && $errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-900 text-sm flex items-start space-x-3 animate-fade-in">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="flex-1">
                        <p class="font-bold">Password Reset Error</p>
                        <p class="text-xs text-rose-700 mt-0.5 leading-relaxed">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('portal.savePassword') }}" method="POST" id="mainForm" class="space-y-5">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                
                <!-- New Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        New Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative flex items-center rounded-xl border border-slate-200 bg-slate-50/50 transition-all duration-200 input-focus-ring">
                        <div class="pl-4 text-slate-400 flex items-center pointer-events-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            required 
                            autofocus
                            autocomplete="new-password"
                            placeholder="Enter new secure password"
                            class="w-full py-3.5 pl-3 pr-11 text-sm text-slate-900 bg-transparent placeholder-slate-400 focus:outline-none"
                        >
                        <button 
                            type="button" 
                            id="togglePassword" 
                            aria-label="Toggle password visibility"
                            class="absolute right-3 p-1 text-slate-400 hover:text-slate-600 focus:outline-none"
                        >
                            <svg class="w-4 h-4 eye-show" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg class="w-4 h-4 eye-hide hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Confirm New Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative flex items-center rounded-xl border border-slate-200 bg-slate-50/50 transition-all duration-200 input-focus-ring">
                        <div class="pl-4 text-slate-400 flex items-center pointer-events-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation" 
                            required 
                            autocomplete="new-password"
                            placeholder="Repeat new password"
                            class="w-full py-3.5 pl-3 pr-11 text-sm text-slate-900 bg-transparent placeholder-slate-400 focus:outline-none"
                        >
                        <button 
                            type="button" 
                            id="toggleConfirm" 
                            aria-label="Toggle password visibility"
                            class="absolute right-3 p-1 text-slate-400 hover:text-slate-600 focus:outline-none"
                        >
                            <svg class="w-4 h-4 eye-show" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg class="w-4 h-4 eye-hide hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Match Status Indicator -->
                <div id="matchNotice" class="hidden text-xs flex items-center space-x-1.5 transition-all"></div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    id="submitBtn" 
                    class="btn-gradient w-full py-3.5 px-6 rounded-xl text-white font-semibold text-sm shadow-md flex items-center justify-center space-x-2 focus:ring-4 focus:ring-brand-500/30 outline-none"
                >
                    <span id="btnText">Update &amp; Save Password</span>
                    <div id="btnSpinner" class="spinner hidden"></div>
                </button>
            </form>

            <!-- Footer Return Link -->
            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <p class="text-sm text-slate-600">
                    Return to authentication?
                    <a href="{{ route('portal.login') }}" class="font-bold text-brand-600 hover:text-brand-700 hover:underline ml-1 inline-flex items-center">
                        Back to Login
                    </a>
                </p>
            </div>
        </div>

        <!-- System Footer -->
        <div class="p-3.5 bg-slate-50 border-t border-slate-100 text-center text-[11px] text-slate-400">
            Zamboanga City Medical Center &bull; Hospital Information System
        </div>
    </div>

    <!-- Bottom Compliance -->
    <div class="text-center text-xs text-slate-400 my-4">
        <p>&copy; {{ date('Y') }} Zamboanga City Medical Center &bull; Data Privacy Act of 2012</p>
    </div>

    <!-- Client Script -->
    <script>
        const form = document.getElementById('mainForm');
        const btn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');
        const box = document.getElementById('actionBox');

        const pwd = document.getElementById('password');
        const pwdConfirm = document.getElementById('password_confirmation');
        const matchNotice = document.getElementById('matchNotice');

        // Password Show/Hide Toggle
        function setupToggle(btnId, inputId) {
            const toggle = document.getElementById(btnId);
            const input = document.getElementById(inputId);
            const eyeShow = toggle.querySelector('.eye-show');
            const eyeHide = toggle.querySelector('.eye-hide');

            toggle.addEventListener('click', () => {
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                eyeShow.classList.toggle('hidden', isPassword);
                eyeHide.classList.toggle('hidden', !isPassword);
            });
        }

        setupToggle('togglePassword', 'password');
        setupToggle('toggleConfirm', 'password_confirmation');

        // Live Password Match Check
        function checkMatch() {
            if (!pwdConfirm.value) {
                matchNotice.classList.add('hidden');
                return;
            }

            matchNotice.classList.remove('hidden');
            if (pwd.value === pwdConfirm.value) {
                matchNotice.className = 'text-xs flex items-center space-x-1.5 text-emerald-600 font-medium';
                matchNotice.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Passwords match</span>
                `;
            } else {
                matchNotice.className = 'text-xs flex items-center space-x-1.5 text-rose-600 font-medium';
                matchNotice.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span>Passwords do not match</span>
                `;
            }
        }

        pwd.addEventListener('input', checkMatch);
        pwdConfirm.addEventListener('input', checkMatch);

        form.addEventListener('submit', (e) => {
            if (pwd.value !== pwdConfirm.value) {
                e.preventDefault();
                checkMatch();
                pwdConfirm.focus();
                return false;
            }

            btn.disabled = true;
            btn.classList.add('submitting-state');
            btnText.textContent = 'Saving Password...';
            btnSpinner.classList.remove('hidden');
        });
    </script>
</body>

</html>
