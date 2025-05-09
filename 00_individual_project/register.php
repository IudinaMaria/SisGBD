<?php
require_once __DIR__ . '/db/db.php';

$pdo = getPDO();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    if ($login === '' || $password === '') {
        $error = 'Все поля обязательны.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = ?");
        $stmt->execute([$login]);
        if ($stmt->fetch()) {
            $error = 'Пользователь с таким логином уже существует.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (login, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$login, $hash, $role]);
            header('Location: login.php');
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
<div class="card p-4 shadow" style="max-width:400px; width:100%;">
  <h4 class="mb-3">Регистрация</h4>
  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
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
    <div class="mb-3">
      <label class="form-label">Роль</label>
      <select name="role" class="form-control">
        <option value="user">Пользователь</option>
        <option value="admin">Администратор</option>
      </select>
    </div>
    <button class="btn btn-success w-100">Зарегистрироваться</button>
  </form>
  <p class="mt-3"><a href="login.php">Уже есть аккаунт? Войти</a></p>
</div>
</body>
</html>
