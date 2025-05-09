<?php

/**
 * Обработчик добавления новой позиции мебели (только для администратора).
 * 
 */

require_once __DIR__ . '/../db/db.php';

$token = $_COOKIE['auth_token'] ?? null;

if ($token) {
    $pdo = getPDO();

    $stmt = $pdo->prepare("SELECT * FROM users WHERE token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user && $user['role'] === 'admin') {

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $image = trim($_POST['image'] ?? '');

        if ($name && $price > 0) {
            $stmt = $pdo->prepare("
                INSERT INTO furniture (name, description, price, image) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$name, $description, $price, $image]);

            header("Location: ../index.php");
            exit;
        } else {
            echo "Ошибка: Не все поля заполнены корректно.";
        }

    } else {
        header("Location: ../login.php");
        exit;
    }

} else {
    header("Location: ../login.php");
    exit;
}
