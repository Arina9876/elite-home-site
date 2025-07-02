<?php
session_start();
require 'config.php';
if (empty($_SESSION['user_id'] || $_SESSION['role'] !== 'agent')) {
  header('Location: login.php');
  exit;
}

$pdo = getPDO();
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare(
  'DELETE FROM properties WHERE id = ? AND agent_id = ?'
);
$stmt->execute([$id, $_SESSION['user_id']]);
header('Location: properties_list.php');
exit;