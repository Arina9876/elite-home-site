<?php
session_start();
require 'config.php';
include 'header.php';
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = getPDO();

if ($_SESSION['role'] === 'agent') {
  // агент видит заявки только на свои объекты
  $stmt = $pdo->prepare(
    'SELECT b.id, b.start_date, b.end_date, b.created_at,
            p.title, u.name AS client
       FROM bookings b
  INNER JOIN properties p ON b.property_id = p.id
  INNER JOIN users u      ON b.user_id = u.id
      WHERE p.agent_id = ?
   ORDER BY b.created_at DESC'
  );
  $stmt->execute([$_SESSION['user_id']]);
} else {
  // клиент или админ
  $stmt = $pdo->prepare(
    'SELECT b.id, b.start_date, b.end_date, b.created_at,
            p.title, u.name AS client
       FROM bookings b
  INNER JOIN properties p ON b.property_id = p.id
  INNER JOIN users u      ON b.user_id = u.id
      WHERE b.user_id = ?
   ORDER BY b.created_at DESC'
  );
  $stmt->execute([$_SESSION['user_id']]);
}

$rows = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Мои заявки</title></head>
<body>
  <h1>Мои заявки</h1>
  <p><a href="add_booking.php">+ Новая заявка</a></p>

  <table border="1" cellpadding="5">
    <tr>
      <th>ID</th><th>Объект</th><th>Клиент</th>
      <th>С</th><th>По</th><th>Дата создания</th>
      <?php if ($_SESSION['role'] !== 'agent'): ?>
        <th>Действия</th>
      <?php endif; ?>
    </tr>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= $r['id'] ?></td>
        <td><?= htmlspecialchars($r['title']) ?></td>
        <td><?= htmlspecialchars($r['client']) ?></td>
        <td><?= $r['start_date'] ?></td>
        <td><?= $r['end_date'] ?></td>
        <td><?= $r['created_at'] ?></td>
        <?php if ($_SESSION['role'] !== 'agent'): ?>
          <td>
            <a href="edit_booking.php?id=<?= $r['id'] ?>">Изм.</a> |
            <a href="delete_booking.php?id=<?= $r['id'] ?>"
               onclick="return confirm('Удалить заявку?');">Уд.</a>
          </td>
        <?php endif; ?>
      </tr>
    <?php endforeach; ?>
  </table>

  <p><a href="index.php">← На главную</a></p>
  include 'footer.php';
</body>
</html>