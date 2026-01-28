<?php
/**
 * Front Controller
 * Entry Point ของระบบ CAMELS Analysis
 * 
 * จัดการ Routing และ Request ทั้งหมด
 * PHP 8.0.30
 */

// โหลดไฟล์ Configuration
require_once __DIR__ . '/../config/config.php';

// โหลด Base Database Model
require_once APP_PATH . '/Models/Database.php';

// ========================================
// Basic Routing System
// ========================================

/**
 * ดึง URL Path ปัจจุบัน
 * 
 * @return string
 */
function getCurrentPath(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';

    // ลบ BASE_URL ออกจาก URI
    $basePath = parse_url(BASE_URL, PHP_URL_PATH) ?? '/';
    $path = str_replace($basePath, '', $uri);

    // ลบ query string ออก
    $path = strtok($path, '?');

    // เพิ่ม / ที่ท้ายถ้าไม่มี
    $path = '/' . trim($path, '/');

    return $path;
}

/**
 * ดึง HTTP Method
 * 
 * @return string
 */
function getRequestMethod(): string
{
    return $_SERVER['REQUEST_METHOD'] ?? 'GET';
}

/**
 * Simple Router
 * 
 * @param string $path
 * @param string $method
 * @return array
 */
function route(string $path, string $method = 'GET'): array
{
    $routes = [
        'GET' => [
            '/' => ['controller' => 'DashboardController', 'action' => 'index'],
            '/dashboard' => ['controller' => 'DashboardController', 'action' => 'index'],
            '/auth/login' => ['controller' => 'AuthController', 'action' => 'showLoginForm'],
            '/auth/logout' => ['controller' => 'AuthController', 'action' => 'logout'],
            '/auth/register' => ['controller' => 'AuthController', 'action' => 'showRegisterForm'],
            '/financial/input' => ['controller' => 'FinancialController', 'action' => 'showForm'],
            '/ratio/view' => ['controller' => 'RatioController', 'action' => 'index'],
            '/camels/result' => ['controller' => 'CamelsController', 'action' => 'showResult'],
            '/camels/trend' => ['controller' => 'CamelsController', 'action' => 'showTrend'],
            '/report/pdf' => ['controller' => 'ReportController', 'action' => 'generatePdf'],
            '/report/preview' => ['controller' => 'ReportController', 'action' => 'preview'],
        ],
        'POST' => [
            '/auth/login' => ['controller' => 'AuthController', 'action' => 'login'],
            '/auth/register' => ['controller' => 'AuthController', 'action' => 'register'],
            '/financial/save' => ['controller' => 'FinancialController', 'action' => 'saveData'],
        ]
    ];

    // ตรวจสอบ route ที่ตรงกัน
    if (isset($routes[$method][$path])) {
        return $routes[$method][$path];
    }

    // ตรวจสอบ dynamic routes (เช่น /ratio/view/1/2025)
    foreach ($routes[$method] as $pattern => $route) {
        $pattern = preg_replace('/\{[a-zA-Z_]+\}/', '([a-zA-Z0-9_-]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $path, $matches)) {
            array_shift($matches); // ลบ full match ออก
            $route['params'] = $matches;
            return $route;
        }
    }

    // ถ้าไม่เจอ route ให้ return 404
    return ['controller' => 'ErrorController', 'action' => 'notFound'];
}

/**
 * โหลด Controller และเรียก Action
 * 
 * @param array $route
 * @return void
 */
function loadController(array $route): void
{
    $controllerName = $route['controller'];
    $action = $route['action'];
    $params = $route['params'] ?? [];

    $controllerFile = APP_PATH . '/Controllers/' . $controllerName . '.php';

    // ตรวจสอบว่าไฟล์ Controller มีอยู่หรือไม่
    if (!file_exists($controllerFile)) {
        // แสดงหน้า 404
        show404();
        return;
    }

    require_once $controllerFile;

    // ตรวจสอบว่า Class มีอยู่หรือไม่
    if (!class_exists($controllerName)) {
        show404();
        return;
    }

    // สร้าง instance ของ Controller
    $controller = new $controllerName();

    // ตรวจสอบว่า Method มีอยู่หรือไม่
    if (!method_exists($controller, $action)) {
        show404();
        return;
    }

    // เรียก Method พร้อม Parameters
    call_user_func_array([$controller, $action], $params);
}

/**
 * แสดงหน้า 404 Not Found
 * 
 * @return void
 */
function show404(): void
{
    http_response_code(404);

    if (file_exists(APP_PATH . '/Views/errors/404.php')) {
        require_once APP_PATH . '/Views/errors/404.php';
    } else {
        echo '<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - ไม่พบหน้าที่ค้นหา</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-9xl font-bold text-gray-300">404</h1>
            <h2 class="text-3xl font-semibold text-gray-700 mt-4">ไม่พบหน้าที่ค้นหา</h2>
            <p class="text-gray-500 mt-2">ขอโทษค่ะ ไม่พบหน้าที่คุณกำลังค้นหา</p>
            <a href="' . BASE_URL . '" class="mt-6 inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                กลับหน้าแรก
            </a>
        </div>
    </div>
</body>
</html>';
    }
    exit();
}

/**
 * แสดงหน้า 500 Internal Server Error
 * 
 * @param string $message
 * @return void
 */
function show500(string $message = ''): void
{
    http_response_code(500);

    if (file_exists(APP_PATH . '/Views/errors/500.php')) {
        require_once APP_PATH . '/Views/errors/500.php';
    } else {
        echo '<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - เกิดข้อผิดพลาด</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-9xl font-bold text-red-300">500</h1>
            <h2 class="text-3xl font-semibold text-gray-700 mt-4">เกิดข้อผิดพลาดในระบบ</h2>
            <p class="text-gray-500 mt-2">ขอโทษค่ะ เกิดข้อผิดพลาดในการประมวลผล</p>';

        if (DEBUG_MODE && !empty($message)) {
            echo '<div class="mt-4 p-4 bg-red-50 text-red-800 rounded-lg max-w-2xl mx-auto text-left">
                <strong>Error:</strong><br>
                <pre class="text-sm mt-2">' . htmlspecialchars($message) . '</pre>
              </div>';
        }

        echo '    <a href="' . BASE_URL . '" class="mt-6 inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                กลับหน้าแรก
            </a>
        </div>
    </div>
</body>
</html>';
    }
    exit();
}

// ========================================
// จัดการ Request
// ========================================

try {
    // ดึง current path และ method
    $path = getCurrentPath();
    $method = getRequestMethod();

    // หา route ที่ตรงกัน
    $route = route($path, $method);

    // โหลดและเรียก Controller
    loadController($route);

} catch (Exception $e) {
    // Log error
    error_log('Application Error: ' . $e->getMessage());

    // แสดงหน้า error
    show500($e->getMessage());
}