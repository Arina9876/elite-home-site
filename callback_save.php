<?php
require 'config.php';          // getPDO() уже есть
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);         // method not allowed
    echo json_encode(['error'=>'POST only']);  exit;
}

$name  = trim($_POST['name']  ?? '');
$phone = trim($_POST['phone'] ?? '');

if ($name === '' || $phone === '') {
    http_response_code(400);
    echo json_encode(['error'=>'Заполните все поля']);  exit;
}

$pdo = getPDO();
$stmt = $pdo->prepare(
  'INSERT INTO callbacks (name, phone) VALUES (?, ?)'
);
$stmt->execute([$name, $phone]);

echo json_encode(['ok'=>true]);