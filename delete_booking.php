<?php
session_start();
require 'config.php';
if (empty($_SESSION['user_id']) || $_SESSION['role'] === 'agent') {
    header('Location: login.php');
    exit;
}

$pdo = getPDO();
$id = intval($_GET['id'] ?? 0);

// удаляем только свою заявку
$stmt = $pdo->prepare(
  'DELETE FROM bookings WHERE id = ? AND user_id = ?'
);
$stmt->execute([$id, $_SESSION['user_id']]);

header('Location: bookings_list.php');
exit;