<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/jwt_helper.php';
require_once __DIR__ . '/../models/User_model.php';

$db = getDbConnection();
$user_model = new User_model($db);

// Mendapatkan data dari body request
$data = json_decode(file_get_contents("php://input"));

if (empty($data->username) || empty($data->password)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Username dan password tidak boleh kosong.']);
    exit;
}

$user = $user_model->getUserByUsername($data->username);

if ($user && password_verify($data->password, $user['password'])) {
    $tokenData = [
        'id_user' => $user['id_user'],
        'username' => $user['username'],
        'level' => $user['level']
    ];
    $token = Jwt_helper::createToken($tokenData);

    unset($user['password']);
    http_response_code(200);
    echo json_encode([
        'status' => 'success', 'message' => 'Login berhasil.',
        'token' => $token, 'user' => $user
    ]);
} else {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Username atau password salah.']);
}