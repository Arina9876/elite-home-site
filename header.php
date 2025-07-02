<?php
// header.php — подключается в начале каждой страницы сразу после require 'config.php'
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>RealEstate</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <header>
    <nav>
      <a href="index.php">Главная</a>
      <?php if (empty($_SESSION['user_id'])): ?>
        <a href="register.php">Регистрация</a>
        <a href="login.php">Вход</a>
      <?php else: ?>
        <span>Привет, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
        <?php if ($_SESSION['role'] === 'agent'): ?>
          <a href="properties_list.php">Мои объекты</a>
        <?php elseif ($_SESSION['role'] === 'client'): ?>
          <a href="bookings_list.php">Мои заявки</a>
        <?php elseif ($_SESSION['role'] === 'admin'): ?>
          <a href="messages_list.php">Сообщения</a>
        <?php endif; ?>
        <a href="contact.php">Обратная связь</a>
        <a href="logout.php">Выход</a>
      <?php endif; ?>
    </nav>
  </header>
  <main>