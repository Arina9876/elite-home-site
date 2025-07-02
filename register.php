<?php
session_start();
require 'config.php';          // $pdo = getPDO(); будет ниже
$pdo = getPDO();               // ← создаём соединение один раз

/* === Google reCAPTCHA === */
$RECAP_SECRET = '6LcWF2orAAAAADgBVq_wfUJGVzqpDNTTR7qwWn3V'; // 
/* ======================= */

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ---------- reCAPTCHA ----------
    $token = $_POST['g-recaptcha-response'] ?? '';
    if (!$token) { $errors[] = 'Подтвердите, что вы не робот.'; }
    else {
        $resp = file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?secret=$RECAP_SECRET&response=$token"
        );
        if (!json_decode($resp)->success) $errors[] = 'reCAPTCHA не пройдена.';
    }

    // ---------- данные формы ----------
    $name      = trim($_POST['name']      ?? '');
    $email     = trim($_POST['email']     ?? '');
    $password  =        $_POST['password']  ?? '';
    $password2 =        $_POST['password2'] ?? '';

    if ($name === '')                      $errors[] = 'Введите имя.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Неверный email.';
    if (strlen($password) < 6)             $errors[] = 'Пароль ≥ 6 символов.';
    if ($password !== $password2)          $errors[] = 'Пароли не совпадают.';

    // ---------- если ошибок нет ----------
    if (!$errors) {
        // проверка дубликата email
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email=?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email уже зарегистрирован.';
        } else {
            // вставка
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                'INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)'
            );
            $stmt->execute([$name, $email, $hash]);

            // ставим сессию и ведём в ЛК
            $_SESSION['user_id']   = $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            header('Location: personal.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Регистрация</title>
</head>
<body>
<?php include 'header.php'; ?>

<h1>Регистрация</h1>

<?php if ($errors): ?>
  <ul style="color:red;">
    <?php foreach ($errors as $err): ?>
      <li><?= htmlspecialchars($err) ?></li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<form method="post" action="">
  <label>Имя:<br>
    <input type="text" name="name" value="<?= htmlspecialchars($name ?? '') ?>">
  </label><br><br>

  <label>Email:<br>
    <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>">
  </label><br><br>

  <label>Пароль:<br>
    <input type="password" name="password">
  </label><br><br>

  <label>Повтор пароля:<br>
    <input type="password" name="password2">
  </label><br><br>

  <!-- reCAPTCHA -->
  <div class="g-recaptcha" data-sitekey="6LcWF2orAAAAAOd6simETe6hXa7SEGYpMR7kPcyS"></div>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <br>

  <button type="submit">Зарегистрироваться</button>
</form>

<?php include 'footer.php'; ?>
</body>
</html>