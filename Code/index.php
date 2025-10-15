<?php
// index.php — API PHP thuần: GET /api/get-data, POST /api/tong

// Bật CORS (không bắt buộc với app native, nhưng để test từ browser cho tiện)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

header('Content-Type: application/json; charset=UTF-8');

// Router siêu đơn giản
$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Helper trả JSON
function json_response($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// “kho” phép tính
$Operation = [
    '+',
    '-',
    '*',
    '/'
];
// Tuyến: GET /phep-tinh
if ($method === 'GET' && $uri === '/phep-tinh') {
    $pick = $Operation[array_rand($Operation)];
    json_response(['success' => true, 'data' => $pick]);
}
if ($method === 'GET' && $uri === '/so') {
    $pick = random_int(1,20);
    json_response(['success' => true, 'data' => $pick]);
}
// Tuyến: POST /ket-qua
if ($method === 'POST' && $uri === '/ket-qua') {
    $raw = file_get_contents('php://input');
    $nameody = json_decode($raw, true);

    if (!is_array($nameody)) {
        json_response(['success' => false, 'message' => 'Body phải là JSON'], 400);
    }

    $a = $nameody['a'] ?? null;
    $b = $nameody['b'] ?? null;
    $phep_tinh = $nameody['phep_tinh'] ?? null;

    if ($a === null || $b === null || $phep_tinh === null ) {
        json_response(['success' => false, 'message' => 'Thiếu tham số a hoặc b hoặc phép tính!'], 400);
    }

    if ( !is_numeric($a)|| !is_numeric($b)) {
        json_response(['success' => false, 'message' => 'a và b phải là số'], 400);
    }
    if ( $phep_tinh === "") {
        json_response(['success' => false, 'message' => 'Phép tính không hợp lệ'], 400);
    }
    $ket_qua;
    switch($phep_tinh){
        case '+': {$ket_qua = $a + $b;break;}
        case '-': {$ket_qua = $a - $b;break;}
        case '/': {$ket_qua = $a / $b;break;}
        case '*': $ket_qua = $a * $b;
    }
    json_response(['success' => true,'data' =>$ket_qua]);
}

// Tuyến: POST /danhg-nhap
if ($method === 'POST' && $uri === '/dang-nhap') {
    $raw = file_get_contents('php://input');
    $nameody = json_decode($raw, true);

    if (!is_array($nameody)) {
        json_response(['success' => false, 'message' => 'Body phải là JSON'], 400);
    }

    $mssv = $nameody['mssv'] ?? null;
    $name = $nameody['ho_ten'] ?? null;

    if ($mssv === null || $name === null) {
        json_response(['success' => false, 'message' => 'Không nhận được mssv hoặc họ tên!'], 400);
    }

    if ( $mssv !="123"|| $name != "thong") {
        json_response(['success' => false, 'message' => 'Sai MSSV hoặc họ tên'], 400);
    }

    json_response(['success' => true,'message' =>'Đăng nhập thành công']);
}

// Health check
if ($method === 'GET' && $uri === '/') {
    echo "API OK";
    exit;
}

// 404
json_response(['success' => false, 'message' => 'Not found'], 404);