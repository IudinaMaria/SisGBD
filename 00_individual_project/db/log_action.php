<?php
require_once __DIR__ . '/db.php';

/**
 * Сохраняет действие пользователя в таблицу логов.
 *
 * @param string $message
 * 
 */

function logAction(string $message): void {
    $pdo = getPDO();

    $stmt = $pdo->prepare("INSERT INTO logs (message) VALUES (?)");
    $stmt->execute([$message]);
}
