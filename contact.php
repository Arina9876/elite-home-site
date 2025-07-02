<?php
session_start();
require 'config.php';
include 'header.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // если залогинен — привязываем к user_id, иначе оставляем NULL
    $user_id = $_SESSION['user_id'] ?? null;
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $body    = trim($_POST['body']    ?? '');

    if ($name === '')    $errors[] = 'Введите имя.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Неверный email.';
    if ($body === '')    $errors[] = 'Введите текст сообщения.';

    if (!$errors) {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
          'INSERT INTO messages (user_id, name, email, subject, body)
           VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$user_id, $name, $email, $subject, $body]);
        $success = 'Спасибо! Ваше сообщение отправлено.';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Обратная связь</title>
</head>
<body>
  <h1>Обратная связь</h1>

  <?php if (!empty($success)): ?>
    <p style="color:green;"><?= htmlspecialchars($success) ?></p>
  <?php endif; ?>

  <?php if ($errors): ?>
    <ul style="color:red;">
      <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <!-- Контейнер для клиентских ошибок -->
  <ul id="contactErrors" style="color:red;"></ul>

  <form id="contactForm" method="post" action="">
    <label>Имя:<br>
      <input type="text" name="name" value="<?= htmlspecialchars($name ?? '') ?>">
    </label><br><br>

    <label>Email:<br>
      <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>">
    </label><br><br>

    <label>Тема:<br>
      <input type="text" name="subject" value="<?= htmlspecialchars($subject ?? '') ?>">
    </label><br><br>

    <label>Сообщение:<br>
      <textarea name="body"><?= htmlspecialchars($body ?? '') ?></textarea>
    </label><br><br>

    <button type="submit">Отправить</button>
  </form>

  <p><a href="index.php">← На главную</a></p>

  <script>
    // Клиентская валидация формы обратной связи
    document.getElementById('contactForm').addEventListener('submit', function(e) {
      const errs = [];
      const name    = this.name.value.trim();
      const email   = this.email.value.trim();
      const body    = this.body.value.trim();

      if (!name) errs.push('Введите имя.');
      if (!email || !/^\S+@\S+\.\S+$/.test(email)) errs.push('Неверный email.');
      if (!body) errs.push('Введите текст сообщения.');

      const container = document.getElementById('contactErrors');
      container.innerHTML = errs.map(msg => `<li>${msg}</li>`).join('');
      if (errs.length) e.preventDefault();
    });
    include 'footer.php';
  </script>
</body>
</html>
