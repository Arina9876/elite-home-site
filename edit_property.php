<?php
session_start();
require 'config.php';
include 'header.php';
if (empty($_SESSION['user_id'] || $_SESSION['role'] !== 'agent')) {
  header('Location: login.php');
  exit;
}

$pdo = getPDO();
$id = intval($_GET['id'] ?? 0);

// получаем данные
$stmt = $pdo->prepare(
  'SELECT * FROM properties WHERE id = ? AND agent_id = ?'
);
$stmt->execute([$id, $_SESSION['user_id']]);
$prop = $stmt->fetch();
if (!$prop) {
  die('Объект не найден или нет доступа.');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title       = trim($_POST['title'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $address     = trim($_POST['address'] ?? '');
  $price       = trim($_POST['price'] ?? '');

  if ($title === '') $errors[] = 'Введите заголовок.';
  if ($price === '' || !is_numeric($price)) $errors[] = 'Неверная цена.';

  if (!$errors) {
    $stmt = $pdo->prepare(
      'UPDATE properties
         SET title = ?, description = ?, address = ?, price = ?
       WHERE id = ? AND agent_id = ?'
    );
    $stmt->execute([$title, $description, $address, $price, $id, $_SESSION['user_id']]);
    header('Location: properties_list.php');
    exit;
  }
} else {
  // первый открытие формы
  $title       = $prop['title'];
  $description = $prop['description'];
  $address     = $prop['address'];
  $price       = $prop['price'];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Редактировать объект</title></head>
<body>
  <h1>Редактировать объект #<?= $prop['id'] ?></h1>
  <?php if ($errors): ?>
    <ul style="color:red;">
      <?php foreach ($errors as $e): ?><li><?=htmlspecialchars($e)?></li><?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form method="post">
    <label>Заголовок:<br>
      <input type="text" name="title" value="<?= htmlspecialchars($title) ?>">
    </label><br><br>

    <label>Описание:<br>
      <textarea name="description"><?= htmlspecialchars($description) ?></textarea>
    </label><br><br>

    <label>Адрес:<br>
      <input type="text" name="address" value="<?= htmlspecialchars($address) ?>">
    </label><br><br>

    <label>Цена:<br>
      <input type="text" name="price" value="<?= htmlspecialchars($price) ?>">
    </label><br><br>

    <button type="submit">Сохранить</button>
  </form>

  <p><a href="properties_list.php">← Вернуться к списку</a></p>
  include 'footer.php';
</body>
</html>