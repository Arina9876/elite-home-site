<?php
// properties_list.php
require 'config.php';
include 'header.php';    // ← шапка с меню и открывающие теги

// Только авторизованные
if (empty($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$pdo = getPDO();
if ($_SESSION['role'] === 'agent') {
  // агенты видят только свои объекты
  $stmt = $pdo->prepare(
    'SELECT * FROM properties WHERE agent_id = ? ORDER BY created_at DESC'
  );
  $stmt->execute([$_SESSION['user_id']]);
} else {
  // админы и клиенты видят все
  $stmt = $pdo->query('SELECT * FROM properties ORDER BY created_at DESC');
}
$properties = $stmt->fetchAll();
?>

  <h1>Список объектов</h1>

  <?php if ($_SESSION['role'] === 'agent'): ?>
    <p><a href="add_property.php">+ Добавить новый объект</a></p>
  <?php endif; ?>

  <table border="1" cellpadding="5">
    <tr>
      <th>ID</th><th>Заголовок</th><th>Адрес</th><th>Цена</th><th>Действия</th>
    </tr>
    <?php foreach ($properties as $p): ?>
      <tr>
        <td><?= $p['id'] ?></td>
        <td><?= htmlspecialchars($p['title']) ?></td>
        <td><?= htmlspecialchars($p['address']) ?></td>
        <td><?= $p['price'] ?></td>
        <td>
          <?php if ($_SESSION['role'] === 'agent'): ?>
            <a href="edit_property.php?id=<?= $p['id'] ?>">Редактировать</a> |
            <a href="delete_property.php?id=<?= $p['id'] ?>"
               onclick="return confirm('Удалить этот объект?');">Удалить</a>
          <?php else: ?>
            —
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>

  <p><a href="index.php">← На главную</a></p>

<?php
include 'footer.php';   // ← футер с закрывающими тегами