<?php
// edit.php?table=properties&id=3  или  edit.php?table=properties  (новая запись)
require 'config.php';
include 'header.php'; // ✅ ДОБАВЛЕНО
$pdo = getPDO();

/* === 1. Проверяем параметры === */
$table = $_GET['table'] ?? '';
$id    = $_GET['id']    ?? null;
if ($table === '') {
    die('Не передано имя таблицы ?table=');
}

/* (по желанию) белый список
$allowed = ['users','properties','bookings','messages','callbacks'];
if (!in_array($table, $allowed, true)) die('Недопустимая таблица');
*/

/* === 2. Читаем структуру таблицы === */
try {
    $desc = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Таблица не найдена: '.htmlspecialchars($table));
}
$cols = array_column($desc, 'Field');
$pk   = array_values(array_filter($desc, fn($d)=>$d['Key']==='PRI'))[0]['Field'];

/* === 3. Обработка POST (Add / Update) === */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [];
    $values = [];

    foreach ($desc as $col) {
        $c = $col['Field'];
        $type = strtolower($col['Type']);

        if ($c === $pk && !$id) continue; // PK при insert пропускаем

        $val = $_POST[$c] ?? null;

        // Обработка пустых значений для дат/времени
        if ((str_contains($type, 'date') || str_contains($type, 'time')) && $val === '') {
            $val = null;
        }

        // Преобразуем дату "ДД.ММ.ГГГГ" в "ГГГГ-ММ-ДД"
        if (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $val)) {
            [$d, $m, $y] = explode('.', $val);
            $val = "$y-$m-$d";
        }

        // Преобразуем datetime вида "ДД.MM.ГГГГ ЧЧ:ММ" в "ГГГГ-ММ-ДД ЧЧ:ММ:СС"
        if (preg_match('/^(\d{2})\.(\d{2})\.(\d{4}) (\d{2}):(\d{2})$/', $val, $m)) {
            $val = "$m[3]-$m[2]-$m[1] $m[4]:$m[5]:00";
        }

        $fields[] = "`$c`=?";
        $values[] = $val;
    }

    if ($id) { // UPDATE
        $values[] = $id;
        $sql = "UPDATE `$table` SET ".implode(', ', $fields)." WHERE `$pk`=?";
    } else {   // INSERT
        $sql = "INSERT INTO `$table` SET ".implode(', ', $fields);
    }

    $pdo->prepare($sql)->execute($values);

    header('Location: admin.php');
    exit;
}

/* === 4. GET: заполняем форму === */
$data = array_fill_keys($cols, '');
if ($id) {
    $st = $pdo->prepare("SELECT * FROM `$table` WHERE `$pk`=?");
    $st->execute([$id]);
    $data = $st->fetch(PDO::FETCH_ASSOC) ?: $data;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title><?= $id ? 'Edit' : 'Add' ?> <?= htmlspecialchars($table) ?></title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    label {display:block; margin-bottom:1rem;}
    input {padding:4px 6px; width: 250px;}
  </style>
</head>
<body>
<h2><?= $id ? 'Edit' : 'Add' ?> <?= htmlspecialchars($table) ?></h2>

<form method="post">
<?php
foreach ($cols as $c) {
    if ($c === $pk && !$id) continue; // при новой записи PK не выводим
    $val = htmlspecialchars($data[$c]);

    // Если название столбца содержит 'date' или 'time' → type=date/datetime-local
    if (stripos($c, 'date') !== false) {
        $type = 'date';

        // Если в данных есть datetime, преобразуем для поля date
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $val)) {
            $val = substr($val, 0, 10);
        }

    } elseif (stripos($c, 'time') !== false) {
        $type = 'datetime-local';

        // Преобразуем формат из MySQL datetime "ГГГГ-ММ-ДД ЧЧ:ММ:СС" в "ГГГГ-ММ-ДДTЧЧ:ММ"
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $val)) {
            $val = str_replace(' ', 'T', substr($val, 0, 16));
        }

    } else {
        $type = 'text';
    }

    echo "<label>$c<br><input type='$type' name='$c' value='$val'></label>";
}
?>
  <button type="submit">Save</button>
  <a href="admin.php">Cancel</a>
</form>
<?php include 'footer.php'; // ✅ ДОБАВЛЕНО ?>
</body>
</html>
