<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/jwt_helper.php';
require_once __DIR__ . '/../models/Transaction_model.php';

// Validasi token
$user_data_from_token = Jwt_helper::validateToken();

$db = getDbConnection();
$transaction_model = new Transaction_model($db);

$method = $_SERVER['REQUEST_METHOD'];
// $params sudah tersedia dari index.php

if ($method == 'GET') {
    // GET /transactions
    $transactions = $transaction_model->getAllTransactions();
    echo json_encode(['status' => 'success', 'data' => $transactions]);

} else if ($method == 'POST') {
    // POST /transactions
    $data = json_decode(file_get_contents("php://input"), true);
    $data['id_user'] = $user_data_from_token->id_user; // Ambil id_user dari token
    
    if ($transaction_model->createTransaction($data) > 0) {
        http_response_code(201);
        echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil dibuat.']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal membuat transaksi.']);
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