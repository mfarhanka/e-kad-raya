<?php
header('Content-Type: application/json');
if (!isset($_GET['id']) || !preg_match('/^[a-z0-9]{8}$/', $_GET['id'])) {
    echo json_encode(['success'=>false,'error'=>'Invalid ID.']);
    exit;
}
$id = $_GET['id'];
$wishFile = __DIR__ . "/$id-wishes.json";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $wish = trim($input['wish'] ?? '');
    if ($wish !== '') {
        $wishes = file_exists($wishFile) ? json_decode(file_get_contents($wishFile), true) : [];
        $wishes[] = [
            'wish' => htmlspecialchars($wish, ENT_QUOTES, 'UTF-8'),
            'time' => date('Y-m-d H:i:s')
        ];
        file_put_contents($wishFile, json_encode($wishes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo json_encode(['success' => true, 'wishes' => $wishes]);
        exit;
    }
    echo json_encode(['success' => false, 'error' => 'Empty wish.']);
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $wishes = file_exists($wishFile) ? json_decode(file_get_contents($wishFile), true) : [];
    echo json_encode(['success' => true, 'wishes' => $wishes]);
    exit;
}
echo json_encode(['success' => false, 'error' => 'Invalid request.']);
