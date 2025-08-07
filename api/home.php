<?php
http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => 'Selamat Datang di API Aplikasi Keuangan Ku (Simple Version)',
    'author' => 'Gemini',
    'version' => '3.0.0'
]);