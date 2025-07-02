<?php
session_start();
require 'config.php';
if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$pdo = getPDO();
$id  = intval($_GET['id'] ?? 0);
$pdo->prepare('DELETE FROM messages WHERE id = ?')->execute([$id]);
header('Location: messages_list.php');
exit;