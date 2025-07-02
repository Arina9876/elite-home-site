<?php
session_start();
require 'config.php';
include 'header.php';
// Только авторизованные клиенты и агенты могут создавать заявки
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = getPDO();

// Для выпадающего списка — берём все объекты
$stmt = $pdo->query('SELECT id, title FROM properties ORDER BY title');
$allProps = $stmt->fetchAll();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id = intval($_POST['property_id'] ?? 0);
    $start_date  = trim($_POST['start_date']  ?? '');
    $end_date    = trim($_POST['end_date']    ?? '');

    if (!$property_id)      $errors[] = 'Выберите объект.';
    if (!$start_date)       $errors[] = 'Укажите дату начала.';
    if (!$end_date)         $errors[] = 'Укажите дату окончания.';
    if ($start_date > $end_date) $errors[] = 'Дата начала позже даты окончания.';

    if (!$errors) {
        $stmt = $pdo->prepare(
          'INSERT INTO bookings 
             (user_id, property_id, start_date, end_date)
           VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
          $_SESSION['user_id'],
          $property_id,
          $start_date,
          $end_date
        ]);
        header('Location: bookings_list.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Новая заявка</title></head>
<body>
  <h1>Новая заявка</h1>
  <?php if ($errors): ?>
    <ul style="color:red;">
      <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form method="post">
    <label>Объект:<br>
      <select name="property_id">
        <option value="">– выберите –</option>
        <?php foreach ($allProps as $p): ?>
          <option value="<?= $p['id'] ?>"
            <?= (isset($property_id) && $property_id == $p['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($p['title']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label><br><br>

    <label>С:<br>
      <input type="date" name="start_date" value="<?= htmlspecialchars($start_date ?? '') ?>">
    </label><br><br>

    <label>По:<br>
      <input type="date" name="end_date" value="<?= htmlspecialchars($end_date ?? '') ?>">
    </label><br><br>

    <button type="submit">Отправить заявку</button>
  </form>

  <p><a href="bookings_list.php">← Мои заявки</a></p>
  include 'footer.php';
</body>
</html>