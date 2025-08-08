<?php
require_once __DIR__ . '/vendor/autoload.php';
// Mengatur header default
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight request (OPTIONS) dari browser
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Memuat autoloader dari Composer

// --- BAGIAN YANG DIPERBAIKI ---
// Parsing URL yang lebih simpel dan andal
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$uri = $path ? explode('/', $path) : [];
// --- AKHIR BAGIAN YANG DIPERBAIKI ---

// Hapus nama folder proyek dari URI jika ada
// Sesuaikan 'projek_api_flutter' dengan nama folder proyek lo
$projectName = 'projek_api_flutter'; 
$projectIndex = array_search($projectName, $uri);
if ($projectIndex !== false) {
    $uri = array_slice($uri, $projectIndex + 1);
}


// Menentukan controller yang akan dipanggil
$controller = $uri[0] ?? 'home';
$controllerFile = __DIR__ . '/api/' . $controller . '.php';

if (file_exists($controllerFile)) {
    // Menyimpan sisa URI sebagai parameter
    $params = array_slice($uri, 1);
    // Memanggil file controller
    require $controllerFile;
} else {
    http_response_code(404);
    echo json_encode(["message" => "Endpoint tidak ditemukan."]);
}
