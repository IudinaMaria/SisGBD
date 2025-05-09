<?php

/**
 * Обработчик регистрации нового пользователя (администратором).
 * 
 * - Получает данные из POST-запроса
 * - Валидирует поля
 * - Хеширует пароль
 * - Добавляет пользователя в таблицу `users`
 * - Логирует добавление
 */

require_once __DIR__ . '/../db/db.php';
require_once __DIR__ . '/../log_action.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$role = trim($_POST['role'] ?? 'user');

if ($name && $email && $password) {
    $pdo = getPDO();

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password, role) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$name, $email, $hashedPassword, $role]);

    logAction("Добавлен пользователь: $name");
}

header("Location: ../pages/users.php");
exit;
