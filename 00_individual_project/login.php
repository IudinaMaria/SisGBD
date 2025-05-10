<?php
require_once __DIR__ . '/db/db.php';

$pdo = getPDO();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $login = trim($_POST['login'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($login === '' || $password === '') {
    $error = 'Введите логин и пароль.';
  } else {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
      $token = bin2hex(random_bytes(32));
      setcookie('auth_token', $token, time() + 86400 * 7, '/');

      $stmt = $pdo->prepare("UPDATE users SET token = ? WHERE id = ?");
      $stmt->execute([$token, $user['id']]);

      header('Location: index.php');
      exit;
    } else {
      $error = 'Неверный логин или пароль.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <title>Вход</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(120deg, #d4edda, #f8f9fa);
    }

    .card {
      background-color: #ffffff;
      border-radius: 1rem;
    }

    .btn-primary {
      background-color: #28a745;
      border-color: #28a745;
    }

    .btn-primary:hover {
      background-color: #218838;
      border-color: #1e7e34;
    }
  </style>
</head>

<body class="d-flex justify-content-center align-items-center vh-100">
  <div class="card p-4 shadow" style="max-width:400px; width:100%;">
    <h4 class="mb-3 text-center">Вход в систему</h4>
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Логин</label>
        <input type="text" name="login" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Пароль</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button class="btn btn-primary w-100">Войти</button>
      <p class="mt-3 text-center">
        Ещё нет аккаунта?
        <a href="register.php" class="btn btn-outline-success btn-sm ms-1">Зарегистрироваться</a>
      </p>
    </form>
  </div>
</body>

</html>