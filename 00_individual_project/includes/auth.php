<?php

/**
 * Авторизация пользователя по токену.
 * 
 */

require_once __DIR__ . '/../db/db.php';

$pdo = getPDO();

if (!isset($_COOKIE['auth_token'])) {
    header('Location: /login.php');
    exit;
}

$token = $_COOKIE['auth_token'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    setcookie('auth_token', '', time() - 3600, '/');
    header('Location: /login.php');
    exit;
}

/**
 * Проверяет, является ли текущий пользователь администратором.
 *
 * @return bool
 */

function isAdmin(): bool {
    global $user;
    return isset($user['role']) && $user['role'] === 'admin';
}
