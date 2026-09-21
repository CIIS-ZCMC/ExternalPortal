<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Authentication &bull; ZCMC External Portal</title>
    <link rel="shortcut icon" href="{{ asset('asset/zcmc.png') }}" type="image/png">

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
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
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
            background-color: #0f172a;
            background-image:
                radial-gradient(at 10% 20%, rgba(37, 99, 235, 0.25) 0px, transparent 50%),
                radial-gradient(at 90% 80%, rgba(14, 165, 233, 0.2) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(30, 41, 59, 0.5) 0px, transparent 60%);
        }
    </style>
</head>

<body class="hero-pattern min-h-screen text-slate-800 antialiased flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl shadow-black/40 border border-slate-100/20 overflow-hidden p-8 sm:p-10 relative">
        <!-- Top branding -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-slate-50 ring-1 ring-slate-900/10 p-2.5 shadow-md shadow-slate-200 mb-4">
                <img src="{{ asset('asset/zcmc.png') }}" alt="ZCMC Seal" class="w-full h-full object-contain">
            </div>
            
            <div class="mb-2">
                <span class="inline-block px-3 py-0.5 rounded-full text-[11px] font-bold tracking-wide uppercase bg-blue-50 text-blue-700 border border-blue-100">
                    Administrator Access
                </span>
            </div>

            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                ZCMC Admin Console
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                External Employee &amp; Biometrics Management System
            </p>
        </div>

        <!-- Session error -->
        @session('error')
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200/80 text-rose-800 text-sm flex items-start space-x-2.5">
                <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="text-xs text-rose-700 font-medium leading-relaxed">
                    {{ session('error') }}
                </div>
            </div>
        @endsession

        <!-- Form -->
        <form action="/admin/signin" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="employeeId" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    Authorized Employee ID
                </label>
                <div class="relative flex items-center rounded-xl border border-slate-200 bg-slate-50/50 focus-within:border-blue-600 focus-within:ring-2 focus-within:ring-blue-600/20 transition">
                    <div class="pl-4 text-slate-400 pointer-events-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <input 
                        type="number" 
                        name="employeeId" 
                        id="employeeId" 
                        required
                        autofocus
                        class="w-full py-3.5 pl-3 pr-4 text-sm text-slate-900 bg-transparent placeholder-slate-400 focus:outline-none"
                        placeholder="Enter your registered employee ID" 
                    />
                </div>
            </div>

            <!-- Submit -->
            <button 
                type="submit"
                class="w-full py-3.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-md hover:shadow-lg transition-all focus:outline-none focus:ring-4 focus:ring-blue-500/30 flex items-center justify-center space-x-2"
            >
                <span>Authorize &amp; Enter Console</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <a href="{{ route('portal.login') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition inline-flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Return to External Personnel Sign In
            </a>
            <p class="text-[11px] text-slate-400 mt-4">
                &copy; {{ date('Y') }} Zamboanga City Medical Center &bull; IT Division
            </p>
        </div>
    </div>
</body>

</html>
