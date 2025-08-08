<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/jwt_helper.php';
require_once __DIR__ . '/../models/User_model.php';

$db = getDbConnection();
$user_model = new User_model($db);

$method = $_SERVER['REQUEST_METHOD'];
$params = $params ?? []; // Pastikan $params ada

// Logika routing
if ($method == 'POST' && empty($params)) {
    // === REGISTRASI USER BARU (ENDPOINT PUBLIK) ===
    $data = json_decode(file_get_contents("php://input"), true);

    // Validasi input
    if (empty($data['nama_user']) || empty($data['level']) || empty($data['username']) || empty($data['password'])) {
        http_response_code(400);
        die(json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi.']));
    }
    if ($user_model->getUserByUsername($data['username'])) {
        http_response_code(409); // Conflict
        die(json_encode(['status' => 'error', 'message' => 'Username sudah digunakan.']));
    }

    if ($user_model->createUser($data) > 0) {
        http_response_code(201);
        echo json_encode(['status' => 'success', 'message' => 'Registrasi berhasil. Silakan login.']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal melakukan registrasi.']);
    }
} else {
    // === SEMUA ENDPOINT LAIN BUTUH TOKEN ===
    $user_data_from_token = Jwt_helper::validateToken();

    // Logika untuk endpoint yang butuh otorisasi
    if ($method == 'GET' && empty($params)) {
        // GET /users -> Melihat semua user (Hanya Admin)
        if ($user_data_from_token->level !== 'Admin') {
            http_response_code(403);
            die(json_encode(['status' => 'error', 'message' => 'Akses ditolak. Fitur ini hanya untuk Admin.']));
        }
        $users = $user_model->getAllUsers();
        echo json_encode(['status' => 'success', 'data' => $users]);
    } else if ($method == 'POST' && isset($params[0]) && $params[0] == 'update') {
        // POST /users/update/{id} -> Mengupdate info user (Hanya Admin)
        if ($user_data_from_token->level !== 'Admin') {
            http_response_code(403);
            die(json_encode(['status' => 'error', 'message' => 'Akses ditolak. Fitur ini hanya untuk Admin.']));
        }
        $id_to_update = $params[1] ?? 0;
        $data = json_decode(file_get_contents("php://input"), true);
        if ($user_model->updateUser($id_to_update, $data) > 0) {
            echo json_encode(['status' => 'success', 'message' => 'User berhasil diupdate.']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate user atau tidak ada data yang berubah.']);
        }
    } else if ($method == 'POST' && isset($params[0]) && $params[0] == 'delete') {
        // POST /users/delete/{id} -> Menghapus user (Hanya Admin)
        if ($user_data_from_token->level !== 'Admin') {
            http_response_code(403);
            die(json_encode(['status' => 'error', 'message' => 'Akses ditolak. Fitur ini hanya untuk Admin.']));
        }
        $id_to_delete = $params[1] ?? 0;
        if ($user_model->deleteUser($id_to_delete) > 0) {
            echo json_encode(['status' => 'success', 'message' => 'User berhasil dihapus.']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus user.']);
        }
    } else if ($method == 'POST' && isset($params[0]) && $params[0] == 'update_password') {
        // POST /users/update_password/{id} -> Mengupdate password
        $id_to_update = $params[1] ?? 0;
        if ($user_data_from_token->level !== 'Admin' && $user_data_from_token->id_user != $id_to_update) {
            http_response_code(403);
            die(json_encode(['status' => 'error', 'message' => 'Akses ditolak. Anda hanya bisa mengubah password sendiri.']));
        }
        $data = json_decode(file_get_contents("php://input"));
        if ($user_model->updatePassword($id_to_update, $data->new_password) > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Password berhasil diupdate.']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate password.']);
        }
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Endpoint di /users ini tidak ditemukan atau butuh otorisasi.']);
    }
}
