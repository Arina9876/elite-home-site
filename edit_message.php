<?php
session_start();
require 'config.php';
include 'header.php';
if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$pdo = getPDO();
$id  = intval($_GET['id'] ?? 0);

// получаем сообщение
$stmt = $pdo->prepare('SELECT * FROM messages WHERE id = ?');
$stmt->execute([$id]);
$msg = $stmt->fetch();
if (!$msg) die('Сообщение не найдено.');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $body    = trim($_POST['body']    ?? '');
    if ($body === '') $errors[] = 'Сообщение не может быть пустым.';

    if (!$errors) {
        $upd = $pdo->prepare(
          'UPDATE messages SET subject = ?, body = ? WHERE id = ?'
        );
        $upd->execute([$subject, $body, $id]);
        header('Location: messages_list.php');
        exit;
    }
} else {
    $subject = $msg['subject'];
    $body    = $msg['body'];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Редактировать сообщение</title></head>
<body>
  <h1>Редактировать сообщение #<?= $msg['id'] ?></h1>
  <?php if ($errors): ?>
    <ul style="color:red;">
      <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form method="post">
    <label>Тема:<br>
      <input type="text" name="subject" value="<?= htmlspecialchars($subject) ?>">
    </label><br><br>

    <label>Сообщение:<br>
      <textarea name="body"><?= htmlspecialchars($body) ?></textarea>
    </label><br><br>

    <button type="submit">Сохранить</button>
  </form>

  <p><a href="messages_list.php">← К списку</a></p>
  include 'footer.php';
</body>
</html>