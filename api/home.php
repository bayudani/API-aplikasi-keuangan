<?php
http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => 'Selamat Datang di API Aplikasi Keuangan ',
    // 'author' => '',
    'version' => '1.0.0'
]);