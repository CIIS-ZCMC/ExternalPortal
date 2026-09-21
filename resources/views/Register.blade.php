<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Personnel Registration &bull; ZCMC External Portal</title>
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
                radial-gradient(at 100% 0%, rgba(16, 100, 163, 0.4) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(15, 118, 110, 0.25) 0px, transparent 60%);
        }

        .input-focus-ring:focus-within {
            border-color: #1064a3;
            box-shadow: 0 0 0 3px rgba(16, 100, 163, 0.12);
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

<body class="bg-slate-100/80 min-h-screen text-slate-800 antialiased py-8 px-4 sm:px-6 lg:px-8">

    <!-- Top Header Navigation -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between">
        <a href="{{ route('portal.login') }}" class="inline-flex items-center text-xs font-semibold text-slate-600 hover:text-brand-600 transition space-x-1.5 bg-white/80 backdrop-blur px-3.5 py-2 rounded-xl border border-slate-200 shadow-sm hover:shadow">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Back to Portal Login</span>
        </a>

        <div class="flex items-center space-x-2 text-xs text-slate-500 font-medium">
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>External Registration Portal</span>
        </div>
    </div>

    <!-- Main Registration Container -->
    <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-xl shadow-slate-200/70 border border-slate-200/80 overflow-hidden" id="registerBox">
        
        <!-- Header Banner -->
        <div class="bg-gradient-mesh text-white p-6 sm:p-8 relative overflow-hidden">
            <!-- Decorative circle -->
            <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full border border-white/10 pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-2xl bg-white p-2.5 shadow-lg flex-shrink-0 ring-4 ring-white/10">
                        <img src="{{ asset('asset/zcmc.png') }}" alt="ZCMC Official Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                Department of Health
                            </span>
                            <span class="text-xs text-slate-300 font-medium">Republic of the Philippines</span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-white mt-1">
                            ZCMC External Personnel Registration
                        </h1>
                        <p class="text-slate-300 text-xs sm:text-sm mt-0.5">
                            Create your account to access the Daily Time Record (DTR) and hospital affiliate services.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Wrapper -->
        <div class="p-6 sm:p-10">
            
            <!-- Global Validation Alerts -->
            @if (isset($errors) && $errors->any())
                <div class="mb-8 p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-sm">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="font-bold">Please correct the following errors before submitting:</p>
                            <ul class="list-disc list-inside mt-2 space-y-1 text-xs text-rose-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if (request()->has('email_address') && !empty(request()->email_address))
                <div class="mb-6 p-4 rounded-2xl bg-sky-50 border border-sky-200 text-sky-900 text-xs sm:text-sm flex items-center space-x-3">
                    <svg class="w-5 h-5 text-sky-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p>
                        Linking Google Account: <strong>{{ request()->email_address }}</strong>. Please complete the form below to finalize your registration.
                    </p>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" id="registerForm" class="space-y-8">
                @csrf
                <input type="hidden" name="email_address" value="{{ $email }}">

                <!-- ============================================== -->
                <!-- SECTION 1: Personal Identification -->
                <!-- ============================================== -->
                <div class="p-6 rounded-2xl bg-slate-50/70 border border-slate-200/80">
                    <div class="flex items-center space-x-3 pb-4 mb-5 border-b border-slate-200">
                        <div class="w-8 h-8 rounded-xl bg-brand-500 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                            1
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800">Personal Information</h2>
                            <p class="text-xs text-slate-500">Legal name as it appears on official government IDs.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4">
                        <!-- First Name -->
                        <div class="lg:col-span-4">
                            <label for="first_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                First Name <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="first_name" 
                                id="first_name" 
                                required 
                                autocomplete="given-name"
                                value="{{ old('first_name') }}"
                                placeholder="e.g. Juan"
                                class="w-full px-3.5 py-2.5 rounded-xl border @error('first_name') border-rose-400 bg-rose-50/40 @else border-slate-200 bg-white @enderror text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                            >
                            @error('first_name')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Middle Name -->
                        <div class="lg:col-span-3">
                            <label for="middle_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Middle Name <span class="text-slate-400 font-normal text-[11px]">(Optional)</span>
                            </label>
                            <input 
                                type="text" 
                                name="middle_name" 
                                id="middle_name" 
                                autocomplete="additional-name"
                                value="{{ old('middle_name') }}"
                                placeholder="e.g. Santos"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                            >
                            @error('middle_name')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="lg:col-span-3">
                            <label for="last_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Last Name <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="last_name" 
                                id="last_name" 
                                required 
                                autocomplete="family-name"
                                value="{{ old('last_name') }}"
                                placeholder="e.g. Dela Cruz"
                                class="w-full px-3.5 py-2.5 rounded-xl border @error('last_name') border-rose-400 bg-rose-50/40 @else border-slate-200 bg-white @enderror text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                            >
                            @error('last_name')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Extension Name -->
                        <div class="lg:col-span-2">
                            <label for="ext_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Suffix <span class="text-slate-400 font-normal text-[11px]">(Jr./III)</span>
                            </label>
                            <input 
                                type="text" 
                                name="ext_name" 
                                id="ext_name" 
                                value="{{ old('ext_name') }}"
                                placeholder="e.g. Jr., III"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                            >
                            @error('ext_name')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- SECTION 2: Contact & Address Information -->
                <!-- ============================================== -->
                <div class="p-6 rounded-2xl bg-slate-50/70 border border-slate-200/80">
                    <div class="flex items-center space-x-3 pb-4 mb-5 border-b border-slate-200">
                        <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                            2
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800">Contact &amp; Address Details</h2>
                            <p class="text-xs text-slate-500">Official communication and verification channels.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Email Address -->
                            <div>
                                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Email Address <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative flex items-center rounded-xl border @error('email') border-rose-400 bg-rose-50/40 @else border-slate-200 bg-white @enderror transition input-focus-ring">
                                    <div class="pl-3.5 text-slate-400 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <input 
                                        type="email" 
                                        name="email" 
                                        id="email" 
                                        required 
                                        autocomplete="email"
                                        value="{{ old('email') ?? $email }}"
                                        placeholder="user@example.com"
                                        class="w-full py-2.5 pl-2.5 pr-3.5 text-sm text-slate-900 bg-transparent placeholder-slate-400 focus:outline-none"
                                    >
                                </div>
                                @error('email')
                                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Contact Number -->
                            <div>
                                <label for="contact_number" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Mobile / Contact Number <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative flex items-center rounded-xl border @error('contact_number') border-rose-400 bg-rose-50/40 @else border-slate-200 bg-white @enderror transition input-focus-ring">
                                    <div class="pl-3.5 text-slate-400 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                    </div>
                                    <input 
                                        type="tel" 
                                        name="contact_number" 
                                        id="contact_number" 
                                        required 
                                        autocomplete="tel"
                                        inputmode="tel"
                                        value="{{ old('contact_number') }}"
                                        placeholder="0917-123-4567"
                                        class="w-full py-2.5 pl-2.5 pr-3.5 text-sm text-slate-900 bg-transparent placeholder-slate-400 focus:outline-none"
                                    >
                                </div>
                                @error('contact_number')
                                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Complete Address -->
                        <div>
                            <label for="address" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Complete Residential Address <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative rounded-xl border @error('address') border-rose-400 bg-rose-50/40 @else border-slate-200 bg-white @enderror transition input-focus-ring">
                                <textarea 
                                    name="address" 
                                    id="address" 
                                    rows="2" 
                                    required 
                                    autocomplete="street-address"
                                    placeholder="House/Unit No., Street Name, Barangay, City/Municipality, Province"
                                    class="w-full p-3 text-sm text-slate-900 bg-transparent placeholder-slate-400 focus:outline-none resize-none"
                                >{{ old('address') }}</textarea>
                            </div>
                            @error('address')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- SECTION 3: Agency & Designation -->
                <!-- ============================================== -->
                <div class="p-6 rounded-2xl bg-slate-50/70 border border-slate-200/80">
                    <div class="flex items-center space-x-3 pb-4 mb-5 border-b border-slate-200">
                        <div class="w-8 h-8 rounded-xl bg-sky-600 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                            3
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800">Employment &amp; Affiliation</h2>
                            <p class="text-xs text-slate-500">Select your parent agency or affiliate institution.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Government Agency -->
                        <div>
                            <label for="select_agency" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Government Agency / Institution <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select 
                                    name="agency" 
                                    id="select_agency" 
                                    required 
                                    class="w-full px-3.5 py-2.5 rounded-xl border @error('agency') border-rose-400 bg-rose-50/40 @else border-slate-200 bg-white @enderror text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition appearance-none pr-10"
                                >
                                    <option value="" disabled {{ old('agency') ? '' : 'selected' }}>-- Select Government Agency --</option>
                                    
                                    <optgroup label="Hospital &amp; Healthcare">
                                        <option value="Zamboanga City Medical Center (ZCMC)" {{ old('agency') == 'Zamboanga City Medical Center (ZCMC)' ? 'selected' : '' }}>Zamboanga City Medical Center (ZCMC)</option>
                                        <option value="Department of Health (DOH)" {{ old('agency') == 'Department of Health (DOH)' ? 'selected' : '' }}>Department of Health (DOH)</option>
                                        <option value="PhilHealth" {{ old('agency') == 'PhilHealth' ? 'selected' : '' }}>PhilHealth</option>
                                        <option value="Food and Drug Administration (FDA)" {{ old('agency') == 'Food and Drug Administration (FDA)' ? 'selected' : '' }}>Food and Drug Administration (FDA)</option>
                                        <option value="Other Hospital / Medical Institution" {{ old('agency') == 'Other Hospital / Medical Institution' ? 'selected' : '' }}>Other Hospital / Medical Institution</option>
                                    </optgroup>

                                    <optgroup label="National Government Agencies">
                                        <option value="Department of Education (DepEd)" {{ old('agency') == 'Department of Education (DepEd)' ? 'selected' : '' }}>Department of Education (DepEd)</option>
                                        <option value="Department of the Interior and Local Government (DILG)" {{ old('agency') == 'Department of the Interior and Local Government (DILG)' ? 'selected' : '' }}>Department of the Interior and Local Government (DILG)</option>
                                        <option value="Department of Social Welfare and Development (DSWD)" {{ old('agency') == 'Department of Social Welfare and Development (DSWD)' ? 'selected' : '' }}>Department of Social Welfare and Development (DSWD)</option>
                                        <option value="Department of Finance (DOF)" {{ old('agency') == 'Department of Finance (DOF)' ? 'selected' : '' }}>Department of Finance (DOF)</option>
                                        <option value="Department of Budget and Management (DBM)" {{ old('agency') == 'Department of Budget and Management (DBM)' ? 'selected' : '' }}>Department of Budget and Management (DBM)</option>
                                        <option value="Department of Science and Technology (DOST)" {{ old('agency') == 'Department of Science and Technology (DOST)' ? 'selected' : '' }}>Department of Science and Technology (DOST)</option>
                                        <option value="Department of Tourism (DOT)" {{ old('agency') == 'Department of Tourism (DOT)' ? 'selected' : '' }}>Department of Tourism (DOT)</option>
                                        <option value="Department of Justice (DOJ)" {{ old('agency') == 'Department of Justice (DOJ)' ? 'selected' : '' }}>Department of Justice (DOJ)</option>
                                        <option value="Department of Agriculture (DA)" {{ old('agency') == 'Department of Agriculture (DA)' ? 'selected' : '' }}>Department of Agriculture (DA)</option>
                                        <option value="Department of Labor and Employment (DOLE)" {{ old('agency') == 'Department of Labor and Employment (DOLE)' ? 'selected' : '' }}>Department of Labor and Employment (DOLE)</option>
                                        <option value="Department of National Defense (DND)" {{ old('agency') == 'Department of National Defense (DND)' ? 'selected' : '' }}>Department of National Defense (DND)</option>
                                        <option value="Department of Transportation (DOTr)" {{ old('agency') == 'Department of Transportation (DOTr)' ? 'selected' : '' }}>Department of Transportation (DOTr)</option>
                                        <option value="Department of Public Works and Highways (DPWH)" {{ old('agency') == 'Department of Public Works and Highways (DPWH)' ? 'selected' : '' }}>Department of Public Works and Highways (DPWH)</option>
                                        <option value="Department of Trade and Industry (DTI)" {{ old('agency') == 'Department of Trade and Industry (DTI)' ? 'selected' : '' }}>Department of Trade and Industry (DTI)</option>
                                        <option value="Department of Environment and Natural Resources (DENR)" {{ old('agency') == 'Department of Environment and Natural Resources (DENR)' ? 'selected' : '' }}>Department of Environment and Natural Resources (DENR)</option>
                                    </optgroup>

                                    <optgroup label="Civil Service &amp; Regulation">
                                        <option value="Civil Service Commission (CSC)" {{ old('agency') == 'Civil Service Commission (CSC)' ? 'selected' : '' }}>Civil Service Commission (CSC)</option>
                                        <option value="Professional Regulation Commission (PRC)" {{ old('agency') == 'Professional Regulation Commission (PRC)' ? 'selected' : '' }}>Professional Regulation Commission (PRC)</option>
                                        <option value="Commission on Audit (COA)" {{ old('agency') == 'Commission on Audit (COA)' ? 'selected' : '' }}>Commission on Audit (COA)</option>
                                        <option value="Government Service Insurance System (GSIS)" {{ old('agency') == 'Government Service Insurance System (GSIS)' ? 'selected' : '' }}>Government Service Insurance System (GSIS)</option>
                                    </optgroup>

                                    <optgroup label="Local Government &amp; Uniformed Personnel">
                                        <option value="Provincial Government" {{ old('agency') == 'Provincial Government' ? 'selected' : '' }}>Provincial Government</option>
                                        <option value="City Government" {{ old('agency') == 'City Government' ? 'selected' : '' }}>City Government</option>
                                        <option value="Municipal Government" {{ old('agency') == 'Municipal Government' ? 'selected' : '' }}>Municipal Government</option>
                                        <option value="Barangay Government" {{ old('agency') == 'Barangay Government' ? 'selected' : '' }}>Barangay Government</option>
                                        <option value="Philippine National Police (PNP)" {{ old('agency') == 'Philippine National Police (PNP)' ? 'selected' : '' }}>Philippine National Police (PNP)</option>
                                        <option value="Armed Forces of the Philippines (AFP)" {{ old('agency') == 'Armed Forces of the Philippines (AFP)' ? 'selected' : '' }}>Armed Forces of the Philippines (AFP)</option>
                                    </optgroup>

                                    <optgroup label="Special &amp; Constitutional Commissions">
                                        <option value="Commission on Elections (COMELEC)" {{ old('agency') == 'Commission on Elections (COMELEC)' ? 'selected' : '' }}>Commission on Elections (COMELEC)</option>
                                        <option value="Commission on Higher Education (CHED)" {{ old('agency') == 'Commission on Higher Education (CHED)' ? 'selected' : '' }}>Commission on Higher Education (CHED)</option>
                                        <option value="Technical Education and Skills Development Authority (TESDA)" {{ old('agency') == 'Technical Education and Skills Development Authority (TESDA)' ? 'selected' : '' }}>Technical Education and Skills Development Authority (TESDA)</option>
                                    </optgroup>

                                    @if (!empty($uniqueAgencies) && count($uniqueAgencies) > 0)
                                        <optgroup label="Registered Affiliate Institutions &amp; Agencies">
                                            @foreach ($uniqueAgencies as $customAgency)
                                                <option value="{{ $customAgency }}" {{ old('agency') == $customAgency ? 'selected' : '' }}>
                                                    {{ $customAgency }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif

                                    <optgroup label="Others">
                                        <option value="Other Government Agency" {{ old('agency') == 'Other Government Agency' ? 'selected' : '' }}>Other Government Agency</option>
                                    </optgroup>
                                </select>

                                <!-- Dropdown Chevron Icon -->
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Dynamic Custom Agency Field -->
                            <div id="input_agency_container" class="mt-3 hidden transition-all duration-300">
                                <label for="input_agency_input" class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                                    Please specify agency name: <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="input_agency_input" 
                                    placeholder="Enter full agency or organization name"
                                    value="{{ old('agency') }}"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-brand-300 bg-brand-50/50 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500"
                                >
                            </div>
                            @error('agency')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Position / Designation -->
                        <div>
                            <label for="position" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Position / Job Title <span class="text-slate-400 font-normal text-[11px]">(Optional)</span>
                            </label>
                            <input 
                                type="text" 
                                name="position" 
                                id="position" 
                                value="{{ old('position') }}"
                                placeholder="e.g. Medical Intern / IT Specialist / Auditor"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                            >
                            @error('position')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- SECTION 4: Security & Credentials -->
                <!-- ============================================== -->
                <div class="p-6 rounded-2xl bg-slate-50/70 border border-slate-200/80">
                    <div class="flex items-center space-x-3 pb-4 mb-5 border-b border-slate-200">
                        <div class="w-8 h-8 rounded-xl bg-slate-800 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                            4
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800">Account Credentials</h2>
                            <p class="text-xs text-slate-500">Create a secure username and password for portal login.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Desired Username -->
                        <div>
                            <label for="username" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Desired Username <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center rounded-xl border @error('username') border-rose-400 bg-rose-50/40 @else border-slate-200 bg-white @enderror transition input-focus-ring">
                                <div class="pl-3.5 text-slate-400 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <input 
                                    type="text" 
                                    name="username" 
                                    id="username" 
                                    required 
                                    autocomplete="username"
                                    value="{{ old('username') }}"
                                    placeholder="Choose a unique username"
                                    class="w-full py-2.5 pl-2.5 pr-3.5 text-sm text-slate-900 bg-transparent placeholder-slate-400 focus:outline-none"
                                >
                            </div>
                            @error('username')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password & Confirm Password -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Password <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative flex items-center rounded-xl border @error('password') border-rose-400 bg-rose-50/40 @else border-slate-200 bg-white @enderror transition input-focus-ring">
                                    <div class="pl-3.5 text-slate-400 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <input 
                                        type="password" 
                                        name="password" 
                                        id="regPassword" 
                                        required 
                                        autocomplete="new-password"
                                        placeholder="Minimum 4 characters"
                                        class="w-full py-2.5 pl-2.5 pr-10 text-sm text-slate-900 bg-transparent placeholder-slate-400 focus:outline-none"
                                    >
                                    <button 
                                        type="button" 
                                        id="toggleRegPassword" 
                                        aria-label="Toggle password visibility"
                                        class="absolute right-2.5 p-1 text-slate-400 hover:text-slate-600 focus:outline-none"
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
                                @error('password')
                                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Confirm Password <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative flex items-center rounded-xl border border-slate-200 bg-white transition input-focus-ring">
                                    <div class="pl-3.5 text-slate-400 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                    </div>
                                    <input 
                                        type="password" 
                                        name="password_confirmation" 
                                        id="regPasswordConfirm" 
                                        required 
                                        autocomplete="new-password"
                                        placeholder="Re-enter password"
                                        class="w-full py-2.5 pl-2.5 pr-10 text-sm text-slate-900 bg-transparent placeholder-slate-400 focus:outline-none"
                                    >
                                    <button 
                                        type="button" 
                                        id="toggleRegConfirm" 
                                        aria-label="Toggle confirm password visibility"
                                        class="absolute right-2.5 p-1 text-slate-400 hover:text-slate-600 focus:outline-none"
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
                        </div>

                        <!-- Live Password Match Indicator -->
                        <div id="passwordMatchStatus" class="hidden text-xs flex items-center space-x-1.5 transition-all"></div>
                    </div>
                </div>

                <!-- Privacy & Terms Notice -->
                <div class="p-4 rounded-xl bg-slate-50 text-slate-500 text-xs leading-relaxed border border-slate-200/60 flex items-start space-x-2.5">
                    <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <p>
                        By submitting this form, you certify that all information submitted is true and accurate. Your information is protected under the <strong>Philippine Data Privacy Act of 2012 (R.A. 10173)</strong> and will only be used for official hospital DTR &amp; affiliate personnel record-keeping.
                    </p>
                </div>

                <!-- Submit Button & Action Links -->
                <div class="pt-4 space-y-4">
                    <button 
                        type="submit" 
                        id="registerBtn" 
                        class="btn-gradient w-full py-4 px-6 rounded-xl text-white font-bold text-base shadow-lg shadow-brand-500/20 flex items-center justify-center space-x-2 focus:ring-4 focus:ring-brand-500/30 outline-none"
                    >
                        <span id="regBtnText">Complete &amp; Submit Registration</span>
                        <div id="regBtnSpinner" class="spinner hidden"></div>
                    </button>

                    <div class="text-center">
                        <p class="text-sm text-slate-600">
                            Already have an external employee account?
                            <a href="{{ route('portal.login') }}" class="font-bold text-brand-600 hover:text-brand-700 hover:underline ml-1">
                                Sign In here
                            </a>
                        </p>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="p-4 bg-slate-50 border-t border-slate-100 text-center text-xs text-slate-400">
            Zamboanga City Medical Center &bull; Dr. Evangelista St., Sta. Catalina, Zamboanga City &bull; Hospital Information System
        </div>
    </div>

    <!-- Client Interaction Script -->
    <script>
        // Dynamic Agency Handling
        const agencySelect = document.getElementById("select_agency");
        const customAgencyContainer = document.getElementById("input_agency_container");
        const customAgencyInput = document.getElementById("input_agency_input");

        function handleAgencyChange() {
            if (agencySelect.value === "Other Government Agency") {
                customAgencyContainer.classList.remove("hidden");
                customAgencyInput.name = "agency";
                customAgencyInput.required = true;
                agencySelect.removeAttribute("name");
                customAgencyInput.focus();
            } else {
                customAgencyContainer.classList.add("hidden");
                customAgencyInput.removeAttribute("name");
                customAgencyInput.required = false;
                agencySelect.name = "agency";
            }
        }

        agencySelect.addEventListener("change", handleAgencyChange);

        // Check on initial load if old value requires custom input
        if (agencySelect.value === "Other Government Agency" || (customAgencyInput.value && !agencySelect.value)) {
            agencySelect.value = "Other Government Agency";
            handleAgencyChange();
        }

        // Password Show/Hide Toggle Helper
        function setupPasswordToggle(toggleId, inputId) {
            const toggle = document.getElementById(toggleId);
            const input = document.getElementById(inputId);
            if (!toggle || !input) return;

            const eyeShow = toggle.querySelector(".eye-show");
            const eyeHide = toggle.querySelector(".eye-hide");

            toggle.addEventListener("click", () => {
                const isPassword = input.type === "password";
                input.type = isPassword ? "text" : "password";
                eyeShow.classList.toggle("hidden", isPassword);
                eyeHide.classList.toggle("hidden", !isPassword);
            });
        }

        setupPasswordToggle("toggleRegPassword", "regPassword");
        setupPasswordToggle("toggleRegConfirm", "regPasswordConfirm");

        // Live Password Match Check
        const pwdInput = document.getElementById("regPassword");
        const confirmInput = document.getElementById("regPasswordConfirm");
        const matchStatus = document.getElementById("passwordMatchStatus");

        function checkPasswordMatch() {
            const val1 = pwdInput.value;
            const val2 = confirmInput.value;

            if (!val2) {
                matchStatus.classList.add("hidden");
                return;
            }

            matchStatus.classList.remove("hidden");
            if (val1 === val2) {
                matchStatus.className = "text-xs flex items-center space-x-1.5 text-emerald-600 mt-1 font-medium";
                matchStatus.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Passwords match perfectly</span>
                `;
            } else {
                matchStatus.className = "text-xs flex items-center space-x-1.5 text-rose-600 mt-1 font-medium";
                matchStatus.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span>Passwords do not match</span>
                `;
            }
        }

        pwdInput.addEventListener("input", checkPasswordMatch);
        confirmInput.addEventListener("input", checkPasswordMatch);

        // Submit Button Loading State
        const regForm = document.getElementById("registerForm");
        const regBtn = document.getElementById("registerBtn");
        const regBtnText = document.getElementById("regBtnText");
        const regBtnSpinner = document.getElementById("regBtnSpinner");
        const regBox = document.getElementById("registerBox");

        regForm.addEventListener("submit", () => {
            regBtn.disabled = true;
            regBtn.classList.add("submitting-state");
            regBtnText.textContent = "Submitting Registration...";
            regBtnSpinner.classList.remove("hidden");
        });
    </script>
</body>

</html>
