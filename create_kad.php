<?php
header('Content-Type: application/json');
$uploadDir = __DIR__ . '/kad/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
function random_id($length = 8) {
    return substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, $length);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $theme = $_POST['theme'] ?? 'theme1';
    $wish = trim($_POST['wish'] ?? '');
    $signature = trim($_POST['signature'] ?? '');
    if ($wish === '' || $signature === '' || !isset($_FILES['photo'])) {
        echo json_encode(['success'=>false,'error'=>'Semua medan diperlukan.']);
        exit;
    }
    $id = random_id();
    $photo = $_FILES['photo'];
    $ext = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
        echo json_encode(['success'=>false,'error'=>'Format gambar tidak disokong.']);
        exit;
    }
    $photoName = $id . '.' . $ext;
    $photoPath = $uploadDir . $photoName;
    if (!move_uploaded_file($photo['tmp_name'], $photoPath)) {
        echo json_encode(['success'=>false,'error'=>'Gagal muat naik gambar.']);
        exit;
    }
    $data = [
        'id' => $id,
        'theme' => $theme,
        'wish' => htmlspecialchars($wish, ENT_QUOTES, 'UTF-8'),
        'signature' => htmlspecialchars($signature, ENT_QUOTES, 'UTF-8'),
        'photo' => $photoName,
        'created' => date('Y-m-d H:i:s')
    ];
    file_put_contents($uploadDir . $id . '.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo json_encode(['success'=>true,'url'=>'kad/' . $id]);
    exit;
}
echo json_encode(['success'=>false,'error'=>'Invalid request.']);
