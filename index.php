<?php
// index.php
require 'config.php';
include 'header.php';    // подключаем шапку с навигацией

// если не залогинен — отдаём на вход
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

  <h1>Привет, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
  <p>Добро пожаловать на сайт недвижимости.</p>

<?php
include 'footer.php';    // подключаем футер с закрывающими тегами