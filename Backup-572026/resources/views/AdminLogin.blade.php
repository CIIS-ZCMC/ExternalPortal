<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ZCMC | Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-xl rounded-2xl p-10 w-full max-w-md">
        <h1 class="text-3xl font-bold text-center text-blue-700 mb-6">
            ZCMC <br> External Employee Lists
        </h1>

        <form action="/admin/signin" method="POST" class="space-y-5">
            <!-- Employee ID -->
            @csrf
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Employee ID</label>
                <input type="number" name="employeeId" required
                    class="w-full px-4 py-3 rounded-lg @session('error')
border-red-500
@endsession border focus:ring-2 focus:ring-blue-600 outline-none"
                    placeholder="Enter your Employee ID" />

                @session('error')
                    <p class="text-red-500 text-sm mt-1">{{ session('error') }}</p>
                @endsession
            </div>

            <!-- Submit -->
            <button type="submit"
                class="w-full bg-blue-700 text-white py-3 rounded-lg text-lg font-semibold hover:bg-blue-800 transition">
                Login
            </button>
        </form>

        <p class="text-center text-gray-500 text-sm mt-6">
            ZCMC External Employee DTR Portal
        </p>
    </div>
</body>

</html>
