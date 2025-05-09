<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db/db.php'; 

/**
 * Удаление пользователя из базы данных (только для администратора).
 * 
 */

if (!isAdmin()) {
    die("У вас нет прав для удаления записи.");
}

$id = $_GET['id'] ?? null;
if (!$id) {
    die('ID пользователя не передан');
}

$pdo = getPDO();
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

header('Location: ../pages/users.php');
exit;
