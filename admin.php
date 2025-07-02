<?php
require 'config.php';
session_start();

$pdo = getPDO();

// === 1. Обработка входа ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_password'])) {
    $entered = $_POST['admin_password'];
    $correct = '123456'; // ← здесь укажи свой пароль

    if ($entered === $correct) {
        $_SESSION['is_admin'] = true;
        header('Location: admin.php'); // чтобы не пересылать форму повторно
        exit;
    } else {
        $error = 'Неверный пароль';
    }
}

// === Вставляем шапку прямо здесь ===
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Админ-панель</title>
  <style>
    body {
      margin: 0; padding: 0; font-family: Arial, sans-serif;
      background: #f0f2f5;
    }
    header.admin-header {
      background-color: #1f2937;
      color: white;
      padding: 15px 30px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    header.admin-header a {
      color: #a5b4fc;
      text-decoration: none;
      font-weight: bold;
    }
    header.admin-header a:hover {
      text-decoration: underline;
    }
    footer.admin-footer {
      background-color: #1f2937;
      color: white;
      text-align: center;
      padding: 10px 0;
      position: fixed;
      width: 100%;
      bottom: 0;
      left: 0;
      font-size: 14px;
    }
    main {
      padding: 20px 30px 60px; /* снизу отступ чтобы футер не налезал */
      min-height: 90vh;
    }
    table {
      border-collapse: collapse;
      width: 100%;
      margin-bottom: 40px;
      background: white;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 8px;
    }
    th {
      background-color: #1f2937;
      color: white;
    }
    a.edit-link, a.delete-link {
      margin-right: 10px;
      text-decoration: none;
      font-weight: bold;
    }
    a.edit-link {
      color: #2563eb;
    }
    a.delete-link {
      color: #dc2626;
    }
  </style>
</head>
<body>

<header class="admin-header">
  <div>Привет, администратор</div>
  <a href="index.php">← Вернуться на главную</a>
</header>

<main>

<?php
// === 2. Если НЕ вошёл — показываем форму входа ===
if (empty($_SESSION['is_admin'])) {
    ?>

    <h2>Вход в админ-панель</h2>

    <?php if (!empty($error)): ?>
        <p style="color: red"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Пароль администратора:
            <input type="password" name="admin_password" required>
        </label>
        <button type="submit">Войти</button>
    </form>

    </main>
    <footer class="admin-footer">
      <p>&copy; <?= date('Y') ?> RealEstate Agency</p>
    </footer>
    </body>
    </html>
    <?php
    exit;
}

// === 3. Вошли — показываем таблицы ===

$tables = ['users', 'properties', 'bookings', 'messages', 'callbacks'];

foreach ($tables as $t) {
    echo "<h2>$t</h2><a class='edit-link' href='edit.php?table=$t'>➕ Add new</a><br><br>";

    $rows = $pdo->query("SELECT * FROM $t")->fetchAll(PDO::FETCH_ASSOC);
    if (!$rows) { echo 'Пусто<hr>'; continue; }

    echo "<table><tr>";
    foreach (array_keys($rows[0]) as $col) echo "<th>$col</th>";
    echo "<th>✎</th><th>🗑</th></tr>";

    foreach ($rows as $r) {
        echo "<tr>";
        foreach ($r as $cell) echo '<td>' . htmlspecialchars($cell ?? '') . '</td>';
        // Допустим, у всех таблиц есть поле id
        $id = $r['id'] ?? $r[array_key_first($r)];
        echo "<td><a class='edit-link' href='edit.php?table=$t&id=$id'>✎</a></td>";
        echo "<td><a class='delete-link' href='delete.php?table=$t&id=$id' onclick=\"return confirm('Удалить?');\">🗑</a></td>";
        echo "</tr>";
    }
    echo "</table><hr>";
}

// === 4. Представления (VIEWs) ===
echo "<h1>Представления</h1>";
$views = $pdo->query("SHOW FULL TABLES WHERE TABLE_TYPE='VIEW'")
             ->fetchAll(PDO::FETCH_COLUMN);

foreach ($views as $v) {
    echo "<h3>$v</h3>";
    $rows = $pdo->query("SELECT * FROM $v LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
    if (!$rows) { echo 'Пусто<hr>'; continue; }

    echo "<table><tr>";
    foreach (array_keys($rows[0]) as $col) echo "<th>$col</th>";
    echo "</tr>";
    foreach ($rows as $r) {
        echo '<tr>';
        foreach ($r as $cell) echo '<td>'.htmlspecialchars($cell).'</td>';
        echo '</tr>';
    }
    echo "</table><hr>";
}
?>

</main>

<footer class="admin-footer">
  <p>&copy; <?= date('Y') ?> RealEstate Agency</p>
</footer>

</body>
</html>

