<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>เข้าสู่ระบบ -
        <?= SITE_NAME ?>
    </title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts - Thai Support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Sarabun', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .login-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .input-focus:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md fade-in">
        <!-- Login Card -->
        <div class="login-card rounded-2xl shadow-2xl p-8">

            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <div
                    class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 text-white w-16 h-16 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">ระบบวิเคราะห์ CAMELS</h1>
                <p class="text-gray-600">สำหรับสหกรณ์และกลุ่มเกษตรกร</p>
            </div>

            <?php
            // แสดง Flash Message
            $flashMessage = getFlashMessage();
            if ($flashMessage):
                $bgColor = match ($flashMessage['type']) {
                    'success' => 'bg-green-50 border-green-200 text-green-800',
                    'error' => 'bg-red-50 border-red-200 text-red-800',
                    'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
                    default => 'bg-blue-50 border-blue-200 text-blue-800'
                };
                ?>
                <div class="<?= $bgColor ?> border rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span>
                            <?= sanitize($flashMessage['message']) ?>
                        </span>
                    </div>
                </div>
            <?php endif; ?>

            <?php
            // แสดง Validation Errors
            if (isset($_SESSION['login_errors'])):
                $errors = $_SESSION['login_errors'];
                unset($_SESSION['login_errors']);
                ?>
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 mb-6">
                    <div class="font-medium mb-2">พบข้อผิดพลาด:</div>
                    <ul class="list-disc list-inside space-y-1">
                        <?php foreach ($errors as $error): ?>
                            <li>
                                <?= sanitize($error) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="<?= url('auth/login') ?>" class="space-y-6">

                <?= csrfField() ?>

                <!-- Username Field -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        ชื่อผู้ใช้
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input type="text" id="username" name="username"
                            value="<?= sanitize($_SESSION['old_username'] ?? '') ?>"
                            class="input-focus block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none transition duration-150"
                            placeholder="กรอกชื่อผู้ใช้" required autocomplete="username" autofocus>
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        รหัสผ่าน
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                        </div>
                        <input type="password" id="password" name="password"
                            class="input-focus block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none transition duration-150"
                            placeholder="กรอกรหัสผ่าน" required autocomplete="current-password">
                        <button type="button" onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg id="eye-icon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember_me" name="remember_me"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                            จดจำฉันไว้
                        </label>
                    </div>

                    <!-- <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        ลืมรหัสผ่าน?
                    </a> -->
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                        class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                            </path>
                        </svg>
                        เข้าสู่ระบบ
                    </button>
                </div>
            </form>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-xs text-gray-500">
                    ระบบวิเคราะห์ความพร้อมในการให้บริการของสหกรณ์<br>
                    CAMELS Analysis System v
                    <?= VERSION ?>
                </p>
            </div>

        </div>

        <!-- Demo Credentials (แสดงในโหมด Development เท่านั้น) -->
        <?php if (DEBUG_MODE): ?>
            <div class="mt-4 bg-white bg-opacity-90 rounded-lg p-4 text-sm">
                <div class="font-medium text-gray-700 mb-2">🔑 บัญชีทดสอบ (Demo):</div>
                <div class="grid grid-cols-2 gap-2 text-gray-600">
                    <div>
                        <strong>Admin:</strong><br>
                        username: <code class="bg-gray-100 px-2 py-1 rounded">admin</code><br>
                        password: <code class="bg-gray-100 px-2 py-1 rounded">password</code>
                    </div>
                    <div>
                        <strong>User:</strong><br>
                        username: <code class="bg-gray-100 px-2 py-1 rounded">user</code><br>
                        password: <code class="bg-gray-100 px-2 py-1 rounded">password</code>
                    </div>
                    <div>
                        <strong>Viewer:</strong><br>
                        username: <code class="bg-gray-100 px-2 py-1 rounded">viewer</code><br>
                        password: <code class="bg-gray-100 px-2 py-1 rounded">password</code>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <script>
        // Toggle Password Visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }

        // Clear old_username session after page load
        <?php unset($_SESSION['old_username']); ?>

            // Auto-focus on username field
            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('username').focus();
            });
    </script>

</body>

</html>