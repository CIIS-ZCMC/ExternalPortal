<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sign In &bull; ZCMC External Portal</title>
    <link rel="shortcut icon" href="https://portal.zcmc.online/assets/zcmc-DW37XhWu.png" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN + Vite compiled assets for bulletproof rendering -->
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

        .hero-pattern {
            background-color: #082d4d;
            background-image: 
                radial-gradient(at 10% 20%, rgba(28, 181, 114, 0.22) 0px, transparent 50%),
                radial-gradient(at 90% 80%, rgba(16, 100, 163, 0.45) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(15, 118, 110, 0.2) 0px, transparent 60%);
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
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

<body class="bg-slate-50 min-h-screen text-slate-800 antialiased flex items-center justify-center p-3 sm:p-6 lg:p-10">

    <!-- Container Card -->
    <div class="w-full max-w-5xl bg-white rounded-3xl shadow-2xl shadow-slate-200/60 border border-slate-100 overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[640px]">
        
        <!-- Left Column: Hospital Brand & Info Panel (Desktop/Tablet) -->
        <div class="hero-pattern lg:col-span-5 p-8 sm:p-10 lg:p-12 text-white flex flex-col justify-between relative overflow-hidden">
            
            <!-- Subtle decorative background rings -->
            <div class="absolute -top-24 -left-24 w-72 h-72 rounded-full border border-white/10 pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 rounded-full border border-emerald-400/10 pointer-events-none"></div>
            
            <!-- Top branding section -->
            <div class="relative z-10">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-white p-2 shadow-lg shadow-black/20 flex items-center justify-center ring-4 ring-white/10">
                        <img src="{{ asset('asset/zcmc.png') }}" alt="ZCMC Official Seal" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold tracking-wide uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                            Department of Health
                        </span>
                        <h1 class="text-sm font-semibold tracking-wide text-slate-200 mt-0.5">Republic of the Philippines</h1>
                    </div>
                </div>

                <div class="space-y-2 mt-8">
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-tight">
                        Zamboanga City Medical Center
                    </h2>
                    <p class="text-emerald-300 font-medium text-sm sm:text-base">
                        External Personnel &amp; Affiliate Portal
                    </p>
                </div>

                <div class="mt-8 space-y-4">
                    <div class="glass-panel rounded-2xl p-4 flex items-start space-x-3.5 text-xs sm:text-sm text-slate-200">
                        <div class="p-2 rounded-xl bg-emerald-500/20 text-emerald-400 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-white">Daily Time Record (DTR)</p>
                            <p class="text-slate-300 text-xs mt-0.5">Real-time attendance tracking, biometric logs, and official duty validation.</p>
                        </div>
                    </div>

                    <div class="glass-panel rounded-2xl p-4 flex items-start space-x-3.5 text-xs sm:text-sm text-slate-200">
                        <div class="p-2 rounded-xl bg-sky-500/20 text-sky-400 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-white">Secure External Access</p>
                            <p class="text-slate-300 text-xs mt-0.5">Protected access for visiting physicians, trainees, agency staff &amp; contractors.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Support & Footer info -->
            <div class="mt-8 pt-6 border-t border-white/15 text-xs text-slate-300 flex items-center justify-between relative z-10">
                <div class="flex items-center space-x-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>System Operational</span>
                </div>
                <span class="text-slate-400">v2.4 &bull; ZCMC IT</span>
            </div>
        </div>

        <!-- Right Column: Login Form Container -->
        <div class="lg:col-span-7 p-6 sm:p-10 lg:p-14 flex flex-col justify-between bg-white" id="loginBox">
            
            <div class="max-w-md w-full mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <div class="mb-3">
                        <span class="inline-block text-xs font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100">
                            Account Sign In
                        </span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Welcome Back</h2>
                    <p class="text-slate-500 text-sm mt-1.5">Enter your credentials to access your DTR logs and profile.</p>
                </div>

                <!-- Error Alert -->
                @if (session('error'))
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-800 text-sm flex items-start space-x-3 animate-fade-in">
                        <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="flex-1">
                            <p class="font-semibold">Authentication Notice</p>
                            <p class="text-xs text-rose-700 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-sm flex items-start space-x-3 animate-fade-in">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1">
                            <p class="font-semibold">Success</p>
                            <p class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Form -->
                <form action="{{ route('login.submit') }}" method="POST" id="loginForm" class="space-y-5">
                    @csrf
                    
                    <!-- Username Field -->
                    <div>
                        <label for="username" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Username / Employee ID
                        </label>
                        <div class="relative flex items-center rounded-xl border border-slate-200 bg-slate-50/50 transition-all duration-200 input-focus-ring">
                            <div class="pl-4 text-slate-400 flex items-center pointer-events-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="username" 
                                id="username" 
                                required 
                                value="{{ old('username') }}"
                                autocomplete="username"
                                placeholder="e.g. jdelacruz or 8001"
                                class="w-full py-3.5 pl-3 pr-4 text-sm text-slate-900 bg-transparent placeholder-slate-400 focus:outline-none"
                            >
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Password
                            </label>
                            <a href="{{ route('portal.forgotPassword') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-800 transition">
                                Forgot password?
                            </a>
                        </div>
                        <div class="relative flex items-center rounded-xl border border-slate-200 bg-slate-50/50 transition-all duration-200 input-focus-ring">
                            <div class="pl-4 text-slate-400 flex items-center pointer-events-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input 
                                type="password" 
                                name="password" 
                                id="password" 
                                required 
                                autocomplete="current-password"
                                placeholder="Enter your password"
                                class="w-full py-3.5 pl-3 pr-11 text-sm text-slate-900 bg-transparent placeholder-slate-400 focus:outline-none"
                            >
                            <!-- Password Toggle Eye Button -->
                            <button 
                                type="button" 
                                id="togglePasswordBtn" 
                                aria-label="Toggle password visibility"
                                class="absolute right-3 p-1.5 text-slate-400 hover:text-slate-600 focus:outline-none transition rounded-lg"
                            >
                                <!-- Eye Icon (Show) -->
                                <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <!-- Eye-slash Icon (Hide) -->
                                <svg id="eyeSlashIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        id="loginBtn" 
                        class="btn-gradient w-full py-3.5 px-6 rounded-xl text-white font-semibold text-sm shadow-md flex items-center justify-center space-x-2 focus:ring-4 focus:ring-brand-500/30 outline-none"
                    >
                        <span id="btnText">Sign In to Portal</span>
                        <div id="btnSpinner" class="spinner hidden"></div>
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs uppercase">
                        <span class="bg-white px-4 text-slate-400 font-semibold tracking-wider">Or continue with</span>
                    </div>
                </div>

                <!-- Google OAuth Sign-in Button -->
                <a 
                    href="{{ route('auth.google') }}" 
                    class="w-full py-3.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50/80 text-slate-700 font-semibold text-sm transition-all duration-200 flex items-center justify-center space-x-3 shadow-sm hover:shadow hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-200"
                >
                    <img src="{{ asset('asset/googleLogin.png') }}" alt="Google Logo" class="w-5 h-5 object-contain">
                    <span>Continue with Google Workspace</span>
                </a>

                <!-- Register Footer -->
                <div class="mt-8 text-center pt-6 border-t border-slate-100">
                    <p class="text-sm text-slate-600">
                        Don't have an external account yet?
                        <a href="{{ route('portal.register') }}" class="font-bold text-emerald-600 hover:text-emerald-700 hover:underline inline-flex items-center ml-1">
                            Register now
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </p>
                </div>
            </div>

            <!-- Bottom Disclaimer -->
            <div class="mt-8 text-center text-xs text-slate-400">
                <p>&copy; {{ date('Y') }} Zamboanga City Medical Center. All rights reserved.</p>
                <p class="mt-1">Authorized personnel only &bull; Republic Act No. 10173 (Data Privacy Act of 2012)</p>
            </div>
        </div>
    </div>

    <!-- Client Script -->
    <script>
        // Password Visibility Toggle
        const toggleBtn = document.getElementById("togglePasswordBtn");
        const passwordInput = document.getElementById("password");
        const eyeIcon = document.getElementById("eyeIcon");
        const eyeSlashIcon = document.getElementById("eyeSlashIcon");

        toggleBtn.addEventListener("click", () => {
            const isPassword = passwordInput.getAttribute("type") === "password";
            passwordInput.setAttribute("type", isPassword ? "text" : "password");
            eyeIcon.classList.toggle("hidden", isPassword);
            eyeSlashIcon.classList.toggle("hidden", !isPassword);
        });

        // Submit Button Loading State
        const loginForm = document.getElementById("loginForm");
        const loginBtn = document.getElementById("loginBtn");
        const btnText = document.getElementById("btnText");
        const btnSpinner = document.getElementById("btnSpinner");
        const loginBox = document.getElementById("loginBox");

        loginForm.addEventListener("submit", () => {
            loginBtn.disabled = true;
            loginBtn.classList.add("submitting-state");
            btnText.textContent = "Verifying Credentials...";
            btnSpinner.classList.remove("hidden");
        });
    </script>
</body>

</html>
