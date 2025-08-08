<?php
require_once __DIR__ . '/../vendor/autoload.php'; // path disesuaikan

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Jwt_helper {
    private static $key = 'kunci_rahasia_super_aman_12345';
    private static $iss = 'http://backendapliksikeuangan.test';
    private static $aud = 'http://backendapliksikeuangan.test';

    public static function createToken($data) {
        $iat = time();
        $exp = $iat + (60 * 60 * 24);

        $payload = [
            'iss' => self::$iss, 'aud' => self::$aud, 'iat' => $iat, 'exp' => $exp,
            'data' => $data
        ];

        return JWT::encode($payload, self::$key, 'HS256');
    }

    public static function validateToken() {
        try {
            $headers = getallheaders();
            if (!isset($headers['Authorization'])) {
                http_response_code(401);
                echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Token tidak ditemukan.']);
                exit();
            }

            list($jwt) = sscanf($headers['Authorization'], 'Bearer %s');
            if (!$jwt) {
                http_response_code(401);
                echo json_encode(['status' => 'error', 'message' => 'Format token tidak valid.']);
                exit();
            }

            $decoded = JWT::decode($jwt, new Key(self::$key, 'HS256'));
            return $decoded->data;

        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. ' . $e->getMessage()]);
            exit();
        }
    }
}