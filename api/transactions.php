<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/jwt_helper.php';
require_once __DIR__ . '/../models/Transactions_models.php';
// / Validasi token
$user_data_from_token = Jwt_helper::validateToken();

$db = getDbConnection();
$transaction_model = new Transactions_models($db);

$method = $_SERVER['REQUEST_METHOD'];
$params = $params ?? [];

if ($method == 'POST' && empty($params)) {
    // POST /transactions -> Input data transaksi (Bisa oleh Admin & Karyawan)
    $data = json_decode(file_get_contents("php://input"), true);
    $data['id_user'] = $user_data_from_token->id_user; 
    
    if ($transaction_model->createTransaction($data) > 0) {
        http_response_code(201);
        echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil dibuat.']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal membuat transaksi.']);
    }
} else {
    // Semua method di bawah ini HANYA UNTUK ADMIN
    if ($user_data_from_token->level !== 'Admin') {
        http_response_code(403);
        die(json_encode(['status' => 'error', 'message' => 'Akses ditolak. Fitur ini hanya untuk Admin.']));
    }

    if ($method == 'GET' && empty($params)) {
        // GET /transactions -> Lihat semua transaksi
        $transactions = $transaction_model->getAllTransactions();
        echo json_encode(['status' => 'success', 'data' => $transactions]);

    } else if ($method == 'GET' && isset($params[0]) && $params[0] == 'dashboard') {
        // GET /transactions/dashboard -> Data untuk monitoring
        $data = $transaction_model->getDashboardData();
        echo json_encode(['status' => 'success', 'data' => $data]);

    } else if ($method == 'GET' && isset($params[0]) && $params[0] == 'report') {
        // GET /transactions/report -> Data untuk laporan
        $startDate = $_GET['start'] ?? date('Y-m-01');
        $endDate = $_GET['end'] ?? date('Y-m-t');
        $data = $transaction_model->getReportData($startDate, $endDate);
        echo json_encode(['status' => 'success', 'data' => $data]);

    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Endpoint di /transactions ini tidak ditemukan.']);
    }
}
// else if ($method == 'PUT') {
//     // PUT /transactions/{id}
//     $id_to_update = $params[0] ?? 0;
//     $data = json_decode(file_get_contents("php://input"), true);
    
//     if ($transaction_model->updateTransaction($id_to_update, $data) > 0) {
//         echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil diupdate.']);
//     } else {
//         http_response_code(500);
//         echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate transaksi.']);
//     }
// } else if ($method == 'DELETE') {
//     // DELETE /transactions/{id}
//     $id_to_delete = $params[0] ?? 0;
    
//     if ($transaction_model->deleteTransaction($id_to_delete) > 0) {
//         echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil dihapus.']);
//     } else {
//         http_response_code(500);
//         echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus transaksi.']);
//     }
// }
// else {
//     http_response_code(405);
//     echo json_encode(['status' => 'error', 'message' => 'Metode tidak diizinkan.']);
// }