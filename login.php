<?php
session_start();
require 'config.php';
$pdo = getPDO();

ini_set('display_errors', 1);
error_reporting(E_ALL);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password =      $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Неверный email.';
    if (!$password)                                 $errors[] = 'Введите пароль.';

    if (!$errors) {
        $stmt = $pdo->prepare(
          'SELECT id, name, password_hash, role FROM users WHERE email = ?'
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role']      = $user['role'];
            header('Location: personal.php');
            exit;
        } else {
            $errors[] = 'Неправильный email или пароль.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Вход</title>
</head>
<body>
<?php include 'header.php'; ?>

<h1>Вход</h1>

<!-- Клиентские ошибки -->
<ul id="loginErrors" style="color:red;"></ul>

<form id="loginForm" method="post" action="">
  <label>Email:<br>
    <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>">
  </label><br><br>

  <label>Пароль:<br>
    <input type="password" name="password">
  </label><br><br>

  <button type="submit">Войти</button>
</form>

<?php if ($errors): ?>
  <ul style="color:red;">
    <?php foreach ($errors as $e): ?>
      <li><?= htmlspecialchars($e) ?></li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<script>
document.getElementById('loginForm').addEventListener('submit', e=>{
  const errs=[], f=e.target;
  if(!/^\S+@\S+\.\S+$/.test(f.email.value.trim())) errs.push('Неверный email.');
  if(!f.password.value) errs.push('Введите пароль.');
  document.getElementById('loginErrors').innerHTML =
      errs.map(t=>`<li>${t}</li>`).join('');
  if(errs.length) e.preventDefault();
});
</script>

<?php include 'footer.php'; ?>
</body>
</html>