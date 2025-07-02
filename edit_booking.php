<?php
session_start();
require 'config.php';
include 'header.php';
if (empty($_SESSION['user_id']) || $_SESSION['role'] === 'agent') {
    // только клиент может редактировать свои заявки
    header('Location: login.php');
    exit;
}

$pdo = getPDO();
$id = intval($_GET['id'] ?? 0);

// проверяем, что заявка принадлежит пользователю
$stmt = $pdo->prepare(
  'SELECT * FROM bookings WHERE id = ? AND user_id = ?'
);
$stmt->execute([$id, $_SESSION['user_id']]);
$book = $stmt->fetch();
if (!$book) {
    die('Заявка не найдена.');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date   = trim($_POST['end_date']   ?? '');

    if (!$start_date)         $errors[] = 'Укажите дату начала.';
    if (!$end_date)           $errors[] = 'Укажите дату окончания.';
    if ($start_date > $end_date) $errors[] = 'Дата начала позже даты окончания.';

    if (!$errors) {
        $up = $pdo->prepare(
          'UPDATE bookings
              SET start_date = ?, end_date = ?
            WHERE id = ? AND user_id = ?'
        );
        $up->execute([$start_date, $end_date, $id, $_SESSION['user_id']]);
        header('Location: bookings_list.php');
        exit;
    }
} else {
    $start_date = $book['start_date'];
    $end_date   = $book['end_date'];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Редактировать заявку</title></head>
<body>
  <h1>Редактировать заявку #<?= $book['id'] ?></h1>
  <?php if ($errors): ?>
    <ul style="color:red;">
      <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form method="post">
    <label>С:<br>
      <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
    </label><br><br>

    <label>По:<br>
      <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
    </label><br><br>

    <button type="submit">Сохранить</button>
  </form>

  <p><a href="bookings_list.php">← Мои заявки</a></p>
  include 'footer.php';
</body>
</html>