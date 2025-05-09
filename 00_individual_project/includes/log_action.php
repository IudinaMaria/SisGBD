<?php
require_once __DIR__ . '/../db/db.php';

/**
 * Добавляет запись в журнал действий пользователей.
 *
 * @param string $message
 */

function logAction(string $message): void {
    $pdo = getPDO();
    $stmt = $pdo->prepare("INSERT INTO actions_log (action) VALUES (?)");
    $stmt->execute([$message]);
}
