<?php
// messages_list.php
require 'config.php';
include 'header.php';    // подключаем шапку с навигацией и открывающие теги

// только админ
if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$pdo  = getPDO();
$stmt = $pdo->query(
  'SELECT m.id,
          COALESCE(u.name, \'гость\') AS user_name,
          m.name,
          m.email,
          m.subject,
          m.body,
          m.created_at
     FROM messages m
LEFT JOIN users u ON m.user_id = u.id
 ORDER BY m.created_at DESC'
);
$msgs = $stmt->fetchAll();
?>

  <h1>Сообщения посетителей</h1>

  <table border="1" cellpadding="5">
    <tr>
      <th>ID</th>
      <th>Пользователь</th>
      <th>Имя</th>
      <th>Email</th>
      <th>Тема</th>
      <th>Сообщение</th>
      <th>Дата</th>
      <th>Действия</th>
    </tr>
    <?php foreach ($msgs as $m): ?>
      <tr>
        <td><?= $m['id'] ?></td>
        <td><?= htmlspecialchars($m['user_name']) ?></td>
        <td><?= htmlspecialchars($m['name']) ?></td>
        <td><?= htmlspecialchars($m['email']) ?></td>
        <td><?= htmlspecialchars($m['subject']) ?></td>
        <td><?= nl2br(htmlspecialchars($m['body'])) ?></td>
        <td><?= $m['created_at'] ?></td>
        <td>
          <a href="edit_message.php?id=<?= $m['id'] ?>">Изм.</a> |
          <a href="delete_message.php?id=<?= $m['id'] ?>"
             onclick="return confirm('Удалить сообщение?');">Уд.</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>

  <p><a href="index.php">← На главную</a></p>

<?php
include 'footer.php';   // подключаем футер с закрывающими тегами