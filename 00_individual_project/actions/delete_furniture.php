<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db/db.php';
require_once __DIR__ . '/../includes/log_action.php';

/**
 * Удаление мебели из базы данных (только для администратора).
 */

if (!isAdmin()) {
    die('Доступ запрещён');
}

$id = (int)($_POST['id'] ?? 0);

if ($id > 0) {
    $pdo = getPDO();

    $stmt = $pdo->prepare("DELETE FROM orders WHERE furniture_id = ?");
    $stmt->execute([$id]);

    $stmt = $pdo->prepare("DELETE FROM furniture WHERE id = ?");
    $stmt->execute([$id]);

    logAction("Удалён товар ID $id вместе с заказами");
}

header('Location: ../index.php');
exit;
