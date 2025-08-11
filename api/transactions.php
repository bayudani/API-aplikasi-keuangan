<?php
// transactions.php - VERSI LENGKAP DENGAN CRUD & HAK AKSES

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/jwt_helper.php';
require_once __DIR__ . '/../models/Transactions_models.php';

// Atur header
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Tambahkan PUT & DELETE
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Validasi token untuk semua endpoint di file ini
$user_data_from_token = Jwt_helper::validateToken();

$db = getDbConnection();
$transaction_model = new Transactions_models($db);
$method = $_SERVER['REQUEST_METHOD'];

// Ambil parameter dari path URL (cth: /123/update)
$pathInfo = $_SERVER['PATH_INFO'] ?? '';
$params = $pathInfo ? explode('/', trim($pathInfo, '/')) : [];

// --- LOGIKA UTAMA ---

if ($method == 'POST' && empty($params)) {
    // POST /transactions.php -> Input data transaksi baru (Bisa semua role)
    $data = json_decode(file_get_contents("php://input"), true);
    $data['id_user'] = $user_data_from_token->id_user; 
    
    if ($transaction_model->createTransaction($data) > 0) {
        http_response_code(201);
        echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil dibuat.']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal membuat transaksi.']);
    }

} else if ($method == 'GET' && empty($params)) {
    // GET /transactions.php -> Lihat transaksi (Admin lihat semua, Karyawan lihat punya sendiri)
    if ($user_data_from_token->level === 'Admin') {
        $transactions = $transaction_model->getAllTransactions();
    } else {
        $transactions = $transaction_model->getTransactionsByUserId($user_data_from_token->id_user);
    }
    echo json_encode(['status' => 'success', 'data' => $transactions]);

} else if ($method == 'PUT' && !empty($params[0])) {
    // PUT /transactions.php/{id} -> Update transaksi (Hanya Admin)
    if ($user_data_from_token->level !== 'Admin') {
        http_response_code(403);
        die(json_encode(['status' => 'error', 'message' => 'Akses ditolak. Fitur ini hanya untuk Admin.']));
    }
    $id_to_update = $params[0];
    $data = json_decode(file_get_contents("php://input"), true);
    
    if ($transaction_model->updateTransaction($id_to_update, $data) > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil diupdate.']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate transaksi atau tidak ada data yang berubah.']);
    }

} else if ($method == 'DELETE' && !empty($params[0])) {
    // DELETE /transactions.php/{id} -> Hapus transaksi (Hanya Admin)
    if ($user_data_from_token->level !== 'Admin') {
        http_response_code(403);
        die(json_encode(['status' => 'error', 'message' => 'Akses ditolak. Fitur ini hanya untuk Admin.']));
    }
    $id_to_delete = $params[0];
    
    if ($transaction_model->deleteTransaction($id_to_delete) > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil dihapus.']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus transaksi.']);
    }

} else if ($method == 'GET' && isset($params[0])) {
    // Routing untuk endpoint GET lain seperti dashboard & report
    $action = $params[0];
    if ($user_data_from_token->level !== 'Admin') {
        http_response_code(403);
        die(json_encode(['status' => 'error', 'message' => 'Akses ditolak. Fitur ini hanya untuk Admin.']));
    }
    
    if ($action == 'dashboard') {
        $data = $transaction_model->getDashboardData();
        echo json_encode(['status' => 'success', 'data' => $data]);
    } else if ($action == 'report') {
        $startDate = $_GET['start'] ?? date('Y-m-01');
        $endDate = $_GET['end'] ?? date('Y-m-t');
        $data = $transaction_model->getReportData($startDate, $endDate);
        echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Endpoint GET di /transactions ini tidak ditemukan.']);
    }

} else {
    http_response_code(404);
    echo json_encode(['message' => 'Endpoint tidak ditemukan atau metode tidak sesuai.']);
}