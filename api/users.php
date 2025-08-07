<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/jwt_helper.php';
require_once __DIR__ . '/../models/User_model.php';

// Validasi token
$user_data_from_token = Jwt_helper::validateToken();

$db = getDbConnection();
$user_model = new User_model($db);

$method = $_SERVER['REQUEST_METHOD'];
// $params sudah tersedia dari index.php

// Logika routing sederhana berdasarkan method dan parameter
if ($method == 'GET' && empty($params)) {
    // GET /users -> Melihat semua user (Hanya Admin)
    if ($user_data_from_token->level !== 'Admin') {
        http_response_code(403);
        die(json_encode(['status' => 'error', 'message' => 'Akses ditolak.']));
    }
    $users = $user_model->getAllUsers();
    echo json_encode(['status' => 'success', 'data' => $users]);

} else if ($method == 'POST' && isset($params[0]) && $params[0] == 'update_password') {
    // POST /users/update_password/{id}
    $id_to_update = $params[1] ?? 0;
    if ($user_data_from_token->level !== 'Admin' && $user_data_from_token->id_user != $id_to_update) {
        http_response_code(403);
        die(json_encode(['status' => 'error', 'message' => 'Akses ditolak.']));
    }
    $data = json_decode(file_get_contents("php://input"));
    if ($user_model->updatePassword($id_to_update, $data->new_password) > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Password berhasil diupdate.']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate password.']);
    }
}