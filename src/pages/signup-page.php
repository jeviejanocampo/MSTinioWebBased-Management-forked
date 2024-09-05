<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-lg w-full">
        <h2 class="text-2xl font-semibold mb-6 text-center">Sign Up</h2>
        <form action="#" method="POST">
            <!-- Full Name -->
            <div class="mb-4">
                <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" id="full_name" name="full_name" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                       placeholder="John Doe">
            </div>

            <!-- Username -->
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" id="username" name="username" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                       placeholder="johndoe">
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" id="email" name="email" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                       placeholder="you@example.com">
            </div>

            <!-- Birthday -->
            <div class="mb-4">
                <label for="birthday" class="block text-sm font-medium text-gray-700">Birthday</label>
                <input type="date" id="birthday" name="birthday" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                       onchange="calculateAge()">
                <p id="age" class="mt-2 text-sm text-gray-600">Age: <span id="ageValue">N/A</span></p>
            </div>

            <div class="mb-4 relative">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                       placeholder="••••••••">
                <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 px-3 py-2 text-gray-500 hover:text-gray-700 mt-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.706 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.248 7-9.542 7-4.293 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>

            <!-- Confirm Password -->
            <div class="mb-6 relative">
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                       placeholder="••••••••">
                <button type="button" onclick="togglePassword('confirm_password')" class="absolute inset-y-0 right-0 px-3 py-2 text-gray-500 hover:text-gray-700 mt-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.706 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.248 7-9.542 7-4.293 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            <!-- Submit Button -->
            <button type="submit"
                    class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Sign Up
            </button>
        </form>

        <!-- Sign In Link -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">Already have an account? <a href="./login-page.php" class="text-indigo-600 hover:text-indigo-700">Sign in</a></p>
        </div>
    </div>
    <script src="../js/signup-js.js"></script>
</body>
</html>
