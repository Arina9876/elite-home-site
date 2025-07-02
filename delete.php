<?php
require 'config.php';
$pdo = getPDO();

/* --- читаем параметры запроса --- */
$table = $_GET['table'] ?? '';
$id    = $_GET['id']    ?? '';

/* --- базовая проверка --- */
if ($table === '' || $id === '') {
    header('Location: admin.php'); exit;
}

/*  (необязательно) белый список допустимых таблиц
$allowed = ['users','properties','bookings','messages','callbacks'];
if (!in_array($table, $allowed, true)) {
    header('Location: admin.php'); exit;
}
*/

/* --- находим первичный ключ таблицы --- */
$desc = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_ASSOC);
$pk   = array_values(array_filter($desc, fn($d)=>$d['Key']==='PRI'))[0]['Field'];

/* --- удаляем запись --- */
$stmt = $pdo->prepare("DELETE FROM $table WHERE $pk = ?");
$stmt->execute([$id]);

header('Location: admin.php');